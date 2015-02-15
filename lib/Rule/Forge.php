<?PHP
namespace Rule;

/**
 * 规则熔炼炉类 (PS: 批量规则验证 :)
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-31
 */
class Forge extends RuleAbstract
{
    /**
     * 规则列表选项字段
     */
    const OPT_REQUIRE = 'require'; // 是否必须(布尔)
    const OPT_RULES   = 'rules';   // 规则列表(数组)
    const OPT_NODES   = 'nodes';   // 节点列表(数组)

    /**
     * 批量验证方法
     * 最小规则原则
     *
     * @param array $params
     * @param array $ruleList
     * @return void
     * @throws Exception
     */
    public static function validate(array $params, array $ruleList)
    {
        foreach ($ruleList as $field => $options) {
            // 支持多维规则递归验证
            if (is_int($field)) {
                self::validate($params[$field], $options);
                continue;
            }

            // 字段需要性验证
            if (isset($options[self::OPT_REQUIRE])) {
                if (true === $options[self::OPT_REQUIRE]) {
                    if ( ! isset($params[$field])) {
                        static::throws($field, '该字段是必须的');
                    }
                }
            }
            if ( ! isset($params[$field])) {
                continue;
            }

            // 规则验证
            if (isset($options[self::OPT_RULES])) {
                if (is_array($options[self::OPT_RULES])) {
                    foreach ($options[self::OPT_RULES] as $k => $v) {
                        if (is_int($k)) {
                            $rulePath = $v;
                            $opts = array();
                        } else {
                            $rulePath = $k;
                            $opts = is_array($v) ? $v : array();
                        }
                        self::callRule($rulePath, $params[$field], $field, $opts);
                    }
                }
            }

            // 子节点递归验证
            if (isset($options[self::OPT_NODES])) {
                if (is_array($options[self::OPT_NODES])) {
                    self::validate($params[$field], $options[self::OPT_NODES]);
                }
            }
        }
    }

    /**
     * 调用规则验证方法
     *
     * @param string $rulePath
     * @param mixed $param
     * @param string $field
     * @param array $options
     * @return void
     * @throws Exception
     */
    private static function callRule(
        $rulePath,
        $param,
        $field,
        $options = array()
    ) {
        $className = '\\Rule\\' . str_replace('/', '\\', $rulePath);
        if (class_exists($className)) {
            $className::validate($param, $field, ...$options);
        } else {
            self::throws($className, '该规则验证类不存在');
        }
    }
}
