<?PHP
namespace Rule\Field;

use Rule\RuleAbstract;
use Model\Subscription;

/**
 * 订阅ID验证类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-02-23
 */
class SubscriptionId extends RuleAbstract
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
        \Rule\Atom\Guid::validate($param, $fieldName);

        static::checkIfDbExists($param, $fieldName);
    }

    /**
     * 检查该ID是否存在于数据库中
     *
     * @param int $id
     * @param string $fieldName
     * @return void
     * @throws Exception
     */
    protected static function checkIfDbExists($id, $fieldName)
    {
        $cert = Subscription::single()->getCertByGuid($id);
        if ( ! $cert) {
            self::throws($fieldName, sprintf(
                '数据库中没有检索到字段值为 %s 的记录',
                $id
            ));
        }
    }
}
