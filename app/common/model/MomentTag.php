<?php
namespace app\common\model;

use app\common\traites\TraitModel;
use think\Model;

/**
 * 朋友圈动态与话题标签关联表模型
 * 表名：ntp_im_moment_tag
 */
class MomentTag extends Model
{
    use TraitModel;

    protected $name = 'im_moment_tag';
}
