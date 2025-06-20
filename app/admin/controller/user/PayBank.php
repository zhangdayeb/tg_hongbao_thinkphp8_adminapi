<?php


namespace app\admin\controller\user;


use app\admin\controller\Base;
use app\common\model\PayBank as models;
use app\common\traites\PublicCrudTrait;
use \app\validate\PayBank as validates;
use think\exception\ValidateException;
use think\facade\Db;

class PayBank extends Base
{
    protected $model;
    use PublicCrudTrait;

    /**
     * 支付银行卡控制器
     */
    public function initialize()
    {
        $this->model = new models();
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    /**
     * 菜单栏目树
     */
    public function index()
    {
        //当前页
        $page = $this->request->post('page', 1);
        //每页显示数量
        $limit = $this->request->post('limit', 10);
        //查询搜索条件

        $list = $this->model->where('status', 1)->order('id desc')->paginate(['list_rows' => $limit, 'page' => $page]);
        return $this->success($list);
    }

    public function del()
    {
        //过滤数据
        $postField = 'id';
        $post = $this->request->only(explode(',', $postField), 'post', null);
        //验证数据
        try {
            validate(validates::class)->scene('del')->check($post);
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            return $this->failed($e->getError());
        }

        $post['status'] = -1;
        $del = $this->model->update($post);
        if ($del) return $this->success([]);
        return $this->failed('删除失败');
    }

    /**
     * 银行卡默认修改
     * @return mixed
     */
    public function default()
    {
        //过滤数据
        $postField = 'id';
        $post = $this->request->only(explode(',', $postField), 'post', null);
        //验证数据
        try {
            validate(validates::class)->scene('del')->check($post);
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            return $this->failed($e->getError());
        }

        $post['is_default'] = 1;
        $save = false;
        // 启动事务
        Db::startTrans();
        try {
            //查询该条数据的uid
            $res = $this->model->find($post['id']);
            //其他改为0
            $this->model->where('u_id', $res['u_id'])->update(['is_default' => 0]);
            //当条数据改为默认
            $this->model->update($post);
            $save = true;
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }

        if ($save) return $this->success([]);
        return $this->failed('设置失败');
    }

    public function info()
    {
        //银行卡信息
        $id =$this->request->post('id/d',0);
        if ($id <= 0) return $this->failed('ID错误');
        $find = $this->model->where('u_id', $id)->find();
        $this->success($find);
    }

    public function edit()
    {
        $postField = 'id,address,name,user_name,card,u_id';
        $post = $this->request->only(explode(',', $postField), 'post', null);
        //验证数据
        try {
            validate(validates::class)->scene('edit')->check($post);
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            return $this->failed($e->getError());
        }
        $post['is_default']=1;
        $post['status']=1;
       if (isset($post['id']) && $post['id'] >=0 ){
           $this->model->update($post);
           return $this->success([]);
       }
        if (isset($post['id'])) unset($post['id']);
        $this->model->insert($post);
        return $this->success([]);
    }
}