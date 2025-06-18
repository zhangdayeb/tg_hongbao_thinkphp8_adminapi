<?php

namespace app\admin\controller\telegram;

use app\admin\controller\Base;
use app\common\model\TgCrowdList;
use app\common\traites\PublicCrudTrait;
use think\facade\Db;
use think\exception\ValidateException;

/**
 * Telegram广告管理控制器
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
        // 使用广告表模型
        $this->model = Db::name('tg_advertisements');
        parent::initialize();
    }

    /**
     * 获取广告列表
     * POST /telegram/advertisements
     */
    public function getAdvertisementList()
    {
        $post = $this->request->post();
        
        // 分页参数
        $page = $post['page'] ?? 1;
        $limit = $post['limit'] ?? 20;
        
        // 构建查询条件
        $map = [];
        
        // 标题搜索
        if (!empty($post['title'])) {
            $map[] = ['title', 'like', '%' . $post['title'] . '%'];
        }
        
        // 发送模式筛选
        if (isset($post['send_mode']) && $post['send_mode'] !== '') {
            $sendModeMap = [
                'immediate' => 1,
                'scheduled' => 2, 
                'recurring' => 3
            ];
            if (isset($sendModeMap[$post['send_mode']])) {
                $map[] = ['send_mode', '=', $sendModeMap[$post['send_mode']]];
            }
        }
        
        // 状态筛选
        if (isset($post['status']) && $post['status'] !== '') {
            $statusMap = [
                'draft' => 0,
                'active' => 1,
                'completed' => 2,
                'cancelled' => 3
            ];
            if (isset($statusMap[$post['status']])) {
                $map[] = ['status', '=', $statusMap[$post['status']]];
            }
        }
        
        // 日期范围筛选
        if (!empty($post['start_date']) && !empty($post['end_date'])) {
            $map[] = ['created_at', 'between', [$post['start_date'] . ' 00:00:00', $post['end_date'] . ' 23:59:59']];
        }
        
        // 查询数据
        $total = $this->model->where($map)->count();
        $list = $this->model
            ->where($map)
            ->order('id desc')
            ->page($page, $limit)
            ->select();
        
        // 格式化数据
        $formattedList = [];
        foreach ($list as $item) {
            $formattedList[] = [
                'id' => $item['id'],
                'title' => $item['title'],
                'content' => $item['content'],
                'image_url' => $item['image_url'],
                'send_mode_text' => $this->getSendModeText($item['send_mode']),
                'send_mode' => $item['send_mode'],
                'send_time' => $item['send_time'],
                'daily_times' => $item['daily_times'],
                'interval_minutes' => $item['interval_minutes'],
                'status_text' => $this->getStatusText($item['status']),
                'status' => $item['status'],
                'total_sent_count' => $item['total_sent_count'],
                'success_count' => $item['success_count'],
                'failed_count' => $item['failed_count'],
                'success_rate' => $item['total_sent_count'] > 0 ? 
                    round($item['success_count'] / $item['total_sent_count'] * 100, 2) : 0,
                'start_date' => $item['start_date'],
                'end_date' => $item['end_date'],
                'last_sent_time' => $item['last_sent_time'],
                'next_send_time' => $item['next_send_time'],
                'created_at' => $item['created_at'],
                'updated_at' => $item['updated_at']
            ];
        }
        
        return $this->success([
            'list' => $formattedList,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ]);
    }

    /**
     * 获取广告详情
     * POST /telegram/advertisement/detail
     */
    public function getAdvertisementDetail()
    {
        $id = $this->request->post('id');
        
        if (empty($id)) {
            return $this->failed('广告ID不能为空');
        }
        
        $advertisement = $this->model->where('id', $id)->find();
        
        if (!$advertisement) {
            return $this->failed('广告不存在');
        }
        
        // 获取发送统计
        $sendLogs = Db::name('tg_message_logs')
            ->where('source_id', $id)
            ->where('source_type', 'advertisement')
            ->field('send_status,count(*) as count')
            ->group('send_status')
            ->select();
        
        $sendStats = [
            'pending' => 0,
            'success' => 0,
            'failed' => 0
        ];
        
        foreach ($sendLogs as $log) {
            switch ($log['send_status']) {
                case 0:
                    $sendStats['pending'] = $log['count'];
                    break;
                case 1:
                    $sendStats['success'] = $log['count'];
                    break;
                case 2:
                    $sendStats['failed'] = $log['count'];
                    break;
            }
        }
        
        $result = [
            'id' => $advertisement['id'],
            'title' => $advertisement['title'],
            'content' => $advertisement['content'],
            'image_url' => $advertisement['image_url'],
            'send_mode_text' => $this->getSendModeText($advertisement['send_mode']),
            'send_mode' => $advertisement['send_mode'],
            'send_time' => $advertisement['send_time'],
            'daily_times' => $advertisement['daily_times'],
            'interval_minutes' => $advertisement['interval_minutes'],
            'status_text' => $this->getStatusText($advertisement['status']),
            'status' => $advertisement['status'],
            'total_sent_count' => $advertisement['total_sent_count'],
            'success_count' => $advertisement['success_count'],
            'failed_count' => $advertisement['failed_count'],
            'start_date' => $advertisement['start_date'],
            'end_date' => $advertisement['end_date'],
            'last_sent_time' => $advertisement['last_sent_time'],
            'next_send_time' => $advertisement['next_send_time'],
            'is_sent' => $advertisement['is_sent'],
            'send_stats' => $sendStats,
            'created_at' => $advertisement['created_at'],
            'updated_at' => $advertisement['updated_at']
        ];
        
        return $this->success($result);
    }

    /**
     * 创建广告
     * POST /telegram/advertisement/create
     */
    public function createAdvertisement()
    {
        $post = $this->request->post();
        
        // 验证必填字段
        if (empty($post['title'])) {
            return $this->failed('广告标题不能为空');
        }
        
        if (empty($post['content'])) {
            return $this->failed('广告内容不能为空');
        }
        
        if (empty($post['send_mode'])) {
            return $this->failed('发送模式不能为空');
        }
        
        // 发送模式映射
        $sendModeMap = [
            'immediate' => 1,
            'scheduled' => 2,
            'recurring' => 3
        ];
        
        if (!isset($sendModeMap[$post['send_mode']])) {
            return $this->failed('发送模式参数错误');
        }
        
        $sendMode = $sendModeMap[$post['send_mode']];
        
        // 验证目标群组
        if (empty($post['target_groups']) || !is_array($post['target_groups'])) {
            return $this->failed('目标群组不能为空');
        }
        
        // 验证群组是否存在
        $groupCount = Db::name('tg_crowd_list')
            ->where('crowd_id', 'in', $post['target_groups'])
            ->where('del', 0)
            ->count();
            
        if ($groupCount != count($post['target_groups'])) {
            return $this->failed('部分目标群组不存在');
        }
        
        // 根据发送模式验证其他参数
        switch ($post['send_mode']) {
            case 'scheduled':
                if (empty($post['send_time'])) {
                    return $this->failed('定时发送必须设置发送时间');
                }
                break;
                
            case 'recurring':
                if (empty($post['recurrence_pattern'])) {
                    return $this->failed('循环发送必须设置循环模式');
                }
                
                // 根据循环模式设置相应参数
                switch ($post['recurrence_pattern']) {
                    case 'daily':
                        $dailyTimes = $post['daily_times'] ?? ['09:00'];
                        break;
                    case 'weekly':
                        $intervalMinutes = 7 * 24 * 60; // 一周
                        break;
                    case 'monthly':
                        $intervalMinutes = 30 * 24 * 60; // 一月
                        break;
                    default:
                        return $this->failed('循环模式参数错误');
                }
                break;
        }
        
        // 计算下次发送时间
        $nextSendTime = $this->calculateNextSendTime($sendMode, $post);
        
        // 构建数据
        $data = [
            'title' => trim($post['title']),
            'content' => trim($post['content']),
            'image_url' => $post['image_url'] ?? '',
            'send_mode' => $sendMode,
            'send_time' => $post['send_time'] ?? null,
            'daily_times' => isset($dailyTimes) ? implode(',', $dailyTimes) : null,
            'interval_minutes' => $intervalMinutes ?? null,
            'status' => 1, // 默认启用
            'start_date' => $post['start_date'] ?? date('Y-m-d'),
            'end_date' => $post['end_date'] ?? null,
            'next_send_time' => $nextSendTime,
            'created_by' => $this->adminId ?? 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // 开启事务
        Db::startTrans();
        try {
            // 插入广告
            $adId = $this->model->insertGetId($data);
            
            // 记录目标群组关联 (可以扩展一个关联表)
            // 这里暂时存储在广告记录的备注或扩展字段中
            
            Db::commit();
            
            return $this->success(['id' => $adId], '广告创建成功');
            
        } catch (\Exception $e) {
            Db::rollback();
            return $this->failed('广告创建失败：' . $e->getMessage());
        }
    }

    /**
     * 更新广告
     * POST /telegram/advertisement/update
     */
    public function updateAdvertisement()
    {
        $post = $this->request->post();
        
        if (empty($post['id'])) {
            return $this->failed('广告ID不能为空');
        }
        
        $advertisement = $this->model->where('id', $post['id'])->find();
        
        if (!$advertisement) {
            return $this->failed('广告不存在');
        }
        
        // 检查是否可编辑（已发送的广告可能不允许编辑）
        if ($advertisement['status'] == 2) {
            return $this->failed('已完成的广告不能编辑');
        }
        
        // 构建更新数据
        $updateData = [
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // 更新字段
        $allowedFields = ['title', 'content', 'image_url', 'send_mode', 'send_time', 
                         'daily_times', 'interval_minutes', 'start_date', 'end_date'];
        
        foreach ($allowedFields as $field) {
            if (isset($post[$field])) {
                switch ($field) {
                    case 'send_mode':
                        $sendModeMap = [
                            'immediate' => 1,
                            'scheduled' => 2,
                            'recurring' => 3
                        ];
                        if (isset($sendModeMap[$post[$field]])) {
                            $updateData[$field] = $sendModeMap[$post[$field]];
                        }
                        break;
                    case 'daily_times':
                        if (is_array($post[$field])) {
                            $updateData[$field] = implode(',', $post[$field]);
                        } else {
                            $updateData[$field] = $post[$field];
                        }
                        break;
                    default:
                        $updateData[$field] = $post[$field];
                }
            }
        }
        
        // 重新计算下次发送时间
        if (isset($updateData['send_mode']) || isset($updateData['send_time']) || 
            isset($updateData['daily_times']) || isset($updateData['interval_minutes'])) {
            $updateData['next_send_time'] = $this->calculateNextSendTime(
                $updateData['send_mode'] ?? $advertisement['send_mode'], 
                array_merge($advertisement, $updateData)
            );
        }
        
        $result = $this->model->where('id', $post['id'])->update($updateData);
        
        if ($result !== false) {
            return $this->success([], '广告更新成功');
        } else {
            return $this->failed('广告更新失败');
        }
    }

    /**
     * 删除广告
     * POST /telegram/advertisement/delete
     */
    public function deleteAdvertisement()
    {
        $id = $this->request->post('id');
        
        if (empty($id)) {
            return $this->failed('广告ID不能为空');
        }
        
        $advertisement = $this->model->where('id', $id)->find();
        
        if (!$advertisement) {
            return $this->failed('广告不存在');
        }
        
        // 检查是否可删除
        if ($advertisement['status'] == 2) {
            return $this->failed('已完成的广告不能删除');
        }
        
        // 开启事务
        Db::startTrans();
        try {
            // 删除广告
            $this->model->where('id', $id)->delete();
            
            // 删除相关的发送日志
            Db::name('tg_message_logs')
                ->where('source_id', $id)
                ->where('source_type', 'advertisement')
                ->delete();
            
            Db::commit();
            
            return $this->success([], '广告删除成功');
            
        } catch (\Exception $e) {
            Db::rollback();
            return $this->failed('广告删除失败：' . $e->getMessage());
        }
    }

    /**
     * 发送广告
     * POST /telegram/advertisement/send
     */
    public function sendAdvertisement()
    {
        $id = $this->request->post('id');
        
        if (empty($id)) {
            return $this->failed('广告ID不能为空');
        }
        
        $advertisement = $this->model->where('id', $id)->find();
        
        if (!$advertisement) {
            return $this->failed('广告不存在');
        }
        
        // 检查状态
        if ($advertisement['status'] != 1) {
            return $this->failed('只有启用状态的广告才能发送');
        }
        
        // 检查是否已经发送过(对于一次性广告)
        if ($advertisement['send_mode'] == 1 && $advertisement['is_sent'] == 1) {
            return $this->failed('该广告已经发送过了');
        }
        
        // 这里应该调用实际的发送逻辑
        // 暂时模拟发送成功
        $sendResult = $this->executeSendAdvertisement($advertisement);
        
        if ($sendResult['success']) {
            // 更新发送记录
            $updateData = [
                'total_sent_count' => $advertisement['total_sent_count'] + 1,
                'success_count' => $advertisement['success_count'] + $sendResult['success_count'],
                'failed_count' => $advertisement['failed_count'] + $sendResult['failed_count'],
                'last_sent_time' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // 如果是一次性发送，标记为已发送
            if ($advertisement['send_mode'] == 1) {
                $updateData['is_sent'] = 1;
                $updateData['status'] = 2; // 已完成
            } else {
                // 计算下次发送时间
                $updateData['next_send_time'] = $this->calculateNextSendTime($advertisement['send_mode'], $advertisement);
            }
            
            $this->model->where('id', $id)->update($updateData);
            
            return $this->success([
                'sent_count' => $sendResult['success_count'],
                'failed_count' => $sendResult['failed_count']
            ], '广告发送成功');
        } else {
            return $this->failed('广告发送失败：' . $sendResult['message']);
        }
    }

    /**
     * 获取广告统计
     * POST /telegram/advertisements/statistics
     */
    public function getAdvertisementStatistics()
    {
        $post = $this->request->post();
        $period = $post['period'] ?? 'today';
        
        // 构建时间条件
        $timeMap = $this->getTimeMap($period);
        
        // 基础统计
        $totalStats = [
            'total_ads' => $this->model->count(),
            'active_ads' => $this->model->where('status', 1)->count(),
            'completed_ads' => $this->model->where('status', 2)->count(),
            'cancelled_ads' => $this->model->where('status', 3)->count()
        ];
        
        // 周期内统计
        $periodStats = [
            'period_ads' => $this->model->where($timeMap)->count(),
            'period_sent' => $this->model->where($timeMap)->sum('total_sent_count'),
            'period_success' => $this->model->where($timeMap)->sum('success_count'),
            'period_failed' => $this->model->where($timeMap)->sum('failed_count')
        ];
        
        // 发送模式统计
        $sendModeStats = [];
        for ($i = 1; $i <= 3; $i++) {
            $count = $this->model->where('send_mode', $i)->count();
            $sendModeStats[] = [
                'mode' => $this->getSendModeText($i),
                'mode_value' => $i,
                'count' => $count
            ];
        }
        
        // 成功率
        $successRate = $periodStats['period_sent'] > 0 ? 
            round($periodStats['period_success'] / $periodStats['period_sent'] * 100, 2) : 0;
        
        // 每日趋势（最近7天）
        $dailyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $dayCount = $this->model->whereTime('created_at', $date)->count();
            $daySent = $this->model->whereTime('last_sent_time', $date)->sum('total_sent_count');
            
            $dailyTrend[] = [
                'date' => $date,
                'created_count' => $dayCount,
                'sent_count' => $daySent ?: 0
            ];
        }
        
        return $this->success([
            'total_stats' => $totalStats,
            'period_stats' => $periodStats,
            'success_rate' => $successRate,
            'send_mode_stats' => $sendModeStats,
            'daily_trend' => $dailyTrend,
            'period' => $period
        ]);
    }

    /**
     * 执行广告发送（模拟实现）
     */
    private function executeSendAdvertisement($advertisement)
    {
        // 这里应该是实际的Telegram发送逻辑
        // 暂时返回模拟结果
        
        // 获取活跃群组列表
        $activeGroups = Db::name('tg_crowd_list')
            ->where('is_active', 1)
            ->where('broadcast_enabled', 1)
            ->where('del', 0)
            ->select();
        
        $successCount = 0;
        $failedCount = 0;
        
        foreach ($activeGroups as $group) {
            // 模拟发送
            $sendSuccess = rand(0, 10) > 1; // 90%成功率
            
            // 记录发送日志
            $logData = [
                'message_type' => 'advertisement',
                'target_type' => 'group',
                'target_id' => $group['crowd_id'],
                'content' => $advertisement['content'],
                'send_status' => $sendSuccess ? 1 : 2,
                'error_message' => $sendSuccess ? null : '发送失败',
                'source_id' => $advertisement['id'],
                'source_type' => 'advertisement',
                'sent_at' => date('Y-m-d H:i:s')
            ];
            
            Db::name('tg_message_logs')->insert($logData);
            
            if ($sendSuccess) {
                $successCount++;
            } else {
                $failedCount++;
            }
        }
        
        return [
            'success' => true,
            'success_count' => $successCount,
            'failed_count' => $failedCount,
            'message' => '发送完成'
        ];
    }

    /**
     * 计算下次发送时间
     */
    private function calculateNextSendTime($sendMode, $data)
    {
        switch ($sendMode) {
            case 1: // 立即发送
                return date('Y-m-d H:i:s');
                
            case 2: // 定时发送
                return $data['send_time'] ?? null;
                
            case 3: // 循环发送
                if (!empty($data['interval_minutes'])) {
                    return date('Y-m-d H:i:s', time() + $data['interval_minutes'] * 60);
                } elseif (!empty($data['daily_times'])) {
                    $times = is_string($data['daily_times']) ? 
                        explode(',', $data['daily_times']) : $data['daily_times'];
                    
                    $now = date('H:i');
                    $today = date('Y-m-d');
                    
                    // 找到今天下一个发送时间点
                    foreach ($times as $time) {
                        if (trim($time) > $now) {
                            return $today . ' ' . trim($time) . ':00';
                        }
                    }
                    
                    // 如果今天没有了，返回明天第一个时间点
                    return date('Y-m-d', strtotime('+1 day')) . ' ' . trim($times[0]) . ':00';
                }
                break;
        }
        
        return null;
    }

    /**
     * 获取时间映射条件
     */
    private function getTimeMap($period)
    {
        switch ($period) {
            case 'today':
                return ['created_at', '>=', date('Y-m-d 00:00:00')];
                
            case 'week':
                return ['created_at', '>=', date('Y-m-d 00:00:00', strtotime('-7 days'))];
                
            case 'month':
                return ['created_at', '>=', date('Y-m-d 00:00:00', strtotime('-30 days'))];
                
            default:
                return ['created_at', '>=', date('Y-m-d 00:00:00')];
        }
    }

    /**
     * 获取发送模式文本
     */
    private function getSendModeText($mode)
    {
        $modes = [
            1 => '定时发送',
            2 => '每日定时', 
            3 => '循环发送'
        ];
        
        return $modes[$mode] ?? 'unknown';
    }

    /**
     * 获取状态文本
     */
    private function getStatusText($status)
    {
        $statuses = [
            0 => '草稿',
            1 => '激活',
            2 => '完成',
            3 => '取消'
        ];
        
        return $statuses[$status] ?? 'unknown';
    }
}