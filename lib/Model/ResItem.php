<?PHP
namespace Model;

/**
 * Azure资源条目模型
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-18
 */
class ResItem extends BaseModel
{
    /**
     * 表名称
     *
     * @var string
     */
    protected $table = 'azure_res_item';

    /**
     * 实例对象
     */
    public static $instance;

    /**
     * 状态
     */
    const STATUS_FAIL    = -1;
    const STATUS_ACCEPT  = 0;
    const STATUS_PROCESS = 1;
    const STATUS_SUCCESS = 2;

    /**
     * 根据操作表ID获取多条数据
     *
     * @param int $opId
     * @return array
     */
    public function getDatasByOpId($opId)
    {
        $sql = sprintf(
            'SELECT * FROM `%s` WHERE `op_id`=%d',
            $this->table,
            $opId
        );
        $sth = $this->pdo->prepare($sql);
        $sth->setFetchMode(\PDO::FETCH_ASSOC); 
        $sth->execute();
        $result = $sth->fetchAll();
        return $result ? : array();
    }

    /**
     * 根据操作表ID和状态获取多条数据
     *
     * @param int $opId
     * @param int $status
     * @return array
     */
    public function getDatasByOpIdAndStatus($opId, $status)
    {
        $sql = sprintf(
            'SELECT * FROM `%s` WHERE `op_id`=%d AND `status`=%d',
            $this->table,
            $opId,
            $status
        );
        $sth = $this->pdo->prepare($sql);
        $sth->setFetchMode(\PDO::FETCH_ASSOC); 
        $sth->execute();
        $result = $sth->fetchAll();
        return $result ? : array();
    }

    /**
     * 插入单条数据
     *
     * @param string $opId
     * @param string $data
     * @param string $phaseName
     * @param int $status
     * @return void
     * @throws Exception
     */
    public function addData(
        $opId,
        $data,
        $serviceName,
        $phaseName,
        $status = self::STATUS_ACCEPT
    ) {
        // prepare
        $sql = 'INSERT INTO `%s`(`op_id`, `data`, `service_name`, `phase_name`, `status`, `create_time`)
                VALUES (:op_id, :data, :service_name, :phase_name, :status, :create_time)';
        $sth = $this->pdo->prepare(sprintf($sql, $this->table));

        // bindvalue
        $sth->bindValue(':op_id', $opId, \PDO::PARAM_STR);
        $sth->bindValue(':data', $data, \PDO::PARAM_STR);
        $sth->bindValue(':service_name', $serviceName, \PDO::PARAM_STR);
        $sth->bindValue(':phase_name', $phaseName, \PDO::PARAM_STR);
        $sth->bindValue(':status', $status, \PDO::PARAM_INT);
        $sth->bindValue(':create_time', date('Y-m-d H:i:s'), \PDO::PARAM_INT);

        // execute
        try {
            $sth->execute();
            return (int) $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }

    /**
     * 更新数据
     *
     * @param int $id
     * @param string $phaseName
     * @param int $status
     * @param string $message
     * @return void
     */
    public function updateData($id, $phaseName, $status, $message = '')
    {
        // prepare
        $sql = 'UPDATE `%s`
                SET `phase_name`=:phase_name,
                    `status`=:status,
                    `message`=:message,
                    `update_time`=:update_time
                WHERE `id`=%d';
        $sql = sprintf($sql, $this->table, $id);
        $sth = $this->pdo->prepare($sql);

        // bindvalue
        $sth->bindValue(':phase_name', $phaseName, \PDO::PARAM_STR);
        $sth->bindValue(':status', $status, \PDO::PARAM_INT);
        $sth->bindValue(':message', $message, \PDO::PARAM_STR);
        $sth->bindValue(':update_time', date('Y-m-d H:i:s'), \PDO::PARAM_STR);

        // execute
        try {
            return $sth->execute();
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }
}
