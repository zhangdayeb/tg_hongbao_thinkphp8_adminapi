<?php

namespace app\admin\controller\telegram;

use app\admin\controller\Base;
use app\common\model\RedPacket;
use app\common\model\RedPacketRecord;
use app\common\model\TgCrowdList;
use app\common\model\User;
use app\common\traites\PublicCrudTrait;
use think\facade\Db;
use think\exception\ValidateException;

/**
 * Telegram红包管理控制器
 */
class TGRedPacket extends Base
{
    protected $model;
    protected $recordModel;
    use PublicCrudTrait;

    /**
     * 初始化
     */
    public function initialize()
    {
        $this->model = new RedPacket();
        $this->recordModel = new RedPacketRecord();
        parent::initialize();
    }

    /**
     * 红包列表
     */
    public function index()
    {
        // 当前页
        $page = $this->request->post('page', 1);
        // 每页显示数量
        $limit = $this->request->post('limit', 10);
        // 查询搜索条件
        $post = array_filter($this->request->post());
        
        $map = [];
        
        // 红包标题模糊搜索
        if (!empty($post['title'])) {
            $map[] = ['title', 'like', '%' . $post['title'] . '%'];
        }
        
        // 红包ID精确查找
        if (!empty($post['packet_id'])) {
            $map[] = ['packet_id', '=', $post['packet_id']];
        }
        
        // 发送者TG_ID搜索
        if (!empty($post['sender_tg_id'])) {
            $map[] = ['sender_tg_id', '=', $post['sender_tg_id']];
        }
        
        // 群组ID筛选
        if (!empty($post['chat_id'])) {
            $map[] = ['chat_id', '=', $post['chat_id']];
        }
        
        // 红包状态筛选
        if (isset($post['status']) && $post['status'] !== '') {
            $map[] = ['status', '=', $post['status']];
        }
        
        // 红包类型筛选
        if (isset($post['packet_type']) && $post['packet_type'] !== '') {
            $map[] = ['packet_type', '=', $post['packet_type']];
        }
        
        // 是否系统红包
        if (isset($post['is_system']) && $post['is_system'] !== '') {
            $map[] = ['is_system', '=', $post['is_system']];
        }
        
        // 金额范围筛选
        if (!empty($post['min_amount'])) {
            $map[] = ['total_amount', '>=', $post['min_amount']];
        }
        if (!empty($post['max_amount'])) {
            $map[] = ['total_amount', '<=', $post['max_amount']];
        }
        
        // 时间范围筛选
        if (!empty($post['start_date']) && !empty($post['end_date'])) {
            $map[] = ['created_at', 'between', [$post['start_date'] . ' 00:00:00', $post['end_date'] . ' 23:59:59']];
        }
        
        // 查询数据
        $list = $this->model
            ->where($map)
            ->order('id desc')
            ->paginate(['list_rows' => $limit, 'page' => $page]);
        
        // 格式化数据
        $list->each(function ($item) {
            $item->packet_type_text = $this->getPacketTypeText($item->packet_type);
            $item->status_text = $this->getStatusText($item->status);
            $item->chat_type_text = $this->getChatTypeText($item->chat_type);
            $item->is_system_text = $item->is_system ? '系统红包' : '用户红包';
            $item->progress = $this->calculateProgress($item);
            $item->created_at_format = date('Y-m-d H:i:s', strtotime($item->created_at));
            
            // 获取发送者信息
            $sender = User::where('id', $item->sender_id)->field('user_name,tg_username')->find();
            $item->sender_name = $sender ? ($sender->tg_username ?: $sender->user_name) : '未知用户';
            
            // 获取群组信息
            $group = TgCrowdList::where('crowd_id', $item->chat_id)->field('title')->find();
            $item->group_name = $group ? $group->title : '未知群组';
        });

        return $this->success($list);
    }

