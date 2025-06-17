<?php
namespace app\common\model;

use app\common\traites\TraitModel;
use think\Model;

/**
 * 朋友圈评论模型
 * 对应数据库表：ntp_im_moment_comment
 * 存储动态的评论信息，支持评论回复
 */
class MomentComment extends Model
{
    use TraitModel;
    protected $name = 'im_moment_comment';  // 对应表 ntp_im_moment_comment

    
}
