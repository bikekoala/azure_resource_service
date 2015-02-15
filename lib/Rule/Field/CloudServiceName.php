<?PHP
namespace Rule\Field;

use Rule\RuleAbstract;

/**
 * 云服务验证类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-31
 */
class CloudServiceName extends RuleAbstract
{
    protected static $param;
    protected static $fieldName;

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
        static::$param = $param;
        static::$fieldName = $fieldName;

        static::checkLength();
        static::checkFormat();
    }

    /**
     * 检查字符串长度
     *
     * @param int $start
     * @param int $end
     * @return void
     * @throws Exception
     */
    protected static function checkLength($start = 1, $end = 63)
    {
        \Rule\Atom\Str::validate(self::$param, self::$fieldName, $start, $end);
    }

    /**
     * 检查字符格式
     *
     * @return void
     */
    protected static function checkFormat()
    {
        $options = array(
            'options' => array(
                'regexp' => '/^[a-zA-Z0-9]+([a-zA-Z0-9-]*[a-zA-Z0-9]+)*$/'
            )
        );
        $status = filter_var(self::$param, FILTER_VALIDATE_REGEXP, $options);
        if (false === $status) {
            $errorMessage = '该字段只能包含字母、数字和连字符。该字段必须以字母或数字开头和结尾。';
            self::throws(self::$fieldName, $errorMessage);
        }
    }
}
