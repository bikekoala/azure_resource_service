<?PHP    
namespace Service\CreateResourcePackage;

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
        $vmRes = $this->getAzureResourceForVm();
        $params = $this->getCommonParams();
        foreach ($this->items as $i => $item) {
            $d = $item['data'];
            $params[$i]['data'] = $vmRes[$d['cloud_service_name']][$d['host_name']];
            $params[$i]['data']['size_id'] = $d['size_id'];
            $params[$i]['data']['image_id'] = $d['image_id'];
            $params[$i]['data']['location'] = $d['location'];
            $params[$i]['data']['user_name'] = $d['user_name'];
            $params[$i]['data']['user_password'] = $d['user_password'];
        }
        return $params;
    }
}
