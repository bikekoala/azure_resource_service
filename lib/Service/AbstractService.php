<?PHP    
namespace Service;

use Model\BaseModel;
use Model\Subscription;
use Service\AzureService;

/**
 * 服务抽象类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-18
 */
abstract class AbstractService
{
    /**
     * Azure管理服务对象(GIRL)
     *
     * @var object
     */
    public $serviceManagement;

    /**
     * 条目表ID
     *
     * @var int
     */
    public $itemId;

    /**
     * 订阅ID
     *
     * @var string
     */
    public $subId;

    /**
     * 服务数据
     *
     * @var array
     */
    public $data;

    /**
     * 执行
     *
     * @return void
     */
    abstract public function run();

    /**
     * 设置条目表ID
     *
     * @param int $itemId
     * @return void
     * @throws Exception
     */
    public function setItemId($itemId)
    {
        $this->itemId = (int) $itemId;
        if (0 === $this->itemId) {
            throw new \Exception('Invalid item id !');
        }
    }

    /**
     * 设置订阅ID
     *
     * @param string $subId
     * @return void
     * @throws Exception
     */
    public function setSubId($subId)
    {
        // validate
        \Rule\Atom\Guid::validate($subId, '$subId');
        $this->subId = $subId;

        // initialization azure management service
        $cert = Subscription::single()->getCertByGuid($this->subId);
        if ( ! $cert) {
            throw new \Exception('Invalid subscription certificates !');
        }
        $this->serviceManagement = AzureService::initAzureService(
            $this->subId,
            $cert
        );
    }

    /**
     * 设置服务数据
     *
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function setData($data)
    {
        $this->data = unserialize($data);
        if (false === $this->data) {
            throw new \Exception('Invalid api data !');
        }
    }

    /**
     * 获取下一个阶段名称
     *
     * @param string $curPhaseName
     * @return string
     */
    public static function getNextPhaseName($curPhaseName)
    {
        $i = array_flip(static::$processFlow)[$curPhaseName] + 1;
        return isset(static::$processFlow[$i]) ? static::$processFlow[$i] : '';
    }

    /**
     * Azure服务调用封装方法
     * 针对操作性接口，必须创建、更新、删除、操作类，而非查询类接口
     *
     * @param string $serviceName
     * @param array ...$params
     * @return array
     */
    public function callAzureService($serviceName, ...$params)
    {
        while (true) {
            try {
                $result = $this->serviceManagement->$serviceName(...$params);
                return obj2arr($result);
            } catch (\Exception $e) {
                // ConflictError, 发生了冲突，使操作未能完成，等待执行。
                if (409 === $e->getCode()) {
                    sleep(5);
                    continue;
                } else throw $e;
            }
        }
    }

    /**
     * 获取Azure API异步操作状态
     *
     * @param string $requestId
     * @return void
     */
    public function getAzureOperationStatus($requestId)
    {
        AzureService::getOperationStatusUntilTheEnd(
            $this->serviceManagement,
            $requestId
        );
    }

    /**
     * 实时获取虚拟机资源信息
     *
     * @param array $itemDatas
     * @return array
     */
    public function getAzureVmResource($itemDatas)
    {
        // 统计云服务名称与主机名称的对应关系
        $names = array();
        foreach ($itemDatas as $data) {
            $names[$data['cloud_service_name']][] = $data['host_name'];
        }

        // 根据云服务名称获取部署信息
        $res = array();
        foreach ($names as $cloudServiceName => $hostNames) {
            try {
                $deployment = $this->serviceManagement->getDeployment(
                    $cloudServiceName
                );
            } catch (\Exception $e) {
                return array();
            }

            foreach ($hostNames as $hostName) {
                $roleInstance = isset($deployment->RoleInstanceList->RoleInstance->RoleName) ?
                    array($deployment->RoleInstanceList->RoleInstance) :
                    $deployment->RoleInstanceList->RoleInstance;
                foreach ($roleInstance as $role) {
                    if ($hostName != $role->RoleName) continue;
                    $res[$cloudServiceName][$hostName] = array(
                        'cloud_service_name' => $cloudServiceName,
                        'host_name'          => $role->RoleName,
                        'host_status'        => $role->InstanceStatus,
                        'power_state'        => $role->PowerState
                    );
                }

                $roles = isset($deployment->RoleList->Role->RoleName) ?
                    array($deployment->RoleList->Role) :
                    $deployment->RoleList->Role;
                foreach ($roles as $role) {
                    if ($hostName != $role->RoleName) continue;

                    $item = array();
                    $cs = $role->ConfigurationSets->ConfigurationSet;
                    if ('NetworkConfiguration' === $cs->ConfigurationSetType) {
                        $ports = array();
                        if (isset($cs->InputEndpoints)) {
                            $endpoint = isset($cs->InputEndpoints->InputEndpoint->Name) ?
                                array($cs->InputEndpoints->InputEndpoint) :
                                $cs->InputEndpoints->InputEndpoint;
                            foreach ($endpoint as $i => $p) {
                                $ports[$i] = array(
                                    'name'       => $p->Name,
                                    'protocol'   => $p->Protocol,
                                    'port'       => $p->Port,
                                    'local_port' => $p->LocalPort
                                );
                            }
                        }
                        $item['ports'] = $ports;
                    }
                    $item['internal_ip'] = $cs->StaticVirtualNetworkIPAddress;

                    if (isset($role->DataVirtualHardDisks->DataVirtualHardDisk)) {
                        $dataDisk = $role->DataVirtualHardDisks->DataVirtualHardDisk;
                        $item['data_disk_name'] = $dataDisk->DiskName;
                        $item['data_disk_capacity'] = $dataDisk->LogicalDiskSizeInGB;
                        $item['data_disk_media_link'] = $dataDisk->MediaLink;
                    }
                    $osDisk = $role->OSVirtualHardDisk;
                    $item['os_disk_name'] = $osDisk->DiskName;
                    $item['os_disk_media_link'] = $osDisk->MediaLink;
                    $item['os_name'] = $osDisk->OS;
                    $item['os_source_image_name'] = $osDisk->SourceImageName;

                    $item['size_name'] = $role->RoleSize;

                    $res[$cloudServiceName][$role->RoleName] = array_merge(
                        $res[$cloudServiceName][$hostName],
                        $item
                    );
                }
            }
        }
        return $res;
    }
}
