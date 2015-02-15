<?PHP
namespace Action;

use Rule\Forge as RuleForge;

/**
 * 操作虚拟机
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-02-04
 */
class OperateVirtualMachine extends AbstractAction
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
            'cloud_service_name' => array(
                'require'        => true,
                'rules'          => array('Field/CloudServiceName')
            ),
            'host_name'          => array(
                'require'        => true,
                'rules'          => array('Field/HostName')
            ),
            'operate_type'       => array(
                'require'        => true,
                'rules'          => array(
                    'Atom/Enum'  => array(
                        array(
                            'start',
                            'shutdown',
                            'restart'
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
