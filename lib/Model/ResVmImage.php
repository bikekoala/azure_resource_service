<?PHP
namespace Model;

/**
 * Azure资源虚拟机镜像模型
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-28
 */
class ResVmImage extends BaseModel
{
    /**
     * 表名称
     *
     * @var string
     */
    protected $table = 'azure_vm_image';

    /**
     * 实例对象
     */
    public static $instance;
}
