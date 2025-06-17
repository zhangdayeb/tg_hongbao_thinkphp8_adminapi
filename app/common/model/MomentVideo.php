<?php
namespace app\common\model;

use app\common\traites\TraitModel;
use think\Model;

/**
 * 朋友圈视频表模型
 * 表名：ntp_im_moment_video
 */
class MomentVideo extends Model
{
    use TraitModel;

    protected $name = 'im_moment_video';
}
