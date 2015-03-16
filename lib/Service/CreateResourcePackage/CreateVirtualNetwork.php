<?PHP    
namespace Service\CreateResourcePackage;

use Model\BaseModel;
use Model\ResItemVn;
use Model\ResItemVnSubnet;
use WindowsAzure\ServiceManagement\Models\SetNetworkConfigurationOptions;

/**
 * 创建虚拟网络
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-26
 */
class CreateVirtualNetwork extends Base
{
    /**
     * 虚拟网络名称前缀
     */
    const NAME_PREFIX = 'ucw-vn-';

    /**
     * 虚拟网络地址前缀（ADDRESS SPACE + CIDR）
     */
    const ADDRESS_PREFIX = '10.0.0.0/8';

    /**
     * 虚拟网络子网名称
     */
    const SUBNET_NAME = 'Subnet-1';

    /**
     * 虚拟网络子网地址前缀（ADDRESS SPACE + CIDR）
     * ADDRESS SPACE 在 ADDRESS_PREFIX 的 ADDRESS SPACE 其中
     * CIDR要大于ADDRESS_PREFIX的CIDR
     */
    const SUBNET_ADDRESS_PREFIX = '10.0.0.0/8';

    /**
     * 扩展数据
     *
     * @var array
     */
    private $extData;

    /**
     * 请求ID
     *
     * @var string
     */
    private $requestId;

    /**
     * 执行
     *
     * @return void
     */
    public function run()
    {
        $this->initExtData();

        if ( ! $this->checkIfNameExists()) {
            $this->createVn();

            $this->getAzureOperationStatus($this->requestId);
        }

        $this->saveDatas();
    }

    /**
     * 检查虚拟网络是否已经存在
     *
     * @return bool
     */
    private function checkIfNameExists()
    {
        $result = $this->serviceManagement->listVirtualNetworkSites();
        if ($result) {
            $vns = isset($result->VirtualNetworkSite->Name) ?
                array($result->VirtualNetworkSite) :
                $result->VirtualNetworkSite;

            foreach ($vns as $site) {
                if ($this->extData['custom']['name'] == $site->Name) {
                    $subnets = isset($site->Subnets->Subnet->Name) ?
                        array($site->Subnets->Subnet) :
                        $site->Subnets->Subnet;
                    foreach ($subnets as $subnet) {
                        foreach ($this->extData['custom']['subnet'] as $sv) {
                            if ($sv['name'] == $subnet->Name) {
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * 创建虚拟网络
     *
     * @return void
     */
    private function createVn()
    {
        $vnsOptions = array();
        foreach ($this->extData['vns'] as $v) {
            $subnetOptions = array();
            foreach ($v['subnet'] as $s) {
                $subnetOptions[$s['name']] = new SetNetworkConfigurationOptions();
                $subnetOptions[$s['name']]->setVnsSubnetName($s['name']);
                $subnetOptions[$s['name']]->setVnsSubnetAddressPrefix($s['address_prefix']);
            }
            $vnsOptions[$v['name']] = new SetNetworkConfigurationOptions();
            $vnsOptions[$v['name']]->setVnsName($v['name']);
            $vnsOptions[$v['name']]->setVnsLocation($v['location']);
            $vnsOptions[$v['name']]->setVnsAdressSpaceAddressPrefix($v['address_prefix']);
            $vnsOptions[$v['name']]->setVnsSubnetList($subnetOptions);
        }
        $result = $this->callAzureService('setNetworkConfiguration', $vnsOptions);
        $this->requestId = $result['x-ms-request-id'];
    }

    /**
     * 保存数据
     *
     * @return void
     */
    private function saveDatas()
    {
        // check if exists
        $customData = $this->extData['custom'];
        $data = ResItemVn::single()->getDataByNameAndSubId(
            $customData['name'],
            $this->subId
        );
        if ( ! empty($data)) return;

        // save datas
        try {
            $pdo = BaseModel::single()->pdo;
            $pdo->beginTransaction();

            $id = ResItemVn::single()->addData(
                $this->itemId,
                $this->subId,
                $customData['name'],
                $customData['location'],
                $customData['address_prefix'],
                $this->requestId
            );
            foreach ($customData['subnet'] as $subnet) {
                ResItemVnSubnet::single()->addData(
                    $id,
                    $subnet['name'],
                    $subnet['address_prefix']
                );
            }
            
            $pdo->commit();
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw new \Exception($e->getMessage());
        }

        
    }

    /**
     * 初始化扩展数据
     *
     * @return array
     */
    private function initExtData()
    {
        // custom data
        $custom = array(
            'name'           => self::getName($this->data['location']),
            'location'       => $this->data['location'],
            'address_prefix' => self::ADDRESS_PREFIX,
            'subnet'         => array(
                array(
                    'name'           => self::SUBNET_NAME,
                    'address_prefix' => self::SUBNET_ADDRESS_PREFIX
                )
            )
        );

        // get datas from database
        $vns = ResItemVn::single()->getDatasBySubId($this->subId);
        foreach ($vns as &$v) {
            $v['subnet'] = ResItemVnSubnet::single()->getDatasByVnId($v['id']);
        }
        $vns[] = $custom;

        $this->extData = compact('vns', 'custom');
    }

    /**
     * 获取虚拟网络名称
     *
     * @param string $location
     * @return string
     */
    private static function getName($location)
    {
        return self::NAME_PREFIX . strtolower(str_replace(' ', '', $location));
    }
}
