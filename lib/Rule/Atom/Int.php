<?PHP
namespace Rule\Atom;

use Rule\RuleAbstract;

/**
 * 整型验证类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-31
 */
class Int extends RuleAbstract
{
    /**
     * 验证方法
     * 当$start 与 $end 都等于0时，不做大小的验证
     *
     * @param int $param
     * @param string $fieldName
     * @param int $start 开始数
     * @param int $end 结束数
     * @return void
     * @throws Exception
     */
    public static function validate($param, $fieldName, $start = 0, $end = 0)
    {
        if ( ! is_int($param)) {
            $errorMessage = '该字段必须是整数类型';
            self::throws($fieldName, $errorMessage);
        }

        if ( ! (0 === $start && 0 === $end)) {
            if ($param < $start || $param > $end) {
                $errorMessage = sprintf('该字段必须介于 %d 与 %d 之间', $start, $end);
                self::throws($fieldName, $errorMessage);
            }
        }
    }
}
