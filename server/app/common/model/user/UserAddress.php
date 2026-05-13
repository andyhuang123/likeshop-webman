<?php
namespace app\common\model\user;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class UserAddress extends BaseModel
{
    use SoftDelete;
    
    protected $name = 'user_address';
    protected $deleteTime = 'delete_time';
}
