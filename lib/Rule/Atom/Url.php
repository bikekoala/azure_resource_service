<?PHP
namespace Rule\Atom;

use Rule\RuleAbstract;

/**
 * URL验证类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-30
 */
class Url extends RuleAbstract
{
    public static $filterId = FILTER_VALIDATE_URL;

    public static $errorMessage = '该字段必须符合标准的URL地址格式';
}
