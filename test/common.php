<?PHP
include realpath(__DIR__ . '/../') . '/addons/Curl.php';

try {
    $curl = new Curl($api);
    $curl->setopt(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $result = $curl->rest(json_encode($p), Curl::MD_POST);
    print_r($result);
} catch (\Exception $e) {
    echo strip_tags(str_replace('<br />', PHP_EOL, $e->getMessage())), PHP_EOL;
}
