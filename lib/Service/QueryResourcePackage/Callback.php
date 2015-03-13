<?PHP    
namespace Service\QueryResourcePackage;

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
        $vmRes = $this->getAzureResourceForVm();
        if ( ! empty($vmRes)) {
            foreach ($this->items as $i => $item) {
                $vm = $vmRes[$item['data']['cloud_service_name']][$item['data']['host_name']];
                $params[$i]['host_status'] = $vm['host_status'];
                $params[$i]['power_state'] = $vm['power_state'];
            }
        }
        
        return $params;
    }
}
