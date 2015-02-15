<?PHP
$p = array(
    'subscription_id' => '5da09225-d2f5-4399-819c-1ba4357b3d6e',
    'callback_url'    => 'http://127.0.0.1:8095',
    'resources'  => array(
        array(
            'cloud_service_name' => 'youaremysunshine',
            'host_name'          => 'blowininthe01',
            'operate_type'       => 'start',
        ),
        array(
            'cloud_service_name' => 'youaremysunshine',
            'host_name'          => 'blowininthe01',
            'operate_type'       => 'shutdown',
        ),
        array(
            'cloud_service_name' => 'youaremysunshine',
            'host_name'          => 'blowininthe02',
            'operate_type'       => 'restart',
        ),
        array(
            'cloud_service_name' => 'youaremysunshine',
            'host_name'          => 'blowininthe01',
            'operate_type'       => 'shutdown',
        ),
    ) 
);
$api = 'http://127.0.0.1:8092/OperateVirtualMachine';
include 'common.php';
