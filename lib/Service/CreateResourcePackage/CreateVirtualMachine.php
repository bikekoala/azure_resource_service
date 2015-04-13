<?PHP    
namespace Service\CreateResourcePackage;

use Model\BaseModel;
use Model\ResVmSize;
use Model\ResVmImage;
use Model\ResItemSa;
use Model\ResItemVn;
use Model\ResItemCs;
use Model\ResItemVmd;
use Model\ResItemVmdRole;
use Service\CreateResourcePackage\CreateVirtualNetwork;
use WindowsAzure\ServiceManagement\Models\AddRoleOptions;
use WindowsAzure\ServiceManagement\Models\CreateDeploymentByRolesOptions;

/**
 * 创建虚拟机
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-27
 */
class CreateVirtualMachine extends Base
{
    /**
     * VHD磁盘文件地址
     */
    const MEDIA_LINK = 'https://%s.blob.core.chinacloudapi.cn/vhds/%s.vhd';

    /**
     * 操作系统名称
     */
    const OS_NAME_WINDOWS = 'Windows';
    const OS_NAME_LINUX   = 'Linux';

    /**
     * 扩展数据
     *
     * @var array
     */
    private $extData;

    /**
     * 角色数据
     *
     * @var array
     */
    private $roleData;

    /**
     * 请求ID
     *
     * @var string
     */
    private $requestId;

    /**
     * 执行
     *
     * @return void
     */
    public function run()
    {
        $this->initExtData();
        $this->initRoleData();
        $hasDeployment = $this->checkIfDeploymentNameExists();

        if ( ! $this->checkIfRoleNameExists()) {
            $this->createVm($hasDeployment);

            $this->getAzureOperationStatus($this->requestId);
        }

        $this->saveDatas($hasDeployment);
    }

