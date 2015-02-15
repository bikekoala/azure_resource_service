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
     * 开火！
     *
     * @return void
     * @throws Exception
     */
    abstract public function fire();

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
            $this->fire();
        } catch (\Exception $e) {
            static::outputJson(false, $e->getMessage());
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
     * @return void
     * @throws Exception
     */
    public function checkCommonParams()
    {
        \Rule\Field\SubscriptionId::validate(
            $this->data['subscription_id'],
            'subscription_id'
        );
        \Rule\Atom\Url::validate($this->data['callback_url'], 'callback_url');
        \Rule\Atom\Arr::validate($this->data['resources'], 'resources');

        $this->subId       = $this->data['subscription_id'];
        $this->callbackUrl = $this->data['callback_url'];
        $this->resources   = $this->data['resources'];
    }

    /**
     * 初始化异步操作
     *
     * @return int
     */
    public function initAsyncOperation()
    {
        $curClass = get_called_class();
        $serviceName = substr($curClass, strrpos($curClass, '\\') + 1);
        $baseServiceClass = '\\Service\\' . $serviceName . '\\Base';

        try {
            $pdo = BaseModel::single()->pdo;
            $pdo->beginTransaction();

            $opId = ResOp::single()->addData(
                $this->subId,
                $this->callbackUrl,
                $serviceName,
                serialize($this->resources)
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
