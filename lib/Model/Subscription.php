<?PHP
namespace Model;

/**
 * Azure资源操作模型
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-18
 */
class Subscription extends BaseModel
{
    /**
     * 表名称
     *
     * @var string
     */
    protected $table = 'azure_subscription';

    /**
     * 实例对象
     */
    public static $instance;

    /**
     * 根据GUID获取CERT私钥数据
     *
     * @param string $guid
     * @return string
     */
    public function getCertByGuid($guid)
    {
        $sql = sprintf(
            'SELECT `cert` FROM `%s` WHERE `guid`="%s"',
            $this->table,
            $guid
        );
        $sth = $this->pdo->prepare($sql);
        $sth->execute();
        $result = $sth->fetchColumn();
        return $result ? : '';
    }
}
