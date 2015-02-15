<?PHP
$p = array(
    'subscription_id' => '5da09225-d2f5-4399-819c-1ba4357b3d6e',
    'callback_url'    => 'http://127.0.0.1:8095',
    'resources'  => array(
        array(
            'cloud_service_name'   => 'youaremysunshine',
            'host_name'            => 'blowininthe01',
            'size_id'              => 1,
            'image_id'             => 15, // 15:linux, 8:windows
            'data_disk_capacity'   => 10,
            'internal_ip'          => '10.0.0.10',
            'user_name'            => 'popfeng',
            'user_password'        => 'popfeng@ucw.COM',
            'location'             => 'China North',
            'ports'                => array(
                array(
                    'name'         => 'SSH',
                    'protocol'     => 'TCP',
                    'port'         => 22,
                    'local_port'   => 22,
                ),
                array(
                    'name'         => 'HTTP',
                    'protocol'     => 'TCP',
                    'port'         => 8080,
                    'local_port'   => 80,
                ),
            )
        ),
        array(
            'cloud_service_name'   => 'youaremysunshine',
            'host_name'            => 'blowininthe02',
            'size_id'              => 1,
            'image_id'             => 8, // 15:linux, 8:windows
            'data_disk_capacity'   => 1023,
            'internal_ip'          => '10.0.0.11',
            'user_name'            => 'popfeng',
            'user_password'        => 'popfeng@ucw.COM',
            'location'             => 'China North',
            'ports'                => array(
                array(
                    'name'         => 'HTTP',
                    'protocol'     => 'TCP',
                    'local_port'   => 80,
                ),
            )
        ),
    ) 
);
$api = 'http://127.0.0.1:8092/CreateResourcePackage';
include 'common.php';
