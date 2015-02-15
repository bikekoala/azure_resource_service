<?PHP
namespace Action;

use Rule\Forge as RuleForge;

/**
 * 创建资源包操作
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-16
 */
class CreateResourcePackage extends AbstractAction
{
    /**
     * 开火！
     *
     * @return void
     * @throws Exception
     */
    public function fire()
    {
        $this->checkCustomParams();
        $opId = $this->initAsyncOperation();

        static::outputJson(true, 'Accepd', $opId);
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
            'cloud_service_name'  => array(
                'require'         => true,
                'rules'           => array('Field/CloudServiceName')
            ),
            'host_name'           => array(
                'require'         => true,
                'rules'           => array('Field/HostName')
            ),
            'size_id'             => array(
                'require'         => true,
                'rules'           => array('Field/SizeId')
            ),
            'image_id'            => array(
                'require'         => true,
                'rules'           => array('Field/ImageId')
            ),
            'data_disk_capacity'  => array(
                'require'         => true,
                'rules'           => array('Atom/Int' => array(1, 1023))
            ),
            'internal_ip'         => array(
                'require'         => true,
                'rules'           => array('Atom/Ip')
            ),
            'user_name'           => array(
                'require'         => true,
                'rules'           => array('Atom/Str' => array(1, 100))
            ),
            'user_password'       => array(
                'require'         => true,
                'rules'           => array('Field/UserPassword')
            ),
            'location'            => array(
                'require'         => true,
                'rules'           => array('Field/Location')
            ),
            'ports'               => array(
                'require'         => false,
                'rules'           => array('Atom/Arr'),
                'nodes'           => array(
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
