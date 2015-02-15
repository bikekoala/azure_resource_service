<?PHP
namespace Rule\Atom;

use Rule\RuleAbstract;

/**
 * 枚举类型规则验证类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-02-02
 */
class Enum extends RuleAbstract
{
    /**
     * 验证方法
     *
     * @param mixed $param
     * @param string $fieldName
     * @param array $values
     * @return void
     * @throws Exception
     */
    public static function validate($param, $fieldName, array $values)
    {
        $errorMessage = '该字段必须在以下其中：' . implode(', ', $values);
        ! in_array($param, $values)&& self::throws($fieldName, $errorMessage);
    }
}
