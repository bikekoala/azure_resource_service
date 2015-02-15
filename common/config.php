<?PHP
/**
 * 基本常量
 */
define('APP_PATH', realpath(__DIR__ . '/../'));
define('LIB_PATH', APP_PATH . '/lib/');
define('ADDONS_PATH', APP_PATH . '/addons/');
define('SCRIPT_PATH', APP_PATH . '/script/');
define('AZURE_SDK_PATH', ADDONS_PATH . '/azure_sdk/');
define('AZURE_PEM_PATH', '/tmp/');

/**
 * 项目自定义配置
 */
define('PDO_URI', 'mysql://root:root@ucw.COM@127.0.0.1:3306?dbname=ucw_cmdb');
