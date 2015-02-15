<?PHP
namespace Rule\Field;

use Rule\Field\CloudServiceName;

/**
 * 主机名称验证类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-31
 */
class HostName extends CloudServiceName
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
}