    /**
     * 红包详情
     */
    public function detail()
    {
        $id = $this->request->post('id');
        if (empty($id)) {
            return $this->failed('ID参数不能为空');
        }

        $redPacket = $this->model->where('id', $id)->find();
        if (empty($redPacket)) {
            return $this->failed('红包不存在');
        }

        // 格式化数据
        $redPacket->packet_type_text = $this->getPacketTypeText($redPacket->packet_type);
        $redPacket->status_text = $this->getStatusText($redPacket->status);
        $redPacket->chat_type_text = $this->getChatTypeText($redPacket->chat_type);
        $redPacket->is_system_text = $redPacket->is_system ? '系统红包' : '用户红包';
        $redPacket->progress = $this->calculateProgress($redPacket);
        
        // 获取发送者信息
        $sender = User::where('id', $redPacket->sender_id)->find();
        $redPacket->sender_info = $sender;
        
        // 获取群组信息
        $group = TgCrowdList::where('crowd_id', $redPacket->chat_id)->find();
        $redPacket->group_info = $group;
        
        // 获取领取记录
        $records = $this->recordModel
            ->where('packet_id', $redPacket->packet_id)
            ->order('grab_order asc')
            ->select();
        
        $redPacket->records = $records;
        $redPacket->record_count = count($records);
        
        // 统计信息
        $redPacket->stats = [
            'grabbed_amount' => $redPacket->total_amount - $redPacket->remain_amount,
            'grabbed_count' => $redPacket->total_count - $redPacket->remain_count,
            'completion_rate' => $redPacket->total_count > 0 ? round(($redPacket->total_count - $redPacket->remain_count) / $redPacket->total_count * 100, 2) : 0,
            'avg_amount' => $records->count() > 0 ? round($records->sum('amount') / $records->count(), 2) : 0,
        ];

        return $this->success($redPacket);
    }

