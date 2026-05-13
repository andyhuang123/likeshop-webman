<?php
namespace app\common\model\marketing;

use app\common\model\BaseModel;
use app\common\service\FileService;
use think\model\concern\SoftDelete;

class BlindBox extends BaseModel
{
    use SoftDelete;

    protected $name = 'blind_box';
    protected $deleteTime = 'delete_time';

    // 关联奖品
    public function details()
    {
        return $this->hasMany(BlindBoxDetail::class, 'blind_box_id', 'id');
    }
}
