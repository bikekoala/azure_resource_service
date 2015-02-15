<?PHP
namespace Model;

/**
 * Azure资源条目云虚拟网络子网模型
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-26
 */
class ResItemVnSubnet extends BaseModel
{
    /**
     * 表名称
     *
     * @var string
     */
    protected $table = 'azure_res_item_vn_subnet';

    /**
     * 实例对象
     */
    public static $instance;

    /**
     * 通过虚拟网络表ID获取多条数据
     *
     * @param string $vnId
     * @return array
     */
    public function getDatasByVnId($vnId)
    {
        $sql = sprintf(
            'SELECT * FROM `%s` WHERE `vn_id`="%d"',
            $this->table,
            $vnId
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
     * @param int $vnId
     * @param string $name
     * @param string $addressPrefix
     * @return void
     * @throws Exception
     */
    public function addData(
        $vnId,
        $name,
        $addressPrefix
    ) {
        // prepare
        $sql = 'INSERT INTO `%s`(
                    `vn_id`,
                    `name`,
                    `address_prefix`,
                    `create_time`
                )
                VALUES (
                    :vn_id,
                    :name,
                    :address_prefix,
                    :create_time
                )';
        $sth = $this->pdo->prepare(sprintf($sql, $this->table));

        // bindvalue
        $sth->bindValue(':vn_id', $vnId, \PDO::PARAM_INT);
        $sth->bindValue(':name', $name, \PDO::PARAM_STR);
        $sth->bindValue(':address_prefix', $addressPrefix, \PDO::PARAM_STR);
        $sth->bindValue(':create_time', date('Y-m-d H:i:s'), \PDO::PARAM_STR);

        // execute
        try {
            return $sth->execute();
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }
}
