<?PHP    
namespace Service\CreateResourcePackage;

use Service\AbstractService;

/**
 * 基本服务
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-18
 */
class Base extends AbstractService
{
    /**
     * 处理流
     *
     * @var array
     */
    public static $processFlow = array(
        'CreateStorageAccount',
        'CreateVirtualNetwork',
        'CreateCloudService',
        'CreateVirtualMachine',
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
