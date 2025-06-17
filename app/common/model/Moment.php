<?php
namespace app\common\model;

use app\common\traites\TraitModel;
use think\Model;

/**
 * 朋友圈动态模型
 * 
 * 说明：
 * 该模型对应数据库中的 `ntp_im_moment` 表，
 * 用于存储用户发布的朋友圈动态信息。
 * 
 * @package app\common\model
 */
class Moment extends Model
{
    // 使用 TraitModel，包含通用增删改查等方法
    use TraitModel;

    /**
     * 指定模型对应的数据库表名（去掉前缀 ntp_）
     * 这里设置为 im_moment，实际对应表名为 ntp_im_moment
     * 
     * @var string
     */
    protected $name = 'im_moment';  

    /**
     * 一对多关联：动态的评论集合
     * 
     * 关联模型：MomentComment
     * 外键：moment_id
     * 本地键：id
     * 
     * @return \think\model\relation\HasMany
     */
    public function comments()
    {
        return $this->hasMany(MomentComment::class, 'moment_id', 'id');
    }

    /**
     * 一对多关联：动态的图片集合
     * 
     * 关联模型：MomentImage
     * 外键：moment_id
     * 本地键：id
     * 
     * @return \think\model\relation\HasMany
     */
    public function images()
    {
        return $this->hasMany(MomentImage::class, 'moment_id', 'id');
    }

    /**
     * 一对多关联：动态的视频集合
     * 
     * 关联模型：MomentVideo
     * 外键：moment_id
     * 本地键：id
     * 
     * @return \think\model\relation\HasMany
     */
    public function videos()
    {
        return $this->hasMany(MomentVideo::class, 'moment_id', 'id');
    }

    /**
     * 一对多关联：动态的点赞集合
     * 
     * 关联模型：MomentLike
     * 外键：moment_id
     * 本地键：id
     * 
     * @return \think\model\relation\HasMany
     */
    public function likes()
    {
        return $this->hasMany(MomentLike::class, 'moment_id', 'id');
    }

    /**
     * 一对多关联：动态的标签集合
     * 
     * 关联模型：MomentTag
     * 外键：moment_id
     * 本地键：id
     * 
     * @return \think\model\relation\HasMany
     */
    public function tags()
    {
        return $this->hasMany(MomentTag::class, 'moment_id', 'id');
    }
}
