<?php

namespace app\home\controller\im;

use app\BaseController;
use app\common\traites\PublicCrudTrait;
use app\common\model\Moment;
use app\common\model\MomentLike;
use app\common\model\MomentComment;
use think\facade\Db;
use app\common\model\MomentReport;
use hg\apidoc\annotation as Apidoc;
use think\Request; // ✅ 使用实例类，不是 facade
use think\Response;

/**
 * @Apidoc\Title("朋友圈动态接口")
 * @Apidoc\Group("朋友圈")
 */
class MomentController extends BaseController
{
    use PublicCrudTrait;

    /**
     * 控制器初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new Moment();
    }

    /**
     * @Apidoc\Title("获取动态列表")
     * @Apidoc\Url("/home/im.moment/index")
     * @Apidoc\Method("GET")
     * @Apidoc\Param("user_id", type="int", desc="用户ID", required=false)
     * @Apidoc\Returned("list", type="array", desc="动态列表")
     */
    public function index()
    {
        $page = input('page', 1);
        $limit = input('limit', 10);
        $user_id = input('user_id', 0);
    
        $moments = $this->model
            ->with(['images', 'videos', 'likes', 'comments'])
            ->where('user_id',$user_id)
            ->order('create_time desc')
            ->paginate($limit, false, ['page' => $page]);
    
        $result = [];
    
        foreach ($moments as $moment) {
            // 模拟获取用户头像昵称（真实情况可以 leftJoin 或通过 user 关联）
            $user = \app\common\model\User::find($moment['user_id']);
    
            $result[] = [
                'id' => $moment['id'],
                'avatar' => $user['avatar'] ?? '/static/moments/default-avatar.png',
                'nickname' => $user['nickname'] ?? '匿名用户',
                'text' => $moment['content'],
                'images' => array_column($moment['images']->toArray(), 'image_url'),
                'time' => $this->formatTime($moment['create_time']),
                'likes' => $moment['likes']->map(function ($like) {
                    $u = \app\common\model\User::find($like['user_id']);
                    return $u['nickname'] ?? '用户';
                })->toArray(),
                'comments' => $moment['comments']->map(function ($comment) {
                    $u = \app\common\model\User::find($comment['user_id']);
                    return [
                        'user' => $u['nickname'] ?? '用户',
                        'content' => $comment['content']
                    ];
                })->toArray(),
            ];
        }
    
        return json([
            'code' => 200,
            'msg' => '获取成功',
            'data' => $result,
            'page' => $page,
            'limit' => $limit,
        ]);
    }
    
    protected function formatTime($datetime)
    {
        $time = strtotime($datetime);
        $diff = time() - $time;
    
        if ($diff < 60) return $diff . '秒前';
        if ($diff < 3600) return floor($diff / 60) . '分钟前';
        if ($diff < 86400) return floor($diff / 3600) . '小时前';
        if ($diff < 2592000) return floor($diff / 86400) . '天前';
    
        return date('Y-m-d', $time);
    }
    
