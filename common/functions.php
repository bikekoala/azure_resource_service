<?PHP

/**
 * 自动加载
 *
 * @param string $class_name
 * @return void
 */
function autoload($class_name) {
    $class_name = ltrim($class_name, '\\');
    $file_name  = '';
    $namespace = '';
    if ($last_ns_pos = strrpos($class_name, '\\')) {
        $namespace = substr($class_name, 0, $last_ns_pos);
        $class_name = substr($class_name, $last_ns_pos + 1);
        $file_name  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $file_name = LIB_PATH . $file_name . str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';
    if (file_exists($file_name)) {
        require $file_name;
    }
}

/**
 * 微软pfx文件转换为openssl的pem文件
 *
 * @param string $pfx_str Base64编码的pfx文件字符串
 * @param string $pem_path 待输出的pem文件路径
 * @return bool|string
 */
function pfx2pem($pfx_str, $pem_path) {
    $password = '';
    $results = array();
    $worked = openssl_pkcs12_read(base64_decode($pfx_str), $results, $password);
    if ($worked) {
        file_put_contents($pem_path, implode(PHP_EOL, $results));
        return true;
    } else {
        return openssl_error_string();
    }
}

/**
 * 生成随机字符串
 *
 * @param int       $length  要生成的随机字符串长度
 * @param string    $type    随机码类型：
                               0，数字+大小写字母
                               1，数字
                               2，小写字母
                               3，大写字母
                               4，特殊字符
                               -1，数字+大小写字母+特殊字符
 * @return string
 */
function rand_code($length = 5, $type = 0) {
    $arr = array(
        1 => '0123456789',
        2 => 'abcdefghijklmnopqrstuvwxyz',
        3 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        4 => '~@#$%^&*(){}[]|',
    );
    if ($type == 0) {
        array_pop($arr);
        $string = implode('', $arr);
    } elseif ($type == '-1') {
        $string = implode('', $arr);
    } else {
        $string = $arr[$type];
    }
    $count = strlen($string) - 1;
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $string[rand(0, $count)];
    }
    return $code;
}

/**
 * PHP对象转换为数组
 *
 * @param Object $obj
 * @return array
 */
function obj2arr($obj) {
    if (is_object($obj)) {
        $obj = (array) $obj;
        $obj = obj2arr($obj);
    } elseif (is_array($obj)) {
        foreach ($obj as $k => $v) {
            $obj[$k] = obj2arr($v);
        }
    }
    return $obj;
}

/**
 * 获取下一个IP地址
 *
 * @param string $ip
 * @param array $ignore_last_parts
 * @return string
 */
function get_next_ip($ip, $ignore_last_parts = array()) {
    while (true) {
        $ip = long2ip(ip2long($ip) + 1);
        $octets = explode('.', $ip);
        $last_part = $octets[3];
        if ( ! in_array($last_part, $ignore_last_parts)) {
            return $ip;
        }
    }
}

/**
 * URL回调方法
 *
 * @param string $url
 * @param mixed $data
 * @return bool
 */
function callback_url($url, $data = null) {
    include ADDONS_PATH . 'Curl.php';
    $curl = new Curl($url);
    $curl->setopt(CURLOPT_TIMEOUT, 15);
    $curl->setopt(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $result = $curl->post(json_encode($data, JSON_UNESCAPED_UNICODE));
    return 'ok' === $result;
}
