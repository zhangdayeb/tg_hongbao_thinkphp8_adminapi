<?php


namespace app\common\model;
use app\common\traites\TraitModel;
use think\Model;

class TouziProductOrder extends Model
{
    use TraitModel;
    public $name = 'touzi_product_order';

    public static function page_list($where, $limit, $page, $date)
    {
         $map = self::whereMapUser();
        //时间查询存在
        if (empty($date)) {
            $res = self::alias('a')
              ->join('common_user b', 'a.user_id = b.id', 'left')
              ->field('a.*,b.user_name,b.nickname,b.phone')
                ->where($where)
                ->where($map)
                //->where(['a.status' => 1])
                ->order('a.id desc');
        } else {
            $res = self::alias('a')
              ->join('common_user b', 'a.user_id = b.id', 'left')
              ->field('a.*,b.user_name,b.nickname,b.phone')
                ->where($where)
                ->where($map)
                ->whereBetweenTime('a.create_time', $date['start'], $date['end'])
                ->order('a.id desc');
        }
        return $res->paginate(['list_rows' => $limit, 'page' => $page], false);
    }

    public static function page_list_home($where, $limit, $page, $date)
    {
         $map = self::whereMapUser();
        //时间查询存在
        if (empty($date)) {
            $res = self::alias('a')
                ->join('touzi_product b', 'a.product_id = b.id', 'left')
                ->field('a.*,b.gain_day')
                ->where($where)
                ->where($map)
                ->where('a.order_status','<>',-1)
                ->order('a.id desc');
        } else {
            $res = self::alias('a')
                ->join('touzi_product b', 'a.product_id = b.id', 'left')
                ->field('a.*,b.gain_day')
                ->where($where)
                ->where($map)
                ->where('a.order_status','<>',-1)
                ->whereBetweenTime('a.create_time', $date['start'], $date['end'])
                ->order('a.id desc');
        }
        return $res->paginate(['list_rows' => $limit, 'page' => $page], false);
    }

    private static function buildWhere($uid, $level)
    {
        // if ('n' === strtolower($level)) {
        //     // $field = 'agent_up_ids';
        //     // $op = 'REGEXP';
        //     // $uid = "^$uid#|#$uid#|#$uid$";

        //     return self::where(function ($query) use ($uid) {
        //         $query->where('agent_id_1', '=', $uid)
        //             ->whereOr('agent_id_2', '=', $uid)
        //             ->whereOr('agent_id_3', '=', $uid);
        //     });
        // } else {
        //     $field = 'agent_id_' . $level;
        //     $op = '=';
        //     return self::where($field, $op, $uid);
        // }
        if ('n' === strtolower($level)) {
            return self::where(function ($query) use ($uid) {
                $query->where('agent_id_1', '=', $uid)
                    ->whereOr('agent_id_2', '=', $uid)
                    ->whereOr('agent_id_3', '=', $uid);
            });
        }elseif('w'=== strtolower($level)) {
            $field = 'agent_up_ids';
            $op = 'REGEXP';
            $uid = "^$uid#|#$uid#|#$uid$";
            return self::where($field, $op, $uid);
        }else {
            $field = 'agent_id_' . $level;
            $op = '=';
            return self::where($field, $op, $uid);
        }
    }

    public static function getCountByField($uid, $level, array $where = [],$map_date=[])
    {
        if(!empty($map_date)){
            $users = self::buildWhere($uid, $level)->select();
            $uids = [];
            foreach ($users as $k =>$v){
                $uids[] = $v->id;
            }
            if(count($uids)>0){
                $uids_str = implode(',', $uids);
            
                return self::whereTime('create_time', 'between', $map_date)
                ->where('id','in', $uids_str)
                ->count();
            }else{
                return 0;
            }
            
        }else{
            return self::buildWhere($uid, $level)->when($where, $where)->count();
        }
        
    }

    public static function getFirstBuyByLastMonthCount($uid)
    {
        $count1 = self::where(['agent_id_1' => $uid, 'is_first_buy' => 1])
            ->whereMonth('create_time', 'last month')->count();
        $count2 = self::where(['agent_id_2' => $uid, 'is_first_buy' => 1])
            ->whereMonth('create_time', 'last month')->count();
        $count3 = self::where(['agent_id_3' => $uid, 'is_first_buy' => 1])
            ->whereMonth('create_time', 'last month')->count();

        return $count1 + $count2 + $count3;
    }

    public static function getFirstBuyByYesterdayCount($uid)
    {
        $count1 = self::where(['agent_id_1' => $uid, 'is_first_buy' => 1])
            ->whereDay('create_time', 'yesterday')->count();
        $count2 = self::where(['agent_id_2' => $uid, 'is_first_buy' => 1])
            ->whereDay('create_time', 'yesterday')->count();
        $count3 = self::where(['agent_id_3' => $uid, 'is_first_buy' => 1])
            ->whereDay('create_time', 'yesterday')->count();

        return $count1 + $count2 + $count3;
    }

    public static function getBuyMoneyByLastWeek($uid)
    {
        $count1 = self::where('agent_id_1', $uid)->whereWeek('create_time', 'last week')->sum('buy_price');
        $count2 = self::where('agent_id_2', $uid)->whereWeek('create_time', 'last week')->sum('buy_price');
        $count3 = self::where('agent_id_3', $uid)->whereWeek('create_time', 'last week')->sum('buy_price');

        return $count1 + $count2 + $count3;
    }

    public static function getBuyMoneyByToday($uid)
    {
        $count1 = self::where('agent_id_1', $uid)->whereDay('create_time')->sum('buy_price');
        $count2 = self::where('agent_id_2', $uid)->whereDay('create_time')->sum('buy_price');
        $count3 = self::where('agent_id_3', $uid)->whereDay('create_time')->sum('buy_price');

        return $count1 + $count2 + $count3;
    }

}