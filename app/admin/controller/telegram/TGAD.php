<?php

namespace app\admin\controller\telegram;

use app\admin\controller\Base;
use app\common\model\TgCrowdList;
use app\common\traites\PublicCrudTrait;
use think\facade\Db;
use think\exception\ValidateException;

/**
 * Telegram群组管理控制器
 */
class TGAD extends Base
{
    protected $model;
    use PublicCrudTrait;

    /**
     * 初始化
     */
    public function initialize()
    {
        $this->model = new TgCrowdList();
        parent::initialize();
    }

    
}