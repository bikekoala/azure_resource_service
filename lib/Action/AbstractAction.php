<?PHP
namespace Action;

use Model\BaseModel;
use Model\ResOp;
use Model\ResItem;

/**
 * Action抽象类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-16
 */
abstract class AbstractAction
{
    /**
     * 请求数据
     *
     * @var array
     */
    public $data;

    /**
     * 订阅ID
     *
     * @var string
     */
    public $subId;

    /**
     * 回调URL
     *
     * @var string
     */
    public $callbackUrl;

    /**
     * 资源数据
     *
     * @var array
     */
    public $resources;

    /**
     * 检查自定义参数
     *
     * @return void
     * @throws Exception
     */
    abstract public function checkCustomParams();

    /**
     * 奔跑吧，兄弟！
     *
     * @return void
     */
    public function run()
    {
        try {
            $this->checkHeader();
            $this->checkCommonParams();
            $this->checkCustomParams();

            $opId = $this->initAsyncOperation();
            static::outputJson(true, 'Accepd', $opId);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $opId = $this->saveFailedOperation($message);
            static::outputJson(false, $message, $opId);
        }
    }

    /**
     * 检查请求头部数据
     *
     * @return void
     */
    public function checkHeader()
    {
        if ('application/json' !== strtolower($_SERVER['HTTP_CONTENT_TYPE'])) {
            self::outputJson(false, '该请求的头部数据没有通过验证！');
        }

        $this->data = json_decode(
            rawurldecode(file_get_contents('php://input')),
            true
        );
    }

    /**
     * 检查共同参数
     *
     * @param bool $isAsync
     * @return void
     * @throws Exception
     */
    public function checkCommonParams($isAsync = true)
    {
        \Rule\Field\SubscriptionId::validate($this->data['subscription_id'], 'subscription_id');
        \Rule\Atom\Arr::validate($this->data['resources'], 'resources');
        $this->subId     = $this->data['subscription_id'];
        $this->resources = $this->data['resources'];

        if ($isAsync) {
            \Rule\Atom\Url::validate($this->data['callback_url'], 'callback_url');
            $this->callbackUrl = $this->data['callback_url'];
        }
    }

    /**
     * 初始化异步操作
     *
     * @return int
     */
    public function initAsyncOperation()
    {
        $serviceName = $this->getCurApiName();
        $baseServiceClass = '\\Service\\' . $serviceName . '\\Base';

        try {
            $pdo = BaseModel::single()->pdo;
            $pdo->beginTransaction();

            $opId = ResOp::single()->addData(
                $this->subId,
                $this->callbackUrl,
                $serviceName,
                serialize($this->resources),
                ResOp::CB_STATUS_DEFAULT,
                ResOp::STATUS_ACCEPT
            );
            foreach ($this->resources as $data) {
                ResItem::single()->addData(
                    $opId,
                    serialize($data),
                    $serviceName,
                    current($baseServiceClass::$processFlow)
                );
            }

            $pdo->commit();
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }

        return $opId;
    }

    /**
     * 保存失败的操作
     *
     * @param string $message
     * @return int
     */
    public function saveFailedOperation($message)
    {
        try {
            $opId = ResOp::single()->addData(
                $this->subId,
                $this->callbackUrl,
                $this->getCurApiName(),
                serialize($this->resources),
                ResOp::CB_STATUS_DEFAULT,
                ResOp::STATUS_FAIL,
                $message
            );
        } catch (\Exception $e) {
        }

        return $opId;
    }

    /**
     * 获取当前API的名称
     *
     * @return string
     */
    private function getCurApiName()
    {
        $curClass = get_called_class();
        return substr($curClass, strrpos($curClass, '\\') + 1);
    }

    /**
     * 输出JSON字符串
     *
     * @param bool $status
     * @param string $info
     * @param mixed $data
     * @return void
     */
    public static function outputJson($status, $info, $data = null)
    {
        echo json_encode(compact(
            'status',
            'info',
            'data'
        ), JSON_UNESCAPED_UNICODE);
        exit;
    }
}
