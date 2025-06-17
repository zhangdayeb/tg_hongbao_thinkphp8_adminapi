<?php


namespace app\common\model;
use app\common\traites\TraitModel;
use think\Model;

class TouziProduct extends Model
{
    use TraitModel;
    public $name = 'touzi_product';

    public static function page_list($where,$limit,$page,$order)
    {
       // $map=self::whereMap();
        return self::where($where)
            //->where($map)
            ->order($order)
            ->paginate(['list_rows'=>$limit,'page'=>$page], false)->each(function($item, $key){
                return  $item->product_describe = htmlspecialchars_decode($item->product_describe);
                 //!empty($item->thumb_url) && $item->thumb_url=explode(',',$item->thumb_url);
            });
    }

}