<?PHP
namespace Action;

use Rule\Forge as RuleForge;

/**
 * 资源查看操作
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-02-06
 */
class QueryResourcePackage extends AbstractAction
{
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
}