    /**
     * 创建系统红包
     */
    public function createSystem()
    {
        $post = $this->request->post();
        
        // 验证必要字段
        if (empty($post['title'])) {
            return $this->failed('红包标题不能为空');
        }
        if (empty($post['total_amount']) || $post['total_amount'] <= 0) {
            return $this->failed('红包金额必须大于0');
        }
        if (empty($post['total_count']) || $post['total_count'] <= 0) {
            return $this->failed('红包数量必须大于0');
        }
        if (empty($post['chat_ids'])) {
            return $this->failed('请选择发送群组');
        }

        $chatIds = is_array($post['chat_ids']) ? $post['chat_ids'] : [$post['chat_ids']];
        $successCount = 0;
        $errorMessages = [];

        Db::startTrans();
        try {
            foreach ($chatIds as $chatId) {
                // 生成红包ID
                $packetId = 'SYS' . time() . rand(1000, 9999);
                
                // 计算过期时间
                $expireHours = $post['expire_hours'] ?? 24;
                $expireTime = date('Y-m-d H:i:s', time() + $expireHours * 3600);
                
                // 准备红包数据
                $redPacketData = [
                    'packet_id' => $packetId,
                    'title' => $post['title'],
                    'total_amount' => $post['total_amount'],
                    'total_count' => $post['total_count'],
                    'remain_amount' => $post['total_amount'],
                    'remain_count' => $post['total_count'],
                    'packet_type' => $post['packet_type'] ?? 1,
                    'sender_id' => 1, // 系统用户ID
                    'sender_tg_id' => 'SYSTEM',
                    'chat_id' => $chatId,
                    'chat_type' => 'group',
                    'expire_time' => $expireTime,
                    'status' => 1,
                    'is_system' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                
                $result = $this->model->insert($redPacketData);
                if ($result) {
                    $successCount++;
                } else {
                    $errorMessages[] = "群组 {$chatId} 红包创建失败";
                }
            }

            if ($successCount > 0) {
                Db::commit();
                $message = "成功创建 {$successCount} 个系统红包";
                if (!empty($errorMessages)) {
                    $message .= "，部分失败：" . implode('；', $errorMessages);
                }
                return $this->success([], $message);
            } else {
                Db::rollback();
                return $this->failed('系统红包创建失败：' . implode('；', $errorMessages));
            }
        } catch (\Exception $e) {
            Db::rollback();
            return $this->failed('系统红包创建失败：' . $e->getMessage());
        }
    }

    /**
     * 撤回红包
     */
    public function revoke()
    {
        $id = $this->request->post('id');
        if (empty($id)) {
            return $this->failed('ID参数不能为空');
        }

        $redPacket = $this->model->where('id', $id)->find();
        if (empty($redPacket)) {
            return $this->failed('红包不存在');
        }

        // 检查是否可以撤回
        if ($redPacket->status != 1) {
            return $this->failed('只有进行中的红包才能撤回');
        }

        // 检查是否有人领取
        $recordCount = $this->recordModel->where('packet_id', $redPacket->packet_id)->count();
        if ($recordCount > 0) {
            return $this->failed('已有人领取的红包无法撤回');
        }

        Db::startTrans();
        try {
            // 更新红包状态
            $this->model->where('id', $id)->update([
                'status' => 4, // 已撤回
                'finished_at' => date('Y-m-d H:i:s')
            ]);

            // 如果是用户红包，需要退还金额
            if ($redPacket->is_system == 0) {
                Db::name('common_user')->where('id', $redPacket->sender_id)->inc('money_balance', $redPacket->remain_amount);
                
                // 记录资金流水
                Db::name('common_pay_money_log')->insert([
                    'user_id' => $redPacket->sender_id,
                    'money' => $redPacket->remain_amount,
                    'type' => 1, // 收入
                    'remark' => "红包撤回退款：{$redPacket->packet_id}",
                    'create_time' => date('Y-m-d H:i:s')
                ]);
            }

            Db::commit();
            return $this->success([], '红包撤回成功');
        } catch (\Exception $e) {
            Db::rollback();
            return $this->failed('红包撤回失败：' . $e->getMessage());
        }
    }

    /**
     * 延期红包
     */
    public function extend()
    {
        $id = $this->request->post('id');
        $hours = $this->request->post('hours', 24);
        
        if (empty($id)) {
            return $this->failed('ID参数不能为空');
        }

        $redPacket = $this->model->where('id', $id)->find();
        if (empty($redPacket)) {
            return $this->failed('红包不存在');
        }

        // 检查红包状态
        if (!in_array($redPacket->status, [1, 3])) {
            return $this->failed('只有进行中或已过期的红包才能延期');
        }

        // 计算新的过期时间
        $newExpireTime = date('Y-m-d H:i:s', time() + $hours * 3600);
        
        $result = $this->model->where('id', $id)->update([
            'expire_time' => $newExpireTime,
            'status' => 1 // 重新激活
        ]);

        if ($result) {
            return $this->success([], "红包延期成功，新过期时间：{$newExpireTime}");
        }

        return $this->failed('红包延期失败');
    }

    /**
     * 修改红包状态
     */
    public function changeStatus()
    {
        $id = $this->request->post('id');
        $status = $this->request->post('status');
        
        if (empty($id)) {
            return $this->failed('ID参数不能为空');
        }
        
        if (!in_array($status, [1, 2, 3, 4, 5])) {
            return $this->failed('状态参数错误');
        }

        $updateData = ['status' => $status];
        if (in_array($status, [2, 3, 4, 5])) {
            $updateData['finished_at'] = date('Y-m-d H:i:s');
        }

        $result = $this->model->where('id', $id)->update($updateData);

        if ($result) {
            return $this->success([], '状态修改成功');
        }

        return $this->failed('状态修改失败');
    }

    /**
     * 红包记录列表
     */
    public function records()
    {
        // 当前页
        $page = $this->request->post('page', 1);
        // 每页显示数量
        $limit = $this->request->post('limit', 10);
        // 查询搜索条件
        $post = array_filter($this->request->post());
        
        $map = [];
        
        // 红包ID筛选
        if (!empty($post['packet_id'])) {
            $map[] = ['packet_id', '=', $post['packet_id']];
        }
        
        // 用户TG_ID搜索
        if (!empty($post['user_tg_id'])) {
            $map[] = ['user_tg_id', '=', $post['user_tg_id']];
        }
        
        // 用户名搜索
        if (!empty($post['username'])) {
            $map[] = ['username', 'like', '%' . $post['username'] . '%'];
        }
        
        // 手气最佳筛选
        if (isset($post['is_best']) && $post['is_best'] !== '') {
            $map[] = ['is_best', '=', $post['is_best']];
        }
        
        // 金额范围
        if (!empty($post['min_amount'])) {
            $map[] = ['amount', '>=', $post['min_amount']];
        }
        if (!empty($post['max_amount'])) {
            $map[] = ['amount', '<=', $post['max_amount']];
        }
        
        // 时间范围
        if (!empty($post['start_date']) && !empty($post['end_date'])) {
            $map[] = ['created_at', 'between', [$post['start_date'] . ' 00:00:00', $post['end_date'] . ' 23:59:59']];
        }
        
        // 查询数据
        $list = $this->recordModel
            ->where($map)
            ->order('id desc')
            ->paginate(['list_rows' => $limit, 'page' => $page]);
        
        // 格式化数据
        $list->each(function ($item) {
            $item->is_best_text = $item->is_best ? '手气最佳' : '普通';
            $item->created_at_format = date('Y-m-d H:i:s', strtotime($item->created_at));
            
            // 获取红包信息
            $redPacket = $this->model->where('packet_id', $item->packet_id)->field('title,total_amount,total_count')->find();
            if ($redPacket) {
                $item->red_packet_title = $redPacket->title;
                $item->red_packet_total = $redPacket->total_amount;
                $item->red_packet_count = $redPacket->total_count;
            }
            
            // 获取用户信息
            $user = User::where('id', $item->user_id)->field('user_name,tg_username')->find();
            if ($user) {
                $item->user_display_name = $user->tg_username ?: $user->user_name;
            }
        });

        return $this->success($list);
    }

    /**
     * 红包统计
     */
    public function statistics()
    {
        $dateRange = $this->request->post('date_range', 'today'); // today, week, month, all
        
        // 构建时间条件
        $timeMap = [];
        switch ($dateRange) {
            case 'today':
                $timeMap[] = ['created_at', '>=', date('Y-m-d 00:00:00')];
                $timeMap[] = ['created_at', '<=', date('Y-m-d 23:59:59')];
                break;
            case 'week':
                $timeMap[] = ['created_at', '>=', date('Y-m-d 00:00:00', strtotime('-6 days'))];
                break;
            case 'month':
                $timeMap[] = ['created_at', '>=', date('Y-m-01 00:00:00')];
                break;
        }
        
        $stats = [
            // 基础统计
            'total_packets' => $this->model->where($timeMap)->count(),
            'total_amount' => $this->model->where($timeMap)->sum('total_amount'),
            'completed_packets' => $this->model->where($timeMap)->where('status', 2)->count(),
            'active_packets' => $this->model->where($timeMap)->where('status', 1)->count(),
            'expired_packets' => $this->model->where($timeMap)->where('status', 3)->count(),
            'revoked_packets' => $this->model->where($timeMap)->where('status', 4)->count(),
            
            // 红包类型统计
            'random_packets' => $this->model->where($timeMap)->where('packet_type', 1)->count(),
            'average_packets' => $this->model->where($timeMap)->where('packet_type', 2)->count(),
            
            // 系统红包 vs 用户红包
            'system_packets' => $this->model->where($timeMap)->where('is_system', 1)->count(),
            'user_packets' => $this->model->where($timeMap)->where('is_system', 0)->count(),
            
            // 领取统计
            'total_records' => $this->recordModel->where($timeMap)->count(),
            'total_grabbed' => $this->recordModel->where($timeMap)->sum('amount'),
            'best_luck_count' => $this->recordModel->where($timeMap)->where('is_best', 1)->count(),
            
            // 平均值统计
            'avg_packet_amount' => $this->model->where($timeMap)->avg('total_amount'),
            'avg_packet_count' => $this->model->where($timeMap)->avg('total_count'),
            'avg_grab_amount' => $this->recordModel->where($timeMap)->avg('amount'),
        ];
        
        // 完成率
        $stats['completion_rate'] = $stats['total_packets'] > 0 ? 
            round($stats['completed_packets'] / $stats['total_packets'] * 100, 2) : 0;
        
        // 热门群组排行
        $hotGroups = $this->model
            ->where($timeMap)
            ->field('chat_id, count(*) as packet_count, sum(total_amount) as total_amount')
            ->group('chat_id')
            ->order('packet_count desc')
            ->limit(10)
            ->select();
        
        foreach ($hotGroups as &$group) {
            $groupInfo = TgCrowdList::where('crowd_id', $group['chat_id'])->field('title')->find();
            $group['group_name'] = $groupInfo ? $groupInfo->title : '未知群组';
        }
        
        $stats['hot_groups'] = $hotGroups;
        
        // 每日发送趋势（最近7天）
        $dailyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $count = $this->model->whereTime('created_at', $date)->count();
            $amount = $this->model->whereTime('created_at', $date)->sum('total_amount');
            
            $dailyTrend[] = [
                'date' => $date,
                'count' => $count,
                'amount' => $amount ?: 0
            ];
        }
        
        $stats['daily_trend'] = $dailyTrend;

        return $this->success($stats);
    }

