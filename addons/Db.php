<?PHP
/**
 * pdo数据库抽象类的封装
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-15
 */
final class Db extends PDO
{
    /**
     * 实例对象
     */
    public static $instances = array();

    /**
     * 构造函数
     *
     * @param string $dsn
     * @return void
     */
    public function __construct($dsn) 
    {
        $temp = parse_url($dsn);
        if ($temp['scheme'] == 'mysql') {
            parse_str($temp['query'], $query);
            $user = isset($temp['user']) ? $temp['user'] : 'root';
            $pass = isset($temp['pass']) ? $temp['pass'] : '';
            $port = isset($temp['port']) ? $temp['port'] : '3306';
            $charset = isset($query['charset']) ? $query['charset'] : 'utf8';
            $str = 'mysql:dbname=' . $query['dbname'] .
                ';host=' . $temp['host'] .
                ';port=' . $port .
                ';charset=' . $charset;
            $options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
            $options[PDO::MYSQL_ATTR_USE_BUFFERED_QUERY] = true;
            parent::__construct($str, $user, $pass, $options);
        } else {
            parent::__construct($dsn);
        }
    }

    /**
     * 获取对象实例静态方法
     *
     * @param string $dsn
     * @return Object
     */
    public static function getInstance($dsn)
    {
        if ( ! isset(self::$instances[$dsn])) {
            self::$instances[$dsn] = new self($dsn);
        }
        return self::$instances[$dsn];
    }

    /**
     * 根据变量数组组合sql赋值语句
     *
     * @param array $keys
     * @param array $vals
     * @return string
     */
    public static function genSqlValueStr($keys, &$vals)
    {
        $columns = array();
        foreach ($keys as $key) {
            if (isset($vals[$key])) {
                $columns[] = '`' . $key . '`=:' .$key;
            }
        }
        return implode(',', $columns);
    }

    /**
     * 根据变量数组绑定sql数据
     *
     * @param PDOStatement $sth
     * @param array $keys
     * @param array $vals
     * @return void
     */
    public static function genSqlBindValue(PDOStatement $sth, $keys, &$vals)
    {
        foreach ($keys as $key) {
            if (isset($vals[$key])) {
                $sth->bindValue(':' . $key, $vals[$key]);
            }
        }
    }
}
