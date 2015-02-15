<?PHP
namespace Task;

use Task\Worder;
use Model\ResOp;
use Model\ResItem;

/**
 * 任务管理类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-19
 */
class Manager
{
    /**
     * 操作表ID
     *
     * @var int
     */
    private $opId;

    /**
     * 操作表数据
     *
     * @var array
     */
    private $opData;

    /**
     * 构造方法
     *
     * @param int $opId
     * @return void
     */
    public function __construct($opId)
    {
        $this->opId  = $opId;
        $this->opData = ResOp::single()->getData($opId);
    }

    /**
     * 分发任务
     *
     * @return void
     */
    public function dispatch()
    {
        // 多线程同步执行
        while (true) {
            $items = ResItem::single()->getDatasByOpIdAndStatus(
                $this->opId,
                ResItem::STATUS_ACCEPT
            );
            if (empty($items)) break;

            $threads = array();
            foreach ($items as $i => $data) {
                $threads[$i] = new Worker($data);
                $threads[$i]->start();
            }

            foreach ($threads as $i => $thread) {
                while ($thread->isRunning()) {
                    usleep(10);
                }
                $thread->join();
            }
        }

        // 将操作状态改为已完成
        ResOp::single()->updateStatus(
            $this->opId,
            ResOp::STATUS_SUCCESS
        );

        // 回调返回状态
        $this->callback();
    }

    /**
     * 回调方法
     *
     * @return void
     */
    private function callback()
    {
        // send callback request
        $serviceName = $this->getServiceName('Callback');
        $status = callback_url($this->opData['callback_url'], array(
            'id'    => $this->opId,
            'items' => (new $serviceName($this->opId))->run()
        ));

        // save callback status
        ResOp::single()->updateCallbackStatus(
            $this->opId,
            $status ? ResOp::CB_STATUS_SUCCESS : ResOp::CB_STATUS_FAIL
        );
    }

    /**
     * 获取服务类名称
     *
     * @param string $phaseName
     * @return string
     */
    private function getServiceName($phaseName)
    {
        return '\\Service\\' . $this->opData['api_name'] . '\\' . $phaseName;
    }
}
