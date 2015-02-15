<?PHP
namespace Model;

/**
 * Azure资源条目虚拟机角色端口模型
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-28
 */
class ResItemVmdRolePort extends BaseModel
{
    /**
     * 表名称
     *
     * @var string
     */
    protected $table = 'azure_res_item_vmd_role_port';

    /**
     * 实例对象
     */
    public static $instance;

    /**
     * 插入单条数据
     *
     * @param int $roleId
     * @param string $name
     * @param string $protocol
     * @param int $port
     * @param int $localPort
     * @return int
     * @throws Exception
     */
    public function addData(
        $roleId,
        $name,
        $protocol,
        $port,
        $localPort
    ) {
        // prepare
        $sql = 'INSERT INTO `%s`(
                    `role_id`,
                    `name`,
                    `protocol`,
                    `port`,
                    `local_port`,
                    `create_time`
                )
                VALUES (
                    :role_id,
                    :name,
                    :protocol,
                    :port,
                    :local_port,
                    :create_time
                )';
        $sth = $this->pdo->prepare(sprintf($sql, $this->table));

        // bindvalue
        $sth->bindValue(':role_id', $roleId, \PDO::PARAM_INT);
        $sth->bindValue(':name', $name, \PDO::PARAM_STR);
        $sth->bindValue(':protocol', $protocol, \PDO::PARAM_STR);
        $sth->bindValue(':port', $port, \PDO::PARAM_INT);
        $sth->bindValue(':local_port', $localPort, \PDO::PARAM_INT);
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
