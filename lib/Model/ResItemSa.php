<?PHP
namespace Model;

/**
 * Azure资源条目存储账户模型
 *
 * todo:
 *  创建虚拟机时需要递增disk_count
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
     * 获取可用的存储账户数据
     *
     * @param string $subId
     * @return array
     */
    public function getAvailableData($subId)
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
        if ($result) {
            foreach ($result as $i => $item) {
                if (self::DISK_COUNT_LIMIT <= $item['disk_count']) {
                    unset($result[$i]);
                }
            }
            return (array) array_pop($result);
        } else {
            return array();
        }
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
     * 插入单条数据
     *
     * @param int $itemId
     * @param int $subId
     * @param string $name
     * @param string $label
     * @param string $location
     * @param string $requestId
     * @param int $diskCount
     * @return void
     * @throws Exception
     */
    public function addData(
        $itemId,
        $subId,
        $name,
        $label,
        $location,
        $requestId,
        $diskCount = 0
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
        $sth->bindValue(':create_time', date('Y-m-d H:i:s'), \PDO::PARAM_STR);

        // execute
        try {
            return $sth->execute();
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }
}
