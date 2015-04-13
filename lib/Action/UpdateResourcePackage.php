<?PHP
namespace Action;

use Rule\Forge as RuleForge;

/**
 * 更新资源包操作
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-04-11
 */
class UpdateResourcePackage extends AbstractAction
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
            ),
            'internal_ip'        => array(
                'require'        => false,
                'rules'          => array('Atom/Ip')
            ),
            'ports'              => array(
                'require'        => false,
                'rules'          => array('Atom/Arr'),
                'nodes'          => array(
                    array(
                        'name'        => array(
                            'require' => true,
                            'rules'   => array('Field/PortName')
                        ),
                        'protocol'    => array(
                            'require' => true,
                            'rules'   => array('Field/PortProtocol')
                        ),
                        'port'        => array(
                            'require' => false,
                            'rules'   => array('Atom/Int' => array(1, 65535))
                        ),
                        'local_port'  => array(
                            'require' => true,
                            'rules'   => array('Atom/Int' => array(1, 65535))
                        )
                    )
                )
            )
        );

        foreach ($this->resources as $res) {
            RuleForge::validate($res, $ruleList);
        }
    }
}
