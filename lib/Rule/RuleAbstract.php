<?PHP
namespace Rule;

/**
 * 规则抽象类类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-30
 */
abstract class RuleAbstract
{
    /**
     * 最小静态参数
     */
    public static $filterId; // 过滤器ID
    public static $filterOptions = array(); // 过滤器选项
    public static $errorMessage = ''; // 默认失败信息

    /**
     * 验证方法
     *
     * @param string $param
     * @param string $fieldName
     * @param string $errorMessage
     * @return void
     * @throws Exception
     */
    public static function validate($param, $fieldName, $errorMessage = '')
    {
        $status = filter_var($param, static::$filterId, static::$filterOptions);
        if (false === $status) {
            self::throws($fieldName, $errorMessage);
        }
    }

    /**
     * 异常封装方法
     *
     * @param string $fieldName
     * @param string $errorMessage
     * @return void
     * @throws Exception
     */
    public static function throws($fieldName, $errorMessage = '')
    {
        $message = sprintf(
            'Field: %s, ErrMsg: %s',
            $fieldName,
            $errorMessage ? : static::$errorMessage
        );
        throw new \Exception($message);
    }
}
