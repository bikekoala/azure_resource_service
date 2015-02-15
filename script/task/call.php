<?PHP
/**
 * 任务调用脚本
 */
include realpath(__DIR__ . '/../../') . '/index.php';

$opId = isset($argv[1]) ? $argv[1] : 0;
if(0 === $opId) {
    exit('Invalid op id.' . PHP_EOL);
}
(new Task\Manager($opId))->dispatch();
