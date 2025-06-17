<?php

namespace app\common\model;

use app\common\traites\TraitModel;
use think\Model;

class TouziQianDao extends Model
{
    use TraitModel;

    public $name = 'touzi_qiandao';

    public static function page_list($where, $limit, $page, $date = '')
    {
        $map = self::whereMap();
        //时间查询存在
        if (empty($date)) {
            $res = self::alias('a')
                ->where($where)
                ->where($map)
                ->paginate(['list_rows' => $limit, 'page' => $page], false);
        } else {
            $res = self::alias('a')
                ->where($where)
                ->where($map)
                ->paginate(['list_rows' => $limit, 'page' => $page], false);
        }
        return $res;
    }

    public static function signinCheck($uid)
    {
        return self::where('user_id', $uid)->whereDay('create_time')->count();
    }

}
