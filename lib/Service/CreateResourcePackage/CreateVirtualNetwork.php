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
     * VN表ID
     *
     * @var int
     */
    private $vnId;

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
            $this->saveData();

            $this->createVn();

            $this->getAzureOperationStatus($this->requestId);

            $this->updateData();
        }
    }

    /**
     * 检查虚拟网络是否已经存在
     *
     * @return bool
     */
    private function checkIfNameExists()
    {
        // 查询本地记录
        $data = ResItemVn::single()->getDataByNameAndSubId(
            $this->extData['name'],
            $this->subId
        );

        // 检查
        if ( ! empty($data)) {
            // 持续检查记录创建状态，直至状态为成功
            for ($i=10; $i>0; $i--) {
                $createStatus = ResItemVn::single()->getCreateStatusById($data['id']);
                if (ResItemVn::STATUS_CREATING == $createStatus) {
                    sleep(10);
                } else break;
            }

            // 线上检查名称是否存在
            $result = $this->serviceManagement->listVirtualNetworkSites();
            if ($result) {
                $vns = isset($result->VirtualNetworkSite->Name) ?
                    array($result->VirtualNetworkSite) :
                    $result->VirtualNetworkSite;

                foreach ($vns as $site) {
                    if ($this->extData['name'] == $site->Name) {
                        $subnets = isset($site->Subnets->Subnet->Name) ?
                            array($site->Subnets->Subnet) :
                            $site->Subnets->Subnet;
                        foreach ($subnets as $subnet) {
                            foreach ($this->extData['subnet'] as $sv) {
                                if ($sv['name'] == $subnet->Name) {
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
            return false;
        } else return false;
    }

    /**
     * 创建虚拟网络
     *
     * @return void
     */
    private function createVn()
    {
        // get datas from database
        $vns = ResItemVn::single()->getDatasBySubId($this->subId);
        foreach ($vns as &$v) {
            $v['subnet'] = ResItemVnSubnet::single()->getDatasByVnId($v['id']);
        }

        // call azure service
        $vnsOptions = array();
        foreach ($vns as $i) {
            $subnetOptions = array();
            foreach ($i['subnet'] as $s) {
                $subnetOptions[$s['name']] = new SetNetworkConfigurationOptions();
                $subnetOptions[$s['name']]->setVnsSubnetName($s['name']);
                $subnetOptions[$s['name']]->setVnsSubnetAddressPrefix($s['address_prefix']);
            }
            $vnsOptions[$i['name']] = new SetNetworkConfigurationOptions();
            $vnsOptions[$i['name']]->setVnsName($i['name']);
            $vnsOptions[$i['name']]->setVnsLocation($i['location']);
            $vnsOptions[$i['name']]->setVnsAdressSpaceAddressPrefix($i['address_prefix']);
            $vnsOptions[$i['name']]->setVnsSubnetList($subnetOptions);
        }
        $result = $this->callAzureService('setNetworkConfiguration', $vnsOptions);
        $this->requestId = $result['x-ms-request-id'];
    }

    /**
     * 保存数据
     *
     * @return int
     */
    private function saveData()
    {
        try {
            $pdo = BaseModel::single()->pdo;
            $pdo->beginTransaction();

            $this->vnId = ResItemVn::single()->addData(
                $this->itemId,
                $this->subId,
                $this->extData['name'],
                $this->extData['location'],
                $this->extData['address_prefix'],
                '',
                ResItemVn::STATUS_CREATING
            );
            foreach ($this->extData['subnet'] as $subnet) {
                ResItemVnSubnet::single()->addData(
                    $this->vnId,
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
     * 更新请求结果数据
     *
     * @return void
     */
    private function updateData()
    {
        ResItemVn::single()->updateDataById(
            $this->vnId,
            $this->requestId,
            ResItemVn::STATUS_CREATED
        );
    }

    /**
     * 初始化扩展数据
     *
     * @return array
     */
    private function initExtData()
    {
        $data = array(
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
        $this->extData = $data;
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
