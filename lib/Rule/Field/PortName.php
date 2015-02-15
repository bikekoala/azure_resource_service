<?PHP
namespace Rule\Field;

use Rule\Field\CloudServiceName;

/**
 * 端口名称字段规则验证类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-02-02
 */
class PortName extends CloudServiceName
{
    /**
     * 检查字符串长度
     *
     * @param int $start
     * @param int $end
     * @return void
     * @throws Exception
     */
    protected static function checkLength($start = 3, $end = 15)
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
                'regexp' => '/^[a-zA-Z0-9]+([a-zA-Z0-9-_ ]*[a-zA-Z0-9]+)*$/'
            )
        );
        $status = filter_var(self::$param, FILTER_VALIDATE_REGEXP, $options);
        if (false === $status) {
            $errorMessage = '该字段只能包含字母、数字、连字符、空格和下划线。该名称必须以字母开头且必须以字母或数字结尾。';
            self::throws(self::$fieldName, $errorMessage);
        }
    }
}
