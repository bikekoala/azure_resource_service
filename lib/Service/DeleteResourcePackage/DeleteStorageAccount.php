<?PHP
namespace Service\DeleteResourcePackage;

use Model\ResItemSa;

/**
 * 删除存储账户
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-04-09
 * @todo 只修改存储账户磁盘数，实际上并没有删除线上存储账户，
 *       因为无法控制实际的删除时间，后期可改进
 */
class DeleteStorageAccount extends Base
{
    /**
     * 执行
     *
     * @return void
     */
    public function run()
    {
        $saData = ResItemSa::single()->getAvailableData(
            $this->data['location'],
            $this->subId
        );
        ResItemSa::single()->updateDiskCount($saData['id'], -2);
    }
}
