<?PHP
namespace Rule\Field;

use Rule\RuleAbstract;

/**
 * 用户密码验证类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-31
 */
class UserPassword extends RuleAbstract
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
        \Rule\Atom\Str::validate($param, $fieldName, 8, 123);

        if ( ! self::judge($param)) {
            static::throws($fieldName, '密码必须包含以下 3 项: 小写字符, 大写字符, 数字, 特殊字符');
        }
    }

    /**
     * 判断密码是否符合规则
     *
     * @param string $password
     * @return bool
     */
    private static function judge($password)
    {
        $count = 0;                     // 满足规则的数量
        $lowercase = '/[a-z]/';         // 小写字母正则
        $capital = '/[A-Z]/';           // 大写字母正则
        $digital = '/[0-9]/';           // 数字正则
        $spec = '/[,.<>{}~!@#$%^&*_]/'; // 特殊字符正则

        if (preg_match($lowercase, $password)) {
            $count++;
        }
        if (preg_match($capital, $password)) {
            $count++;
        }
        if (preg_match($digital, $password)) {
            $count++;
        }
        if (preg_match($spec, $password)) {
            $count++;
        }

        return 3 <= $count;
    }
}
