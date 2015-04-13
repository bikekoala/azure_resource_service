<?PHP    
namespace Service\UpdateResourcePackage;

use Service\CallbackService;

/**
 * 回调服务
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-04-11
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
                $d = $vmRes[$item['data']['cloud_service_name']][$item['data']['host_name']];
                $params[$i]['internal_ip'] = $d['internal_ip'];
                $params[$i]['ports'] = $d['ports'];
            }
        }
        return $params;
    }
}
