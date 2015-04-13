<?PHP
namespace Service\DeleteResourcePackage;

use Model\ResItemCs;
use Model\ResItemVmd;
use Model\ResItemVmdRole;
use WindowsAzure\ServiceManagement\Models\DeleteDeploymentOptions;
use WindowsAzure\Common\ServiceException as AzureException;

/**
 * 删除虚拟机
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-04-09
 */
class DeleteVirtualMachine extends Base
{
    /**
     * 执行
     *
     * @return void
     */
    public function run()
    {
        $roleList = $this->getDeploymentRoleList();
        if ($this->isExistRole($roleList)) {
            if ($this->isLastRole($roleList)) {
                $requestId = $this->deleteDeployment();
            } else {
                $requestId = $this->deleteRole();
            }
            $this->getAzureOperationStatus($requestId);
        }

        $this->updateStatus();
    }

    /**
     * 删除部署
     *
     * @return string
     */
    private function deleteDeployment()
    {
        $serviceOptions = new DeleteDeploymentOptions();
        $serviceOptions->setDeploymentName($this->data['cloud_service_name']);
        $result = $this->callAzureService(
            'deleteDeployment',
            $this->data['cloud_service_name'],
            true,
            $serviceOptions
        );

        return $result['x-ms-request-id'];
    }

    /**
     * 删除角色
     *
     * @return string
     */
    private function deleteRole()
    {
        $result = $this->callAzureService(
            'deleteRole',
            $this->data['cloud_service_name'],
            $this->data['cloud_service_name'],
            $this->data['host_name'],
            true
        );

        return $result['x-ms-request-id'];
    }

    /**
     * 判断指定名称机器是否是部署中的最后一台
     *
     * @param array $roleList
     * @return bool
     */
    private function isLastRole($roleList)
    {
        return 1 === count($roleList);
    }

    /**
     * 判断当前机器是否存在
     *
     * @param array $roleList
     * @return bool
     */
    private function isExistRole($roleList)
    {
        foreach ($roleList as $role) {
            if ($this->data['host_name'] == $role['RoleName']) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取部署内机器列表
     *
     * @return array
     */
    private function getDeploymentRoleList()
    {
        try {
            $deployment = $this->callAzureService(
                'getDeployment',
                $this->data['cloud_service_name']
            );
            return isset($deployment['RoleList']['Role']['RoleName']) ?
                array($deployment['RoleList']['Role']) :
                $deployment['RoleList']['Role'];

        } catch (AzureException $e) {
            return array();
        }
    }

    /**
     * 将记录设置成已删除状态
     *
     * @return void
     */
    private function updateStatus()
    {
        // check if exists
        $csId = ResItemCs::single()->getIdByName($this->data['cloud_service_name']);
        $vmdId = ResItemVmd::single()->getIdByNameAndCsId($this->data['cloud_service_name'], $csId);
        $data = ResItemVmdRole::single()->getDataByHostNameAndVmdId(
            $this->data['host_name'],
            $vmdId
        );
        if (empty($data)) return;

        // update delete status
        ResItemVmdRole::single()->updateDeleteStatusById(
            $data['id'],
            ResItemVmdRole::STATUS_DELETE_TRUE
        );
    }
}