    public function sendmoment()
    {
        $params = request()->post();

        // 参数校验
        if (empty($params['user_id']) || empty($params['content'])) {
            return show(400, '参数不完整');
        }

        // 默认隐私为公开
        $privacy = $params['privacy'] ?? 0;

        Db::startTrans();
        try {
            // 插入朋友圈动态主表
            $momentId = Db::name('im_moment')->insertGetId([
                'user_id' => $params['user_id'],
                'content' => $params['content'],
                'privacy' => $privacy,
                'create_time' => date('Y-m-d H:i:s')
            ]);

            // 插入图片（如果有）
            if (!empty($params['images']) && is_array($params['images'])) {
                $images = array_map(function($url) use ($momentId) {
                    return [
                        'moment_id' => $momentId,
                        'image_url' => $url
                    ];
                }, $params['images']);
                Db::name('im_moment_image')->insertAll($images);
            }

            Db::commit();
            return show([], 200, '发布成功');
        } catch (\Exception $e) {
            Db::rollback();
            return show([], 400, '发布失败：' . $e->getMessage());
        }
    }
    /**
     * 搜索同公司用户 & 群组（根据 groupid 限制）
     */
    public function search_user(Request $request)
    {
        $keyword = trim($request->post('keyword', ''));
        $groupid = trim($request->post('groupid', ''));

        if (!$keyword || !$groupid) {
            return show(400, '缺少参数');
        }

        // 搜索同 groupid 的用户（昵称 或 手机号）
        $userList = Db::name('common_user')
            ->where('groupid', $groupid)
            ->where(function ($query) use ($keyword) {
                $query->whereOr([
                    ['nickname', 'like', "%$keyword%"],
                    ['phone', 'like', "%$keyword%"],
                ]);
            })
            ->field('phone as userID, nickname as nick, avatar')
            ->limit(20)
            ->select()
            ->toArray();

        // 搜索同 groupid 的群组（群名 或 sdk_groupid）
        $groupList = Db::name('im_group')
            ->where('groupid', $groupid)
            ->where(function ($query) use ($keyword) {
                $query->whereOr([
                    ['group_name', 'like', "%$keyword%"],
                    ['sdk_groupid', 'like', "%$keyword%"],
                ]);
            })
            ->field('sdk_groupid as groupID, group_name as name, avatar')
            ->limit(20)
            ->select()
            ->toArray();

        $data = [
            'user'  => [ 'list' => $userList ],
            'group' => [ 'list' => $groupList ],
        ];

        return show($data);
    }
    /**
     * @Apidoc\Title("删除动态")
     * @Apidoc\Url("/home/im.moment/delete")
     * @Apidoc\Method("DELETE")
     */
    public function delete($id)
    {
        $moment = $this->model->find($id);

        if (!$moment) {
            return show([], 404, '动态不存在');
        }

        $moment->delete();

        return show([], 200, '删除成功');
    }

    /**
     * @Apidoc\Title("点赞动态")
     * @Apidoc\Url("/home/im.moment/like/:id")
     * @Apidoc\Method("POST")
     */
    public function like($id)
    {
        $userId = input('user_id');

        $exist = MomentLike::where(['moment_id' => $id, 'user_id' => $userId])->find();
        if ($exist) {
            return show([], 400, '已点赞');
        }

        MomentLike::create([
            'moment_id' => $id,
            'user_id' => $userId
        ]);

        return show([], 200, '点赞成功');
    }

    /**
     * @Apidoc\Title("取消点赞")
     * @Apidoc\Url("/home/im.moment/unlike/:id")
     * @Apidoc\Method("DELETE")
     */
    public function unlike($id)
    {
        $userId = input('user_id');

        $like = MomentLike::where(['moment_id' => $id, 'user_id' => $userId])->find();
        if (!$like) {
            return show([], 400, '未点赞');
        }

        $like->delete();
        return show([], 200, '取消点赞成功');
    }

    /**
     * @Apidoc\Title("评论动态")
     * @Apidoc\Url("/home/im.moment/comment")
     * @Apidoc\Method("POST")
     */
    public function comment()
    {
        $data = $this->request->only(['moment_id', 'user_id', 'content']);

        if (empty($data['content'])) {
            return show([], 400, '评论内容不能为空');
        }

        $comment = MomentComment::create($data);

        return show(['comment_id' => $comment->id], 200, '评论成功');
    }

    /**
     * @Apidoc\Title("删除评论")
     * @Apidoc\Url("/home/im.moment/comment/:id")
     * @Apidoc\Method("DELETE")
     */
    public function deleteComment($id)
    {
        $comment = MomentComment::find($id);

        if (!$comment) {
            return show([], 404, '评论不存在');
        }

        $comment->delete();
        return show([], 200, '删除评论成功');
    }

    /**
     * @Apidoc\Title("举报动态")
     * @Apidoc\Url("/home/im.moment/report/:id")
     * @Apidoc\Method("POST")
     */
    public function report($id)
    {
        $data = [
            'moment_id' => $id,
            'user_id' => input('user_id'),
            'reason' => input('reason'),
        ];

        MomentReport::create($data);

        return show([], 200, '举报成功');
    }

    /**
     * 临时测试接口
     */
    public function info()
    {
        return show(['info' => '测试成功']);
    }
// 类结束了 
}
