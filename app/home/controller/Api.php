<?php
namespace app\home\controller;

use app\BaseController;
use app\Request;
use think\App;

// 非登录 处理 一些接口
class Api extends BaseController
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }
    
    /**
     * 检测是否存活
     * @return void
     */
    public function check(){
        echo 'ok';
    }
// 类结束了    
}