<?PHP
$p = array(
    'subscription_id' => '5da09225-d2f5-4399-819c-1ba4357b3d6e',
    'callback_url'    => 'http://127.0.0.1:8301',
    'resources'  => array(
        array(
            'cloud_service_name' => 'youaremysunshine',
            'host_name'          => 'blowininthe01',
            'internal_ip'        => '10.10.10.10',
            'ports'              => array(
                array(
                    'name'       => 'SSH',
                    'protocol'   => 'TCP',
                    'port'       => 22,
                    'local_port' => 22,
                ),
                array(
                    'name'       => 'HTTP',
                    'protocol'   => 'TCP',
                    'port'       => 8080,
                    'local_port' => 80,
                ),
            )
        ),
    ) 
);
$api = 'http://127.0.0.1:8101/UpdateResourcePackage';
include 'common.php';
