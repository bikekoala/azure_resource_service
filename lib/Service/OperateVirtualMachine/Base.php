<?PHP    
namespace Service\OperateVirtualMachine;

use Service\AbstractService;
use WindowsAzure\ServiceManagement\Models\OperateRoleOptions;

/**
 * 基本服务
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-02-04
 */
class Base extends AbstractService
{
    /**
     * 处理流
     *
     * @var array
     */
    public static $processFlow = array(
        'Base',
    );

    /**
     * 操作类型
     */
    const OP_TYPE_START    = 'start';
    const OP_TYPE_SHUTDOWN = 'shutdown';
    const OP_TYPE_RESTART  = 'restart';

    /**
     * 云服务名称
     *
     * @var string
     */
    private $cloudServiceName;

    /**
     * 部署名称
     *
     * @var string
     */
    private $deploymentName;

    /**
     * 角色名称
     *
     * @var string
     */
    private $roleName;

    /**
     * 执行
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $this->cloudServiceName = $this->data['cloud_service_name'];
        $this->deploymentName = $this->data['cloud_service_name'];
        $this->roleName = $this->data['host_name'];

        switch ($this->data['operate_type']) {
            case self::OP_TYPE_START    : return $this->startRole();
            case self::OP_TYPE_SHUTDOWN : return $this->shutdownRole();
            case self::OP_TYPE_RESTART  : return $this->restartRole();
        }
    }

    /**
     * 启动角色实例（虚拟机）
     *
     * @return void
     * @throws Exception
     */
    private function startRole()
    {
        $result = $this->callAzureService(
            'startRole',
            $this->cloudServiceName,
            $this->deploymentName,
            $this->roleName
        );

        $this->getAzureOperationStatus($result['x-ms-request-id']);
    }

    /**
     * 关闭角色实例（虚拟机）
     *
     * options 之 setPostShutdownAction 说明：
     *     Stopped              闭虚拟机，但保留计算资源。
     *                          你将继续对已停止虚拟机使用的资源付费。
     *     StoppedDeallocated   闭虚拟机并释放计算资源。
     *                          你不再对此虚拟机使用的计算资源付费。
     *                          如果将静态虚拟网络 IP 地址分配给虚拟机，则保留。
     *                          有关详细信息，请参阅 获取角色 中的StaticVirtualNetworkIPAddress 
     *     如果未指定此元素，则默认操作为Stopped 
     *
     * @return void
     * @throws Exception
     */
    private function shutdownRole()
    {
        $options = new OperateRoleOptions();
        $options->setPostShutdownAction('StoppedDeallocated');

        $result = $this->callAzureService(
            'shutdownRole',
            $this->cloudServiceName,
            $this->deploymentName,
            $this->roleName,
            $options
        );

        $this->getAzureOperationStatus($result['x-ms-request-id']);
    }

    /**
     * 重启角色实例（虚拟机）
     *
     * @return void
     * @throws Exception
     */
    private function restartRole()
    {
        $result = $this->callAzureService(
            'restartRole',
            $this->cloudServiceName,
            $this->deploymentName,
            $this->roleName
        );

        $this->getAzureOperationStatus($result['x-ms-request-id']);
    }
}
