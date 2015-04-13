<?PHP    
namespace Service;

use Model\ResItem;
use Service\AbstractService;

/**
 * 基础回调服务
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-02-06
 */
class CallbackService extends AbstractService
{
    /**
     * 任务条目数据
     *
     * @var array
     */
    protected $items;

    /**
     * 执行
     *
     * @return void
     */
    public function run()
    {
    }

    /**
     * 设置操作条目数据
     *
     * @param array $items
     * @return void
     */
    public function setItems($items)
    {
        foreach ($items as &$item) {
            $item['data'] = unserialize($item['data']);
        }
        $this->items = $items;
    }

    /**
     * 获取通用的回调参数
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $params = array();
        foreach ($this->items as $i => $item) {
            $status = ResItem::STATUS_SUCCESS == $item['status'];
            $message = $status ? 'Succeed' : $item['message'];
            $params[$i] = [
                'cloud_service_name' => $item['data']['cloud_service_name'],
                'host_name'          => $item['data']['host_name'],
                'status'             => $status,
                'message'            => $message
            ];
        }
        return $params;
    }

    /**
     * 实时获取虚拟机资源信息
     *
     * @return array
     */
    protected function getAzureResourceForVm()
    {
        return $this->getAzureVmResource(array_column($this->items, 'data'));
    }
}
