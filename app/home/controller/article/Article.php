<?php
namespace app\home\controller\article;

use app\BaseController;
use app\common\model\ArticleModel;
use think\exception\ValidateException;

use hg\apidoc\annotation as Apidoc;
/**
 *
 * @Apidoc\Title("文章管理")
 * */
class Article extends BaseController
{
    /**
     * @Apidoc\Title("获取文章列表")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("type", type="string",require=true, desc="文章分类 首页为2 单页为 1 其他为3")
     * @Apidoc\Returned("data", type="bool", desc="返回成功失败结果")
     */
    public function get_article_list()
    {
        //过滤数据
        $postField = 'type';
        $params   = $this->request->only(explode(',', $postField), 'post', null);
        $map = [];
        if (isset($params['type'])) {
            $map['type'] = $params['type'];
        }
        $res = (new ArticleModel())->page_list($map,20,1);
        return show($res);
    }
    /**
     * @Apidoc\Title("获取文章详情")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("id", type="string",require=true, desc="文章ID")
     * @Apidoc\Returned("data", type="bool", desc="返回成功失败结果")
     */
    public function get_article_detail()
    {
        $id = $this->request->param('id','');
        $res = (new ArticleModel())->find($id);
        $res['content'] = htmlspecialchars_decode($res['content']);
        return show($res);
    }
}
