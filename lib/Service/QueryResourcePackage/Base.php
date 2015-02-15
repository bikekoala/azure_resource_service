<?PHP    
namespace Service\QueryResourcePackage;

use Service\AbstractService;

/**
 * 基本服务
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-02-06
 */
class Base extends AbstractService
{
    /**
     * 处理流
     *
     * @var array
     */
    public static $processFlow = array(
        'Base'
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