    /**
     * 线上检查虚拟机角色名称是否已经存在
     *
     * @return bool
     */
    private function checkIfRoleNameExists()
    {
        try {
            $this->serviceManagement->getRole(
                $this->extData['cs_name'],
                $this->extData['vmd_name'],
                $this->data['host_name']
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 检查虚拟机部署名称是否已经存在
     *
     * @return bool
     */
    private function checkIfDeploymentNameExists()
    {
        return (bool) $this->extData['vmd_id'];
    }

    /**
     * 创建虚拟机
     *
     * @param bool $hasDeployment
     * @return void
     */
    private function createVm($hasDeployment)
    {
        if ($hasDeployment) {
            $this->addRole();
        } else {
            $this->addRoleByDeployment();
        }
    }

    /**
     * 像虚拟机部署中添加角色
     *
     * @return void
     */
    private function addRole()
    {
        $roleOptions = $this->getRoleOptions();
        $result = $this->callAzureService(
            'addRole',
            $this->extData['cs_name'],
            $this->extData['vmd_name'],
            $roleOptions
        );
        $this->requestId = $result['x-ms-request-id'];
    }

    /**
     * 创建虚拟机部署同事添加角色
     *
     * @return void
     */
    private function addRoleByDeployment()
    {
        $roleOptionsList[] = $this->getRoleOptions();
        $deploymentOptions = new CreateDeploymentByRolesOptions();
        $deploymentOptions->setName($this->extData['vmd_name']);
        $deploymentOptions->setLabel($this->extData['vmd_label']);
        $deploymentOptions->setVirtualNetworkName($this->extData['vn_name']);
        $result = $this->callAzureService(
            'createDeploymentByRoles',
            $this->extData['cs_name'],
            $roleOptionsList,
            $deploymentOptions
        );
        $this->requestId = $result['x-ms-request-id'];
    }

    /**
     * 获取角色配置选项
     *
     * @return AddRoleOptions $role
     */
    private function getRoleOptions()
    {
        $role = new AddRoleOptions();
        $role->setRoleName($this->roleData['role_name']);
        $role->setRoleSize($this->roleData['role_size']);
        $role->setProvisionGuestAgent($role::STATUS_TRUE);

        $role->setOsvhdSourceImageName($this->roleData['os_source_name']);
        $role->setOsvhdMediaLink($this->roleData['os_media_link']);

        $role->setDvhdMediaLink($this->roleData['data_media_link']);
        $role->setDvhdDiskLabel($this->roleData['data_disk_label']);
        $role->setDvhdLogicalDiskSizeInGB($this->roleData['data_disk_size']);

        if (self::OS_NAME_LINUX === $this->roleData['os_name']) {
            $role->setCsConfigurationSetType($role::CS_TYPE_LINUX_PROVISIONING);
            $role->setCsLinuxHostName($this->roleData['host_name']);
            $role->setCsLinuxUserName($this->roleData['user_name']);
            $role->setCsLinuxUserPassword($this->roleData['user_password']);
        } else if (self::OS_NAME_WINDOWS === $this->roleData['os_name']) {
            $role->setCsConfigurationSetType($role::CS_TYPE_WINDOWS_PROVISIONING);
            $role->setCsWindowsComputerName($this->roleData['host_name']);
            $role->setCsWindowsAdminUsername($this->roleData['user_name']);
            $role->setCsWindowsAdminPassword($this->roleData['user_password']);
        }

        if ( ! empty($this->roleData['internal_ip'])) {
            $role->setCsSubnetNames(array(CreateVirtualNetwork::SUBNET_NAME));
            $role->setCsStaticVirtualNetworkIPAddress($this->roleData['internal_ip']);
        }

        if ( ! empty($this->roleData['ports'])) {
            $endpointList = array();
            foreach ($this->roleData['ports'] as $i => $port) {
                $endpointList[$i] = new AddRoleOptions();
                $endpointList[$i]->setCsNetworkEndpointLocalPort($port['local_port']);
                $endpointList[$i]->setCsNetworkEndpointName($port['name']);
                $endpointList[$i]->setCsNetworkEndpointProtocol($port['protocol']);
                if (isset($port['port'])) {
                    $endpointList[$i]->setCsNetworkEndpointPort($port['port']);
                }
            }
            $role->setCsNetworkEndpointList($endpointList);
        }

        return $role;
    }

    /**
     * 保存数据
     *
     * @param bool $hasDeployment
     * @return void
     */
    private function saveDatas($hasDeployment)
    {
        // check if exists
        $data = ResItemVmdRole::single()->getDataByHostNameAndVmdId(
            $this->data['host_name'],
            $this->extData['vmd_id']
        );
        if ( ! empty($data)) return;

        // save datas
        try {
            $pdo = BaseModel::single()->pdo;
            $pdo->beginTransaction();

            // 保存角色或部署角色数据
            if ($hasDeployment) {
                $this->saveRoleData();
            } else {
                $this->saveRoleDataByDeployment();
            }
            // 将存储账户磁盘数加2（系统盘与数据盘）
            ResItemSa::single()->updateDiskCount($this->extData['sa_id'], 2);

            $pdo->commit();
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * 保存角色数据
     *
     * @param int $vmdId
     * @return void
     */
    private function saveRoleData($vmdId = 0)
    {
        $vmdId = $vmdId ? : $this->extData['vmd_id'];
        ResItemVmdRole::single()->addData(
            $vmdId,
            $this->data['size_id'],
            $this->data['image_id'],
            $this->roleData['os_media_link'],
            $this->roleData['data_media_link'],
            $this->roleData['data_disk_size'],
            $this->roleData['data_disk_label'],
            $this->roleData['internal_ip'],
            $this->roleData['host_name'],
            $this->roleData['user_name'],
            $this->roleData['user_password'],
            $this->requestId
        );
    }

    /**
     * 保存角色数据和部署数据
     *
     * @return void
     */
    private function saveRoleDataByDeployment()
    {
        $vmdId = ResItemVmd::single()->addData(
            $this->itemId,
            $this->extData['sa_id'],
            $this->extData['vn_id'],
            $this->extData['cs_id'],
            $this->extData['vmd_name'],
            $this->extData['vmd_label']
        );

        $this->saveRoleData($vmdId);
    }

    /**
     * 初始化扩展数据
     *
     * @return array
     */
    private function initExtData()
    {
        $saData = ResItemSa::single()->getAvailableData($this->data['location'], $this->subId);
        $vnData = ResItemVn::single()->getDataByLocationAndSubId($this->data['location'], $this->subId);
        $csId = ResItemCs::single()->getIdByName($this->data['cloud_service_name']);
        $vmdId = ResItemVmd::single()->getIdByNameAndCsId($this->data['cloud_service_name'], $csId);

        $this->extData = array(
            'sa_id'    => $saData['id'],
            'sa_name'  => $saData['name'],
            'vn_id'    => $vnData['id'],
            'vn_name'  => $vnData['name'],
            'cs_id'    => $csId,
            'cs_name'  => $this->data['cloud_service_name'],
            'vmd_id'   => $vmdId,
            'vmd_name' => $this->data['cloud_service_name'],
            'vmd_label'=> $this->subId
        );
    }

    /**
     * 初始化角色数据
     *
     * @return void
     */
    private function initRoleData()
    {
        $vmSize = ResVmSize::single()->getName($this->data['size_id']);
        $imageData = ResVmImage::single()->getData($this->data['image_id']);

        $this->roleData = array(
            'role_name'       => $this->data['host_name'],
            'role_size'       => $vmSize,
            'os_name'         => $imageData['os_name'],
            'os_source_name'  => $imageData['source_name'],
            'os_media_link'   => sprintf(
                self::MEDIA_LINK,
                $this->extData['sa_name'],
                $this->data['host_name'] . '-os'
            ),
            'data_media_link' => sprintf(
                self::MEDIA_LINK,
                $this->extData['sa_name'],
                $this->data['host_name'] . '-data'
            ),
            'data_disk_label' => base64_encode($this->subId),
            'data_disk_size'  => $this->data['data_disk_capacity'],
            'internal_ip'     => isset($this->data['internal_ip']) ?
                                    $this->data['internal_ip'] : '',
            'host_name'       => $this->data['host_name'],
            'user_name'       => $this->data['user_name'],
            'user_password'   => $this->data['user_password'],
            'ports'           => isset($this->data['ports']) ?
                                    $this->data['ports'] : array(),
        );
    }
}
