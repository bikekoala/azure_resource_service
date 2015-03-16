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
        Rule\Atom\Guid::validate($subId, '$subId');
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
}
