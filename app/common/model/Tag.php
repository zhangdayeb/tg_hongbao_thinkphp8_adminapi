<?php
namespace app\common\model;

use app\common\traites\TraitModel;
use think\Model;

/**
 * 朋友圈话题标签表模型
 * 表名：ntp_im_tag
 */
class Tag extends Model
{
    use TraitModel;

    protected $name = 'im_tag';
}
