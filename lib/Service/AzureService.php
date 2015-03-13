<?PHP
namespace Service;

use Model\ResStatus;
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\ServiceManagement\ServiceManagementRestProxy;

/**
 * Azure服务
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-23
 */
class AzureService
{
    /**
     * 操作状态
     */
    const STATUS_IN_PROGRESS = 'InProgress';
    const STATUS_SUCCEEDED   = 'Succeeded';
    const STATUS_FAILED      = 'Failed';

    /**
     * 初始化Azure服务
     *
     * @param string $subId
     * @param string $certPemStr
     * @return void
     */
    public static function initAzureService($subId, $certPemStr)
    {
        // include azure sdk
        require AZURE_SDK_PATH . '/WindowsAzure/WindowsAzure.php';

        // create azure service builder
        $certPemPath = AZURE_PEM_PATH . $subId;
        $status = pfx2pem($certPemStr, $certPemPath);
        if (true === $status) {
            return ServicesBuilder::getInstance()->
                createServiceManagementService(sprintf(
                    'SubscriptionID=%s;CertificatePath=%s',
                    $subId,
                    $certPemPath
                )
            );
        } else {
            throw new \Exception($status);
        }
    }

    /**
     * 轮询异步操作的状态
     *
     * @param ServiceManagementRestProxy $serviceManagement
     * @param string $requestId
     * @return void
     * @throws Exception
     */
    public static function getOperationStatusUntilTheEnd(
        ServiceManagementRestProxy $serviceManagement,
        $requestId
    ) {
        while (TRUE) {
            $result = obj2arr($serviceManagement->getOperationStatus($requestId));
            ResStatus::single()->saveData(
                $requestId,
                $result['Status'],
                isset($result['Error']['Code']) ? $result['Error']['Code'] : '',
                isset($result['Error']['Message']) ? $result['Error']['Message'] : ''
            );

            switch ($result['Status']) {
                case self::STATUS_IN_PROGRESS :
                    sleep(5);
                    continue;
                case self::STATUS_FAILED :
                    throw new \Exception($result['Error']['Message']);
                case self::STATUS_SUCCEEDED :
                default :
                    break 2;
            }
        }
    }
}
