<?PHP
namespace Model;

/**
 * Azure资源条目虚拟机角色模型
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-28
 */
class ResItemVmdRole extends BaseModel
{
    /**
     * 表名称
     *
     * @var string
     */
    protected $table = 'azure_res_item_vmd_role';

    /**
     * 实例对象
     */
    public static $instance;

    /**
     * 通过订阅ID获取多条数据
     *
     * @param string $hostName
     * @param int $vmdId
     * @return array
     */
    public function getDataByHostNameAndVmdId($hostName, $vmdId)
    {
        $sql = sprintf(
            'SELECT * FROM `%s` WHERE `host_name`="%s" AND `vmd_id`=%d',
            $this->table,
            $hostName,
            $vmdId
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
     * @param int $vmdId
     * @param int $sizeId
     * @param int $imageId
     * @param string $osMediaLink
     * @param string $dataMediaLink
     * @param int $dataDiskSize
     * @param int $dataDiskLabel
     * @param string $internalIp
     * @param string $hostName
     * @param string $userName
     * @param string $userPassword
     * @param string $requestId
     * @return int
     * @throws Exception
     */
    public function addData(
        $vmdId,
        $sizeId,
        $imageId,
        $osMediaLink,
        $dataMediaLink,
        $dataDiskSize,
        $dataDiskLabel,
        $internalIp,
        $hostName,
        $userName,
        $userPassword,
        $requestId
    ) {
        // prepare
        $sql = 'INSERT INTO `%s`(
                    `vmd_id`,
                    `size_id`,
                    `image_id`,
                    `os_media_link`,
                    `data_media_link`,
                    `data_disk_size`,
                    `data_disk_label`,
                    `internal_ip`,
                    `host_name`,
                    `user_name`,
                    `user_password`,
                    `request_id`,
                    `create_time`
                )
                VALUES (
                    :vmd_id,
                    :size_id,
                    :image_id,
                    :os_media_link,
                    :data_media_link,
                    :data_disk_size,
                    :data_disk_label,
                    :internal_ip,
                    :host_name,
                    :user_name,
                    :user_password,
                    :request_id,
                    :create_time
                )';
        $sth = $this->pdo->prepare(sprintf($sql, $this->table));

        // bindvalue
        $sth->bindValue(':vmd_id', $vmdId, \PDO::PARAM_INT);
        $sth->bindValue(':size_id', $sizeId, \PDO::PARAM_INT);
        $sth->bindValue(':image_id', $imageId, \PDO::PARAM_INT);
        $sth->bindValue(':os_media_link', $osMediaLink, \PDO::PARAM_STR);
        $sth->bindValue(':data_media_link', $dataMediaLink, \PDO::PARAM_STR);
        $sth->bindValue(':data_disk_size', $dataDiskSize, \PDO::PARAM_INT);
        $sth->bindValue(':data_disk_label', $dataDiskLabel, \PDO::PARAM_INT);
        $sth->bindValue(':internal_ip', $internalIp, \PDO::PARAM_STR);
        $sth->bindValue(':host_name', $hostName, \PDO::PARAM_STR);
        $sth->bindValue(':user_name', $userName, \PDO::PARAM_STR);
        $sth->bindValue(':user_password', $userPassword, \PDO::PARAM_STR);
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
