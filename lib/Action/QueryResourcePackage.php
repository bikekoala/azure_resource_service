<?PHP
namespace Action;

use Rule\Forge as RuleForge;
use Service\CallbackService;

/**
 * 资源查看操作（实时接口）
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-02-06
 */
class QueryResourcePackage extends AbstractAction
{
    /**
     * 奔跑吧，兄弟！
     *
     * @return void
     */
    public function run()
    {
        try {
            $this->checkHeader();
            $this->checkCommonParams(false);
            $this->checkCustomParams();

            $result = $this->getResponseResult();
            static::outputJson(true, '', $result);
        } catch (\Exception $e) {
            static::outputJson(false, $e->getMessage());
        }
    }

    /**
     * 检查自定义参数是否合法
     *
     * @return void
     * @throws Exception
     */
    public function checkCustomParams()
    {
        $ruleList = array(
            'cloud_service_name' => array(
                'require'        => true,
                'rules'          => array('Field/CloudServiceName')
            ),
            'host_name'          => array(
                'require'        => true,
                'rules'          => array('Field/HostName')
            )
        );

        foreach ($this->resources as $res) {
            RuleForge::validate($res, $ruleList);
        }
    }

    /**
     * 获取响应结果
     *
     * @return array
     */
    private function getResponseResult()
    {
        $service = new CallbackService;
        $service->setSubId($this->subId);
        $res = $service->getAzureVmResource($this->resources);

        $result = array();
        foreach ($this->resources as $i => $v) {
            if (isset($res[$v['cloud_service_name']][$v['host_name']])) {
                $vm = $res[$v['cloud_service_name']][$v['host_name']];
                $v['status']      = true;
                $v['message']     = 'Succeed';
                $v['host_status'] = $vm['host_status'];
                $v['power_state'] = $vm['power_state'];
            } else {
                $v['status']      = false;
                $v['message']     = '没有查询到指定虚拟机信息';
            }
            $result[$i] = $v;
        }

        return $result;
    }
}
