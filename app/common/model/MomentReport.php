<?php
namespace app\common\model;

use app\common\traites\TraitModel;
use think\Model;

/**
 * 朋友圈举报表模型
 * 表名：ntp_im_moment_report
 */
class MomentReport extends Model
{
    use TraitModel;

    protected $name = 'im_moment_report';
}
