<?PHP
namespace Model;

/**
 * Azure资源条目云服务模型
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-25
 */
class ResItemCs extends BaseModel
{
    /**
     * 表名称
     *
     * @var string
     */
    protected $table = 'azure_res_item_cs';

    /**
     * 实例对象
     */
    public static $instance;

    /**
     * 删除状态常量
     */
    const STATUS_DELETE_TRUE  = 1;
    const STATUS_DELETE_FALSE = 0;

    /**
     * 通过云服务名称获取表ID
     *
     * @param string $name
     * @return int
     */
    public function getIdByName($name)
    {
        $sql = sprintf(
            'SELECT `id` FROM `%s` WHERE `name`="%s" AND `is_deleted`=%d',
            $this->table,
            $name,
            self::STATUS_DELETE_FALSE
        );
        $sth = $this->pdo->prepare($sql);
        $sth->execute();
        return (int) $sth->fetchColumn();
    }

    /**
     * 通过云服务名称获取单条数据
     *
     * @param string $name
     * @return array
     */
    public function getDataByName($name)
    {
        $sql = sprintf(
            'SELECT * FROM `%s` WHERE `name`="%s" AND `is_deleted`=%d',
            $this->table,
            $name,
            self::STATUS_DELETE_FALSE
        );
        $sth = $this->pdo->prepare($sql);
        $sth->setFetchMode(\PDO::FETCH_ASSOC); 
        $sth->execute();
        $result = $sth->fetch();
        return $result ? : array();
    }

    /**
     * 插入单条数据
     *
     * @param int $itemId
     * @param string $name
     * @param string $label
     * @param string $location
     * @param string $requestId
     *
     * @return void
     * @throws Exception
     */
    public function addData(
        $itemId,
        $name,
        $label,
        $location,
        $requestId
    ) {
        // prepare
        $sql = 'INSERT INTO `%s`(
                    `item_id`,
                    `name`,
                    `label`,
                    `location`,
                    `request_id`,
                    `is_deleted`,
                    `create_time`
                )
                VALUES (
                    :item_id,
                    :name,
                    :label,
                    :location,
                    :request_id,
                    :is_deleted,
                    :create_time
                )';
        $sth = $this->pdo->prepare(sprintf($sql, $this->table));

        // bindvalue
        $sth->bindValue(':item_id', $itemId, \PDO::PARAM_INT);
        $sth->bindValue(':name', $name, \PDO::PARAM_STR);
        $sth->bindValue(':label', $label, \PDO::PARAM_STR);
        $sth->bindValue(':location', $location, \PDO::PARAM_STR);
        $sth->bindValue(':request_id', $requestId, \PDO::PARAM_STR);
        $sth->bindValue(':is_deleted', self::STATUS_DELETE_FALSE, \PDO::PARAM_INT);
        $sth->bindValue(':create_time', date('Y-m-d H:i:s'), \PDO::PARAM_STR);

        // execute
        try {
            return $sth->execute();
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }

    /**
     * 通过ID更新删除状态
     *
     * @param int $id
     * @param int $status
     * @return void
     */
    public function updateDeleteStatusById($id, $status)
    {
        // prepare
        $sql = 'UPDATE `%s`
                SET `is_deleted`=:is_deleted,
                    `update_time`=:update_time
                WHERE `id`=%d';
        $sql = sprintf($sql, $this->table, $id);
        $sth = $this->pdo->prepare($sql);

        // bindvalue
        $sth->bindValue(':is_deleted', $status, \PDO::PARAM_INT);
        $sth->bindValue(':update_time', date('Y-m-d H:i:s'), \PDO::PARAM_STR);

        // execute
        try {
            return $sth->execute();
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }
}
