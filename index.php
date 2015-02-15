<?PHP
/**
 * init
 */
require_once 'common/config.php';
require_once 'common/functions.php';
spl_autoload_register('autoload');
date_default_timezone_set('Asia/Taipei');

/**
 * call function
 */
if (isset($_GET['s'])) {
    $className = strtr('/Action' . ucfirst($_GET['s']), '/', '\\');
    (new $className)->run();
}
