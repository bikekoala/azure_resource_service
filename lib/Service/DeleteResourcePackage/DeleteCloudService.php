<?PHP
namespace Service\DeleteResourcePackage;

use Model\ResItemCs;
use WindowsAzure\Common\ServiceException as AzureException;

/**
 * 删除云服务
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-04-09
 */
class DeleteCloudService extends Base
{
    /**
     * 执行
     *
     * @return void
     */
    public function run()
    {
        $this->deleteCloudService();

        $this->updateStatus();
    }

    /**
     * 请求删除云服务接口
     *
     * @return void
     */
    private function deleteCloudService()
    {
        try {
            $result = $this->callAzureService(
                'deleteHostedService',
                $this->data['cloud_service_name']
            );
            $this->getAzureOperationStatus($result['x-ms-request-id']);
        } catch (AzureException $e) {
        }
    }

    /**
     * 更新删除状态
     *
     * @return void
     */
    private function updateStatus()
    {
        // check if exists
        $csId = ResItemCs::single()->getIdByName($this->data['cloud_service_name']);
        if ( ! $csId) return;

        // update delete status
        ResItemCs::single()->updateDeleteStatusById(
            $csId,
            ResItemCs::STATUS_DELETE_TRUE
        );
    }
}
