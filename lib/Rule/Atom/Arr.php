<?PHP
namespace Rule\Atom;

use Rule\RuleAbstract;

/**
 * 数组验证类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-30
 */
class Arr extends RuleAbstract
{
    public static $errorMessage = '该字段必须是数组类型';

    /**
     * 验证方法
     *
     * @param string $param
     * @param string $fieldName
     * @param string $errorMessage
     * @return void
     * @throws Exception
     */
    public static function validate($param, $fieldName, $errorMessage = '')
    {
        ! is_array($param) && self::throws($fieldName, $errorMessage);
    }
}
