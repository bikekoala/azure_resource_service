<?PHP    
namespace Service\DeleteResourcePackage;

use Service\CallbackService;

/**
 * 调用服务
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
        return $this->getCommonParams();
    }
}
