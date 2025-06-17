<?php
namespace app\common\model;

use app\common\traites\TraitModel;
use think\Model;

/**
 * 朋友圈图片模型
 * 对应数据库表：ntp_im_moment_image
 * 存储动态的图片信息
 */
class MomentImage extends Model
{
    use TraitModel;
    protected $name = 'im_moment_image';  // 对应表 ntp_im_moment_image

    
}
