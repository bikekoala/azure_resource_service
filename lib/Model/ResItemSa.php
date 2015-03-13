<?PHP
namespace Model;

/**
 * Azure资源条目存储账户模型
 *
 * todo:
 *  删除虚拟机时需要递减disk_count
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-26
 */
class ResItemSa extends BaseModel
{
    /**
     * 表名称
     *
     * @var string
     */
    protected $table = 'azure_res_item_sa';

    /**
     * 实例对象
     */
    public static $instance;

    /**
     * 磁盘数上限
     */
    const DISK_COUNT_LIMIT = 30;

    /**
     * 是否已创建
     */
    const STATUS_CREATED  = 1;
    const STATUS_CREATING = 0;

    /**
     * 获取可用的存储账户数据
     *
     * @param string $location
     * @param string $subId
     * @return array
     */
    public function getAvailableData($location, $subId)
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
        $result = $sth->fetchAll();
        if ($result) {
            // 磁盘数校验
            foreach ($result as $i => $item) {
                if (self::DISK_COUNT_LIMIT <= $item['disk_count']) {
                    unset($result[$i]);
                }
            }

            // 按是否创建成功排序
            $volume = array();
            foreach ($result as $key => $row) {
                $volume[$key] = $row['is_created'];
            }
            array_multisort($volume, SORT_DESC, $result);

            // 返回第一个结果
            return (array) array_shift($result);
        } else {
            return array();
        }
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
     * 更新磁盘数
     *
     * @param int $id
     * @param int $count
     * @return void
     */
    public function updateDiskCount($id, $count)
    {
        $sql = sprintf(
            'UPDATE `%s` SET `disk_count`=`disk_count`+%d WHERE `id`=%d',
            $this->table,
            $count,
            $id
        );
        $sth = $this->pdo->prepare($sql);

        // execute
        try {
            return $sth->execute();
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
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
     * @param string $label
     * @param string $location
     * @param int $diskCount
     * @param string $requestId
     * @param int $isCreated
     * @return int
     * @throws Exception
     */
    public function addData(
        $itemId,
        $subId,
        $name,
        $label,
        $location,
        $diskCount,
        $requestId,
        $isCreated
    ) {
        // prepare
        $sql = 'INSERT INTO `%s`(
                    `item_id`,
                    `sub_id`,
                    `name`,
                    `label`,
                    `location`,
                    `disk_count`,
                    `request_id`,
                    `is_created`,
                    `create_time`
                )
                VALUES (
                    :item_id,
                    :sub_id,
                    :name,
                    :label,
                    :location,
                    :disk_count,
                    :request_id,
                    :is_created,
                    :create_time
                )';
        $sth = $this->pdo->prepare(sprintf($sql, $this->table));

        // bindvalue
        $sth->bindValue(':item_id', $itemId, \PDO::PARAM_INT);
        $sth->bindValue(':sub_id', $subId, \PDO::PARAM_STR);
        $sth->bindValue(':name', $name, \PDO::PARAM_STR);
        $sth->bindValue(':label', $label, \PDO::PARAM_STR);
        $sth->bindValue(':location', $location, \PDO::PARAM_STR);
        $sth->bindValue(':disk_count', $diskCount, \PDO::PARAM_INT);
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
