<?php
namespace app\common\model;

use app\common\traites\TraitModel;
use think\Model;

/**
 * 朋友圈点赞模型
 * 对应数据库表：ntp_im_moment_like
 * 存储用户对动态的点赞信息
 */
class MomentLike extends Model
{
    use TraitModel;
    protected $name = 'im_moment_like';  // 对应表 ntp_im_moment_like

    
}
