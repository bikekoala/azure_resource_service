<?PHP    
namespace Service\UpdateResourcePackage;

use Service\AbstractService;
use Service\CreateResourcePackage\CreateVirtualNetwork;
use WindowsAzure\ServiceManagement\Models\UpdateRoleOptions;

/**
 * 基本服务
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-04-11
 */
class Base extends AbstractService
{
    /**
     * 处理流
     *
     * @var array
     */
    public static $processFlow = array(
        'Base'
    );

    /**
     * 执行
     *
     * @return void
     */
    public function run()
    {
        $requestId = $this->updateRole();

        $this->getAzureOperationStatus($requestId);
    }

    /**
     * 更新虚拟机配置
     *
     * @return string
     */
    public function updateRole()
    {
        $roleOptions = new UpdateRoleOptions();
        $roleOptions->setCsSubnetNames(array(CreateVirtualNetwork::SUBNET_NAME));
        if ( ! empty($this->data['internal_ip'])) {
            $roleOptions->setCsStaticVirtualNetworkIPAddress($this->data['internal_ip']);
        }
        if ( ! empty($this->data['ports'])) {
            $endpointList = array();
            foreach ($this->data['ports'] as $i => $port) {
                $endpointList[$i] = new UpdateRoleOptions();
                $endpointList[$i]->setCsNetworkEndpointLocalPort($port['local_port']);
                $endpointList[$i]->setCsNetworkEndpointName($port['name']);
                $endpointList[$i]->setCsNetworkEndpointProtocol($port['protocol']);
                if (isset($port['port'])) {
                    $endpointList[$i]->setCsNetworkEndpointPort($port['port']);
                }
            }
            $roleOptions->setCsNetworkEndpointList($endpointList);
        }

        $result = $this->callAzureService(
            'updateRole',
            $this->data['cloud_service_name'],
            $this->data['cloud_service_name'],
            $this->data['host_name'],
            $roleOptions
        );
        return $result['x-ms-request-id'];
    }
}
