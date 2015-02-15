<?PHP
namespace Model;

/**
 * Azure资源虚拟机尺寸模型
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-27
 */
class ResVmSize extends BaseModel
{
    /**
     * 表名称
     *
     * @var string
     */
    protected $table = 'azure_vm_size';

    /**
     * 实例对象
     */
    public static $instance;

    /**
     * 获取尺寸名称
     *
     * @param int $id
     * @return string
     */
    public function getName($id)
    {
        return $this->getFieldById($id, 'name');
    }
}
