<?PHP
$p = array(
    'subscription_id' => '5da09225-d2f5-4399-819c-1ba4357b3d6e',
    'resources'  => array(
        array(
            'cloud_service_name' => 'youaremysunshine',
            'host_name'          => 'blowininthe01',
        ),
        array(
            'cloud_service_name' => 'youaremysunshine',
            'host_name'          => 'blowininthe02',
        ),
    ) 
);
$api = 'http://127.0.0.1:8101/QueryResourcePackage';
include 'common.php';
