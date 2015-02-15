<?PHP
namespace Model;

/**
 * 基本模型类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-18
 */
class BaseModel
{
    /**
     * 表名称
     *
     * @var string
     */
    protected $table;

    /**
     * PDO实例
     *
     * @var PDO
     */
    public $pdo;

    /**
     * 实例对象
     */
    public static $instance;

    /**
     * 构造函数
     *
     * @return void
     */
    public function __construct()
    {
        require_once ADDONS_PATH . 'Db.php';
        $this->pdo = \Db::getInstance(PDO_URI);
    }

    /**
     * 返回实例
     *
     * @return Object
     */
    public static function single()
    {
        return static::$instance ? : (static::$instance = new static());
    }

    /**
     * 通过ID获取特定字段数据
     *
     * @param int $id
     * @param string $fieldName
     * @return string
     */
    protected function getFieldById($id, $fieldName)
    {
        $sql = sprintf(
            'SELECT `%s` FROM `%s` WHERE `id`="%d"',
            $fieldName,
            $this->table,
            $id
        );
        $sth = $this->pdo->prepare($sql);
        $sth->execute();
        $result = $sth->fetchColumn();
        return $result ? : '';
    }

    /**
     * 获取单条数据
     *
     * @param int $id
     * @return array
     */
    public function getData($id)
    {
        $sql = sprintf(
            'SELECT * FROM `%s` WHERE `id`="%d"',
            $this->table,
            $id
        );
        $sth = $this->pdo->prepare($sql);
        $sth->execute();
        $result = $sth->fetch();
        return $result ? : array();
    }
}
