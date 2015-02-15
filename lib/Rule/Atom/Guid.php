<?PHP
namespace Rule\Atom;

use Rule\RuleAbstract;

/**
 * GUID验证类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-30
 */
class Guid extends RuleAbstract
{
    public static $filterId = FILTER_VALIDATE_REGEXP;

    public static $filterOptions = array(
        'options' => array(
            'regexp' => '/^[a-f0-9]{8}(-[a-f0-9]{4}){3}-[a-f0-9]{12}$/'
        )
    );

    public static $errorMessage = '该字段必须符合标准的GUID标识符格式';
}
