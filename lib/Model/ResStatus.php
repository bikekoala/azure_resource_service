<?PHP
namespace Model;

/**
 * Azure资源异步操作状态模型
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-25
 */
class ResStatus extends BaseModel
{
    /**
     * 表名称
     *
     * @var string
     */
    protected $table = 'azure_res_status';

    /**
     * 实例对象
     */
    public static $instance;

    /**
     * 保存单条数据
     *
     * @param int $requestId
     * @param string $status
     * @param string $errorCode
     * @param string $errorMessage
     * @return void
     * @throws Exception
     */
    public function saveData(
        $requestId,
        $status,
        $errorCode = '',
        $errorMessage = ''
    ) {
        // prepare
        $sql = 'INSERT INTO `%s`(
                    `request_id`,
                    `status`,
                    `error_code`,
                    `error_message`,
                    `create_time`,
                    `update_time`
                )
                VALUES (
                    :request_id,
                    :status,
                    :error_code,
                    :error_message,
                    :create_time,
                    :update_time
                )
                ON DUPLICATE KEY UPDATE
                    `request_id`=:request_id,
                    `status`=:status,
                    `error_code`=:error_code,
                    `error_message`=:error_message,
                    `update_time`=:update_time';
        $sth = $this->pdo->prepare(sprintf($sql, $this->table));

        // bindvalue
        $curTime = date('Y-m-d H:i:s');
        $sth->bindValue(':request_id', $requestId, \PDO::PARAM_INT);
        $sth->bindValue(':status', $status, \PDO::PARAM_STR);
        $sth->bindValue(':error_code', $errorCode, \PDO::PARAM_STR);
        $sth->bindValue(':error_message', $errorMessage, \PDO::PARAM_STR);
        $sth->bindValue(':create_time', $curTime, \PDO::PARAM_STR);
        $sth->bindValue(':update_time', $curTime, \PDO::PARAM_STR);

        // execute
        try {
            return $sth->execute();
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }
}
