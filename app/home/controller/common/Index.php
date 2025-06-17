<?php
namespace app\home\controller\common;

use app\BaseController;
use app\common\model\TouziAds;
use app\common\model\Notice;
use app\common\model\SysConfig;
use think\facade\Db;
use think\exception\ValidateException;

use hg\apidoc\annotation as Apidoc;
/**
 *
 * @Apidoc\Title("管理首页")
 * */
class Index extends BaseController
{
    /**
     * @Apidoc\Title("获取配置文件")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("name", type="string",require=true, desc="配置参数")
     * @Apidoc\Returned("data", type="bool", desc="返回成功失败结果")
     */
    public function get_config()
    {
        $name = $this->request->param('name');
        return show(htmlspecialchars_decode(getSystemConfig($name)));
    }

    /**
     * @Apidoc\Title("获取集团标识系统配置")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("name", type="string",require=true, desc="配置参数")
     * @Apidoc\Returned("data", type="bool", desc="返回成功失败结果")
     */
    public function get_group_cfg()
    {
        $name = $this->request->param('groupid');
        $r = Db::name('im_base_set')->where('groupid',$name)->find();
        return show($r);
    }
    /**
     * @Apidoc\Title("获取全部配置文件")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("name", type="string",require=true, desc="配置参数")
     * @Apidoc\Returned("data", type="bool", desc="返回成功失败结果")
     */
    public function get_config_all()
    {
        $res = (new SysConfig)->select();
        return show($res);
    }

    /**
     * @Apidoc\Title("获取 通知")
     * @Apidoc\Method("POST")
     * @Apidoc\Returned("data", type="bool", desc="返回成功失败结果")
     */
    public function get_notice()
    {
        $res = (new Notice())->page_list(['status'=>1],20,1);
        return show($res);
    }

    /**
     * @Apidoc\Title("轮播广告")
     * @Apidoc\Method("GET")
     */
    public function ads(){
      $TouziAds= new TouziAds();
      $res=$TouziAds->field('id,img')->where('status',1)->order('sort asc')->select();
      return show($res);
    }
// 类结束了
}
