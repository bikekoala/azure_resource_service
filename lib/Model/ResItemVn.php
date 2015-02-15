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
     * 插入单条数据
     *
     * @param int $itemId
     * @param int $subId
     * @param string $name
     * @param string $location
     * @param string $addressPrefix
     * @param string $requestId
     * @return int
     * @throws Exception
     */
    public function addData(
        $itemId,
        $subId,
        $name,
        $location,
        $addressPrefix,
        $requestId
    ) {
        // prepare
        $sql = 'INSERT INTO `%s`(
                    `item_id`,
                    `sub_id`,
                    `name`,
                    `location`,
                    `address_prefix`,
                    `request_id`,
                    `create_time`
                )
                VALUES (
                    :item_id,
                    :sub_id,
                    :name,
                    :location,
                    :address_prefix,
                    :request_id,
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
