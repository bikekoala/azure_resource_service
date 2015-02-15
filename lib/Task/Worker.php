<?PHP
namespace Task;

use Model\ResOp;
use Model\ResItem;
use WindowsAzure\Common\ServiceException as AzureException;

/**
 * 任务执行类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-20
 */
class Worker extends \Thread
{
    /**
     * 条目数据
     *
     * @var array
     */
    private $itemData;

    /**
     * 构造方法
     *
     * @param array $itemData
     * @return void
     */
    public function __construct($itemData)
    {
        // kill thread if the data is empty
        $this->itemData = $itemData;
        empty($this->itemData) && $this->kill();
    }

    /**
     * 任务执行
     *
     * @return void
     */
    public function run()
    {
        // initialization environments
        $this->initEnv();

        // lock current operation record
        $this->setItemLocked();

        // process the task
        $this->process();

        // unlock current operation record when work end
        $this->setItemUnlocked();
    }

    /**
     * 处理任务
     *
     * @return void
     * @throws Exception
     */
    private function process()
    {
        $service = $this->getServiceName();
        if (in_array($this->itemData['phase_name'], $service::$processFlow)) {
            $s = new $service;
            $s->setSubId(ResOp::single()->getSubId($this->itemData['op_id']));
            $s->setItemId($this->itemData['id']);
            $s->setData($this->itemData['data']);
            $s->run();
        } else {
            throw new \Exception('The phase name is not in the expected process flow !');
        }
    }

    /**
     * 初始化环境
     *
     * @return void
     */
    private function initEnv()
    {
        // include portal file
        include APP_PATH . '/index.php';

        // sets a user-defined exception handler function
        set_exception_handler(function($exception) {
            if ($exception instanceof AzureException) {
                $message = $exception->getErrorMessage();
            } else {
                $message = $exception->getMessage();
            }
            $this->setItemFailed($message);
            echo $exception;
        });

        // register a function for execution on shutdown
        register_shutdown_function(function() {
            $e = error_get_last();
            if (NULL !== $e && E_NOTICE !== $e['type']) {
                $this->setItemFailed($e['message']);
            }
        });
    }

    /**
     * 锁定当前条目
     *
     * @return void
     */
    private function setItemLocked()
    {
        ResItem::single()->updateData(
            $this->itemData['id'],
            $this->itemData['phase_name'],
            ResItem::STATUS_PROCESS
        );
    }

    /**
     * 解锁当前条目
     *
     * @return void
     */
    private function setItemUnlocked()
    {
        // save status
        $service = $this->getServiceName();
        $nextPhaseName = $service::getNextPhaseName($this->itemData['phase_name']);
        $status = empty($nextPhaseName) ?
            ResItem::STATUS_SUCCESS :
            ResItem::STATUS_ACCEPT;

        ResItem::single()->updateData(
            $this->itemData['id'],
            $nextPhaseName,
            $status
        );
    }

    /**
     * 设置条目状态为失败
     *
     * @param string $message
     * @return void
     */
    private function setItemFailed($message = '')
    {
        // save status
        ResItem::single()->updateData(
            $this->itemData['id'],
            $this->itemData['phase_name'],
            ResItem::STATUS_FAIL,
            $message
        );
    }

    /**
     * 获取服务类名称
     *
     * @return string
     */
    private function getServiceName()
    {
        return '\\Service\\' .
            $this->itemData['service_name'] . '\\' .
            $this->itemData['phase_name'];
    }
}
