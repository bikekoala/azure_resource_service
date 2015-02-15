<?PHP    
namespace Service;

use Model\ResOp;
use Model\ResItem;
use Service\AbstractService;

/**
 * 调用服务
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-02-06
 */
class CallbackService extends AbstractService
{
    /**
     * 任务条目数据
     *
     * @var array
     */
    protected $items;

    /**
     * 构造方法
     *
     * @param int $opId
     * @return void
     */
    public function __construct($opId)
    {
        // set items
        $this->items = ResItem::single()->getDatasByOpId($opId);
        foreach ($this->items as &$item) {
            $item['data'] = unserialize($item['data']);
        }

        // set sub id
        $this->setSubId(ResOp::single()->getSubId($opId));
    }

    /**
     * 执行
     *
     * @return void
     */
    public function run()
    {
    }

    /**
     * 获取通用的回调参数
     *
     * @return array
     */
    public function getCommonParams()
    {
        $params = array();
        foreach ($this->items as $i => $item) {
            $status = ResItem::STATUS_SUCCESS == $item['status'];
            $message = $status ? 'Succeed' : $item['message'];
            $params[$i] = [
                'cloud_service_name' => $item['data']['cloud_service_name'],
                'host_name'          => $item['data']['host_name'],
                'status'             => $status,
                'message'            => $message
            ];
        }
        return $params;
    }

    /**
     * 实时获取虚拟机资源信息
     *
     * @return array
     */
    public function getAzureResourceForVm()
    {
        // 统计云服务名称与主机名称的对应关系
        $names = array();
        foreach ($this->items as $item) {
            $names[$item['data']['cloud_service_name']][] =
                $item['data']['host_name'];
        }

        // 根据云服务名称获取部署信息
        $res = array();
        foreach ($names as $cloudServiceName => $hostNames) {
            $deployment = $this->serviceManagement->getDeployment(
                $cloudServiceName
            );

            foreach ($hostNames as $hostName) {
                foreach ($deployment->RoleInstanceList->RoleInstance as $role) {
                    if ($hostName != $role->RoleName) continue;
                    $item = array();

                    $item['cloud_service_name'] = $cloudServiceName;
                    $item['host_name'] = $role->RoleName;
                    $item['host_status'] = $role->InstanceStatus;
                    $item['power_state'] = $role->PowerState;

                    $res[$cloudServiceName][$hostName] = $item;
                }

                foreach ($deployment->RoleList->Role as $role) {
                    if ($hostName != $role->RoleName) continue;
                    $item = array();

                    $cs = $role->ConfigurationSets->ConfigurationSet;
                    if ('NetworkConfiguration' === $cs->ConfigurationSetType) {
                        $ports = array();
                        if (isset($cs->InputEndpoints)) {
                            $endpoint = $cs->InputEndpoints->InputEndpoint;
                            if (isset($endpoint->Name)) {
                                $ports[0]['name'] = $p->Name;
                                $ports[0]['protocol'] = $p->Protocol;
                                $ports[0]['port'] = $p->Port;
                                $ports[0]['local_port'] = $p->LocalPort;
                            } else {
                                foreach ($endpoint as $i => $p) {
                                    $ports[$i]['name'] = $p->Name;
                                    $ports[$i]['protocol'] = $p->Protocol;
                                    $ports[$i]['port'] = $p->Port;
                                    $ports[$i]['local_port'] = $p->LocalPort;
                                }
                            }
                        }
                        $item['ports'] = $ports;
                    }
                    $item['internal_ip'] = $cs->StaticVirtualNetworkIPAddress;

                    $dataDisk = $role->DataVirtualHardDisks->DataVirtualHardDisk;
                    $item['data_disk_name'] = $dataDisk->DiskName;
                    $item['data_disk_capacity'] = $dataDisk->LogicalDiskSizeInGB;
                    $item['data_disk_media_link'] = $dataDisk->MediaLink;

                    $osDisk = $role->OSVirtualHardDisk;
                    $item['os_disk_name'] = $osDisk->DiskName;
                    $item['os_disk_media_link'] = $osDisk->MediaLink;
                    $item['os_name'] = $osDisk->OS;
                    $item['os_source_image_name'] = $osDisk->SourceImageName;

                    $item['size_name'] = $role->RoleSize;

                    $res[$cloudServiceName][$role->RoleName] = array_merge(
                        $res[$cloudServiceName][$hostName],
                        $item
                    );
                }
            }
        }
        return $res;
    }
}
