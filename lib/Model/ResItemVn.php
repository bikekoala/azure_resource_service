<?PHP
namespace Model;

/**
 * Azure资源条目云虚拟网络模型
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-26
 */
class ResItemVn extends BaseModel
{
    /**
     * 表名称
     *
     * @var string
     */
    protected $table = 'azure_res_item_vn';

    /**
     * 实例对象
     */
    public static $instance;

    /**
     * 是否已创建
     */
    const STATUS_CREATED  = 1;
    const STATUS_CREATING = 0;

    /**
     * 通过订阅ID获取多条数据
     *
     * @param string $subId
     * @return array
     */
    public function getDatasBySubId($subId)
    {
        $sql = sprintf(
            'SELECT * FROM `%s` WHERE `sub_id`="%s"',
            $this->table,
            $subId
        );
        $sth = $this->pdo->prepare($sql);
        $sth->setFetchMode(\PDO::FETCH_ASSOC); 
        $sth->execute();
        $result = $sth->fetchAll();
        return $result ? : array();
    }

    /**
     * 通过地域名称和订阅ID获取单条数据
     *
     * @param string $location
     * @param string $subId
     * @return array
     */
    public function getDataByLocationAndSubId($location, $subId)
    {
        $sql = sprintf(
            'SELECT * FROM `%s` WHERE `location`="%s" AND `sub_id`="%s"',
            $this->table,
            $location,
            $subId
        );
        $sth = $this->pdo->prepare($sql);
        $sth->setFetchMode(\PDO::FETCH_ASSOC); 
        $sth->execute();
        $result = $sth->fetch();
        return $result ? : array();
    }

    /**
     * 通过虚拟网络名称和订阅ID获取单条数据
     *
     * @param string $name
     * @param string $subId
     * @return array
     */
    public function getDataByNameAndSubId($name, $subId)
    {
        $sql = sprintf(
            'SELECT * FROM `%s` WHERE `name`="%s" AND `sub_id`="%s"',
            $this->table,
            $name,
            $subId
        );
        $sth = $this->pdo->prepare($sql);
        $sth->setFetchMode(\PDO::FETCH_ASSOC); 
        $sth->execute();
        $result = $sth->fetch();
        return $result ? : array();
    }

    /**
     * 通过ID获取创建状态
     *
     * @param int $id
     * @return int
     */
    public function getCreateStatusById($id)
    {
        $sql = sprintf(
            'SELECT `is_created` FROM `%s` WHERE `id`="%d"',
            $this->table,
            $id
        );
        $sth = $this->pdo->prepare($sql);
        $sth->execute();
        return (int) $sth->fetchColumn();
    }

    /**
     * 更新数据
     *
     * @param int $id
     * @param string $requestId
     * @param int $isCreated
     * @return mixed
     */
    public function updateDataById($id, $requestId, $isCreated)
    {
        // prepare
        $sql = 'UPDATE `%s`
                SET `request_id`=:request_id, `is_created`=:is_created
                WHERE `id`=%d';
        $sth = $this->pdo->prepare(sprintf($sql, $this->table, $id));

        // bindvalue
        $sth->bindValue(':request_id', $requestId, \PDO::PARAM_STR);
        $sth->bindValue(':is_created', $isCreated, \PDO::PARAM_INT);

        // execute
        try {
            return $sth->execute();
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }

    /**
     * 插入单条数据
     *
     * @param int $itemId
     * @param int $subId
     * @param string $name
     * @param string $location
     * @param string $addressPrefix
     * @param string $requestId
     * @param int $isCreated
     * @return int
     * @throws Exception
     */
    public function addData(
        $itemId,
        $subId,
        $name,
        $location,
        $addressPrefix,
        $requestId,
        $isCreated
    ) {
        // prepare
        $sql = 'INSERT INTO `%s`(
                    `item_id`,
                    `sub_id`,
                    `name`,
                    `location`,
                    `address_prefix`,
                    `request_id`,
                    `is_created`,
                    `create_time`
                )
                VALUES (
                    :item_id,
                    :sub_id,
                    :name,
                    :location,
                    :address_prefix,
                    :request_id,
                    :is_created,
                    :create_time
                )';
        $sth = $this->pdo->prepare(sprintf($sql, $this->table));

        // bindvalue
        $sth->bindValue(':item_id', $itemId, \PDO::PARAM_INT);
        $sth->bindValue(':sub_id', $subId, \PDO::PARAM_STR);
        $sth->bindValue(':name', $name, \PDO::PARAM_STR);
        $sth->bindValue(':location', $location, \PDO::PARAM_STR);
        $sth->bindValue(':address_prefix', $addressPrefix, \PDO::PARAM_STR);
        $sth->bindValue(':request_id', $requestId, \PDO::PARAM_STR);
        $sth->bindValue(':is_created', $isCreated, \PDO::PARAM_INT);
        $sth->bindValue(':create_time', date('Y-m-d H:i:s'), \PDO::PARAM_STR);

        // execute
        try {
            $sth->execute();
            return (int) $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }
}