    /**
     * 导出红包数据
     */
    public function export()
    {
        $post = array_filter($this->request->post());
        $map = [];
        
        // 应用搜索条件
        if (!empty($post['packet_id'])) {
            $map[] = ['packet_id', '=', $post['packet_id']];
        }
        if (!empty($post['sender_tg_id'])) {
            $map[] = ['sender_tg_id', '=', $post['sender_tg_id']];
        }
        if (isset($post['status']) && $post['status'] !== '') {
            $map[] = ['status', '=', $post['status']];
        }
        if (!empty($post['start_date']) && !empty($post['end_date'])) {
            $map[] = ['created_at', 'between', [$post['start_date'] . ' 00:00:00', $post['end_date'] . ' 23:59:59']];
        }

        $list = $this->model->where($map)->limit(5000)->select(); // 限制导出数量
        
        // 格式化导出数据
        $exportData = [];
        foreach ($list as $item) {
            $sender = User::where('id', $item->sender_id)->field('user_name,tg_username')->find();
            $group = TgCrowdList::where('crowd_id', $item->chat_id)->field('title')->find();
            
            $exportData[] = [
                'ID' => $item->id,
                '红包ID' => $item->packet_id,
                '红包标题' => $item->title,
                '总金额' => $item->total_amount,
                '总数量' => $item->total_count,
                '剩余金额' => $item->remain_amount,
                '剩余数量' => $item->remain_count,
                '红包类型' => $this->getPacketTypeText($item->packet_type),
                '发送者' => $sender ? ($sender->tg_username ?: $sender->user_name) : '未知',
                '群组' => $group ? $group->title : '未知群组',
                '状态' => $this->getStatusText($item->status),
                '是否系统红包' => $item->is_system ? '是' : '否',
                '创建时间' => $item->created_at,
                '过期时间' => $item->expire_time,
                '完成时间' => $item->finished_at,
            ];
        }

        return $this->success($exportData);
    }

    /**
     * 获取红包类型文本
     */
    private function getPacketTypeText($type)
    {
        $typeMap = [
            1 => '拼手气红包',
            2 => '平均红包',
            3 => '定制红包'
        ];
        
        return $typeMap[$type] ?? '未知类型';
    }

    /**
     * 获取状态文本
     */
    private function getStatusText($status)
    {
        $statusMap = [
            1 => '进行中',
            2 => '已抢完',
            3 => '已过期',
            4 => '已撤回',
            5 => '已取消'
        ];
        
        return $statusMap[$status] ?? '未知状态';
    }

    /**
     * 获取聊天类型文本
     */
    private function getChatTypeText($type)
    {
        $typeMap = [
            'group' => '群组',
            'supergroup' => '超级群组',
            'private' => '私聊'
        ];
        
        return $typeMap[$type] ?? '未知类型';
    }

    /**
     * 计算红包进度
     */
    private function calculateProgress($redPacket)
    {
        if ($redPacket->total_count == 0) {
            return 0;
        }
        
        $grabbedCount = $redPacket->total_count - $redPacket->remain_count;
        return round($grabbedCount / $redPacket->total_count * 100, 2);
    }
}