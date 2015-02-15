<?PHP
namespace Rule\Field;

use Rule\RuleAbstract;

/**
 * 端口协议验证类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-02-02
 */
class PortProtocol extends RuleAbstract
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
        \Rule\Atom\Enum::validate($param, $fieldName, array('TCP', 'UDP'));
    }
}
