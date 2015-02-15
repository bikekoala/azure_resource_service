<?PHP    
namespace Service\OperateVirtualMachine;

use Service\CallbackService;

/**
 * 调用服务
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-02-06
 */
class Callback extends CallbackService
{
    /**
     * 执行
     *
     * @return array
     */
    public function run()
    {
        $params = $this->getCommonParams();
        foreach ($this->items as $i => $item) {
            $params[$i]['operate_type'] = $item['data']['operate_type'];
        }
        return $params;
    }
}
