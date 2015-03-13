<?PHP    
namespace Service\CreateResourcePackage;

use Model\ResItemSa;
use WindowsAzure\ServiceManagement\Models\CreateStorageOptions;

/**
 * 创建存储账户
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-22
 */
class CreateStorageAccount extends Base
{
    /**
     * 存储名称前缀
     */
    const NAME_PREFIX = 'ucw';

    /**
     * 扩展数据
     *
     * @var array
     */
    private $extData;

    /**
     * SA表ID
     *
     * @var int
     */
    private $saId;

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
        if ( ! $this->checkIfNameAvailable()) {
            $this->initExtData();

            $this->saveData();

            $this->createSa();

            $this->getAzureOperationStatus($this->requestId);

            $this->updateData();
        }
    }

    /**
     * 检查存储账户名称是否可用
     *
     * @return bool
     */
    private function checkIfNameAvailable()
    {
        // 获取一个可用的存储账户记录
        $availabelData = ResItemSa::single()->getAvailableData($this->data['location'], $this->subId);

        // 检查
        if ( ! empty($availabelData)) {
            // 持续检查记录创建状态，直至状态为成功
            for ($i=10; $i>0; $i--) {
                $createStatus = ResItemSa::single()->getCreateStatusById($availabelData['id']);
                if (ResItemSa::STATUS_CREATING == $createStatus) {
                    sleep(5);
                } else break;
            }

            // 线上检查名称是否存在
            $result = $this->serviceManagement->checkStorageAccountName($availabelData['name']);
            return 'false' === $result->Result;
        } else return false;
    }

    /**
     * 发送创建请求
     *
     * @return void
     */
    private function createSa()
    {
        $options = new CreateStorageOptions();
        $options->setLocation($this->extData['location']);
        $result = $this->callAzureService(
            'createStorageAccount',
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
        $name = self::getName();
        $label = base64_encode($this->subId);
        $location = $this->data['location'];
        $this->extData = compact('name', 'label', 'location');
    }

    /**
     * 保存数据
     *
     * @return int
     */
    private function saveData()
    {
        $this->saId = ResItemSa::single()->addData(
            $this->itemId,
            $this->subId,
            $this->extData['name'],
            $this->extData['label'],
            $this->extData['location'],
            0,
            '',
            ResItemSa::STATUS_CREATING
        );
    }

    /**
     * 更新请求结果数据
     *
     * @return void
     */
    private function updateData()
    {
        ResItemSa::single()->updateDataById(
            $this->saId,
            $this->requestId,
            ResItemSa::STATUS_CREATED
        );
    }

    /**
     * 获取存储账号名称
     * 3 - 24 位长度，小写和数字
     *
     * @return string
     */
    private static function getName()
    {
        return self::NAME_PREFIX . date('YmdHis') . rand_code(7, 2);
    }
}
