<?PHP
namespace Rule\Field;

use Rule\Field\SizeId;
use Model\ResVmImage;

/**
 * 镜像ID验证类
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-31
 */
class ImageId extends SizeId
{
    /**
     * 检查该ID是否存在于数据库中
     *
     * @param int $id
     * @param string $fieldName
     * @return void
     * @throws Exception
     */
    protected static function checkIfDbExists($id, $fieldName)
    {
        $data = ResVmImage::single()->getData($id);
        if (empty($data)) {
            self::throws($fieldName, sprintf(
                '数据库中没有检索到字段值为 %d 的记录',
                $id
            ));
        }
    }
}
