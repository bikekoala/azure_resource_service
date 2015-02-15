<?PHP
namespace Model;

/**
 * Azure资源操作模型
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-18
 */
class ResOp extends BaseModel
{
    /**
     * 表名称
     *
     * @var string
     */
    protected $table = 'azure_res_op';

    /**
     * 实例对象
     */
    public static $instance;

    /**
     * 操作状态
     */
    const STATUS_ACCEPT  = 0;
    const STATUS_PROCESS = 1;
    const STATUS_SUCCESS = 2;

    /**
     * callback 状态
     */
    const CB_STATUS_FAIL    = -1;
    const CB_STATUS_DEFAULT = 0;
    const CB_STATUS_SUCCESS = 1;

    /**
     * 获取订阅ID
     *
     * @param int $id
     * @return string
     */
    public function getSubId($id)
    {
        return $this->getFieldById($id, 'sub_id');
    }

    /**
     * 根据操作状态获取多条数据
     *
     * @param int $status
     * @return array
     */
    public function getDatasByStatus($status)
    {
        $sql = sprintf(
            'SELECT * FROM `%s` WHERE `status`=%d',
            $this->table,
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
     * @param string $subId
     * @param string $callbackUrl
     * @param string $apiName
     * @param string $apiData
     * @return int
     * @throws Exception
     */
    public function addData($subId, $callbackUrl, $apiName, $apiData)
    {
        // prepare
        $sql = 'INSERT INTO `%s`(`sub_id`, `callback_url`, `api_name`, `api_data`, `create_time`)
                VALUES (:sub_id, :callback_url, :api_name, :api_data, :create_time)';
        $sth = $this->pdo->prepare(sprintf($sql, $this->table));

        // bindvalue
        $sth->bindValue(':sub_id', $subId, \PDO::PARAM_STR);
        $sth->bindValue(':callback_url', $callbackUrl, \PDO::PARAM_STR);
        $sth->bindValue(':api_name', $apiName, \PDO::PARAM_STR);
        $sth->bindValue(':api_data', $apiData, \PDO::PARAM_STR);
        $sth->bindValue(':create_time', date('Y-m-d H:i:s'), \PDO::PARAM_STR);

        // execute
        try {
            $sth->execute();
            return (int) $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }

    /**
     * 更新操作状态
     *
     * @param int $id
     * @param int $status
     * @return void
     */
    public function updateStatus($id, $status)
    {
        // prepare
        $sql = 'UPDATE `%s`
                SET `status`=:status,
                    `update_time`=:update_time
                WHERE `id`=%d';
        $sql = sprintf($sql, $this->table, $id);
        $sth = $this->pdo->prepare($sql);

        // bindvalue
        $sth->bindValue(':status', $status, \PDO::PARAM_INT);
        $sth->bindValue(':update_time', date('Y-m-d H:i:s'), \PDO::PARAM_STR);

        // execute
        try {
            return $sth->execute();
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }

    /**
     * 更新callback状态
     *
     * @param int $id
     * @param int $status
     * @return void
     */
    public function updateCallbackStatus($id, $status)
    {
        // prepare
        $sql = 'UPDATE `%s`
                SET `callback_status`=:callback_status,
                    `update_time`=:update_time
                WHERE `id`=%d';
        $sql = sprintf($sql, $this->table, $id);
        $sth = $this->pdo->prepare($sql);

        // bindvalue
        $sth->bindValue(':callback_status', $status, \PDO::PARAM_INT);
        $sth->bindValue(':update_time', date('Y-m-d H:i:s'), \PDO::PARAM_STR);

        // execute
        try {
            return $sth->execute();
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }
}
