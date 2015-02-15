<?PHP
namespace Rule\Field;

use Rule\RuleAbstract;

/**
 * 地域验证类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-02-02
 */
class Location extends RuleAbstract
{
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
        $locations = array(
            'China North',
            'China East'
        );
        \Rule\Atom\Enum::validate($param, $fieldName, $locations);
    }
}
