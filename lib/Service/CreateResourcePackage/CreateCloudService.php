<?PHP    
namespace Service\CreateResourcePackage;

use Model\ResItemCs;
use WindowsAzure\ServiceManagement\Models\CreateHostedServiceOptions;

/**
 * 创建云服务
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-22
 */
class CreateCloudService extends Base
{
    /**
     * 扩展数据
     *
     * @var array
     */
    private $extData;

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

        if ( ! $this->checkIfNameExistsOnline()) {
            $this->createCs();

            $this->getAzureOperationStatus($this->requestId);
        }

        $this->saveData();
    }

    /**
     * 在线检查云服务是否已经存在
     *
     * @return bool
     */
    private function checkIfNameExistsOnline()
    {
        $result = $this->serviceManagement->checkHostedServicesName(
            $this->data['cloud_service_name']
        );
        return 'false' === $result->Result;
    }

    /**
     * 发送创建请求
     *
     * @return void
     */
    private function createCs()
    {
        $options = new CreateHostedServiceOptions();
        $options->setLocation($this->extData['location']);
        $result = $this->callAzureService(
            'createHostedService',
            $this->extData['name'],
            $this->extData['label'],
            $options
        );
        $this->requestId = $result['x-ms-request-id'];
    }

    /**
     * 初始化扩展数据
     *
     * @return array
     */
    private function initExtData()
    {
        $name = $this->data['cloud_service_name'];
        $label = base64_encode($this->subId);
        $location = $this->data['location'];
        $this->extData = compact('name', 'label', 'location');
    }

    /**
     * 保存数据
     *
     * @return void
     */
    private function saveData()
    {
        $data = ResItemCs::single()->getDataByName($this->extData['name']);
        if (empty($data)) {
            ResItemCs::single()->addData(
                $this->itemId,
                $this->extData['name'],
                $this->extData['label'],
                $this->extData['location'],
                $this->requestId
            );
        }
    }
}
