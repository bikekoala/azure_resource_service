<?PHP    
namespace Service\DeleteResourcePackage;

use Service\AbstractService;

/**
 * 基本服务
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-04-08
 */
class Base extends AbstractService
{
    /**
     * 处理流
     *
     * @var array
     * @todo 暂且不删除虚拟网络
     */
    public static $processFlow = array(
        'DeleteVirtualMachine',
        'DeleteStorageAccount',
        'DeleteCloudService',
        //'DeleteVirtualNetwork'
    );

    /**
     * 执行
     *
     * @return void
     */
    public function run()
    {
    }
}
