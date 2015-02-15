<?PHP
namespace Rule\Atom;

use Rule\RuleAbstract;

/**
 * IP规则验证类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-02-23
 */
class Ip extends RuleAbstract
{
    public static $filterId = FILTER_VALIDATE_IP;

    public static $errorMessage = '该字段必须符合标准的IP地址格式';
}
