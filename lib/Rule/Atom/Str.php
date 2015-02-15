<?PHP
namespace Rule\Atom;

use Rule\RuleAbstract;

/**
 * 字符串验证类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-31
 */
class Str extends RuleAbstract
{
    /**
     * 验证方法
     * 当$start 与 $end 都等于0时，不做长度的验证
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
        if ( ! is_string($param)) {
            $errorMessage = '该字段必须是字符串类型';
            self::throws($fieldName, $errorMessage);
        }

        if ( ! (0 === $start && 0 === $end)) {
            $len = strlen($param);
            if ($len < $start || $len > $end) {
                $errorMessage = sprintf(
                    '该字段的长度必须介于 %d 与 %d 个字符之间',
                    $start,
                    $end
                );
                self::throws($fieldName, $errorMessage);
            }
        }
    }
}
