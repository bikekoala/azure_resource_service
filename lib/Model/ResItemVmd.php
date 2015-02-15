<?PHP
namespace Model;

/**
 * Azure资源条目虚拟机部署模型
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-28
 */
class ResItemVmd extends BaseModel
{
    /**
     * 表名称
     *
     * @var string
     */
    protected $table = 'azure_res_item_vmd';

    /**
     * 实例对象
     */
    public static $instance;

    /**
     * 通过部署名称与云服务ID获取表ID
     *
     * @param string $name
     * @param int $csId
     * @return int
     */
    public function getIdByNameAndCsId($name, $csId)
    {
        $sql = sprintf(
            'SELECT `id` FROM `%s` WHERE `name`="%s" AND `cs_id`=%d',
            $this->table,
            $name,
            $csId
        );
        $sth = $this->pdo->prepare($sql);
        $sth->setFetchMode(\PDO::FETCH_ASSOC); 
        $sth->execute();
        return (int) $sth->fetchColumn();
    }

    /**
     * 插入单条数据
     *
     * @param int $itemId
     * @param int $saId
     * @param int $vnId
     * @param int $csId
     * @param string $name
     * @param string $label
     * @return int
     * @throws Exception
     */
    public function addData(
        $itemId,
        $saId,
        $vnId,
        $csId,
        $name,
        $label
    ) {
        // prepare
        $sql = 'INSERT INTO `%s`(
                    `item_id`,
                    `sa_id`,
                    `vn_id`,
                    `cs_id`,
                    `name`,
                    `label`,
                    `create_time`
                )
                VALUES (
                    :item_id,
                    :sa_id,
                    :vn_id,
                    :cs_id,
                    :name,
                    :label,
                    :create_time
                )';
        $sth = $this->pdo->prepare(sprintf($sql, $this->table));

        // bindvalue
        $sth->bindValue(':item_id', $itemId, \PDO::PARAM_INT);
        $sth->bindValue(':sa_id', $saId, \PDO::PARAM_INT);
        $sth->bindValue(':vn_id', $vnId, \PDO::PARAM_INT);
        $sth->bindValue(':cs_id', $csId, \PDO::PARAM_INT);
        $sth->bindValue(':name', $name, \PDO::PARAM_STR);
        $sth->bindValue(':label', $label, \PDO::PARAM_STR);
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
