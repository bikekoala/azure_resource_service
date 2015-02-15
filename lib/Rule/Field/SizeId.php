<?PHP
namespace Rule\Field;

use Rule\RuleAbstract;
use Model\ResVmSize;

/**
 * 机器尺寸ID验证类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-31
 */
class SizeId extends RuleAbstract
{
    /**
     * 验证方法
     *
     * @param int $param
     * @param string $fieldName
     * @return void
     * @throws Exception
     */
    public static function validate($param, $fieldName)
    {
        \Rule\Atom\Int::validate($param, $fieldName);

        static::checkIfDbExists($param, $fieldName);
    }

    /**
     * 检查该ID是否存在于数据库中
     *
     * @param int $id
     * @param string $fieldName
     * @return void
     * @throws Exception
     */
    protected static function checkIfDbExists($id, $fieldName)
    {
        $name = ResVmSize::single()->getName($id);
        if ( ! $name) {
            self::throws($fieldName, sprintf(
                '数据库中没有检索到字段值为 %d 的记录',
                $id
            ));
        }
    }
}
