<?php

namespace app\common\model;

use app\common\traites\TraitModel;
use think\Model;

class TouziLotteryTimes extends Model
{
    use TraitModel;

    public $name = 'touzi_choujiang_times_send_log';

    public static function page_list($where, $limit, $page, $order)
    {
        return self::where($where)
            //->where($map)
            ->order($order)
            ->paginate(['list_rows' => $limit, 'page' => $page])
            ->each(function ($item, $key) {

            });
    }

}
