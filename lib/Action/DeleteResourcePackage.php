<?PHP
namespace Action;

use Rule\Forge as RuleForge;

/**
 * 删除资源包
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-04-08
 */
class DeleteResourcePackage extends AbstractAction
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
