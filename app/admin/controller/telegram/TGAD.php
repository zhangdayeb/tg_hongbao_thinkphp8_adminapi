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
        
        
        // 开启事务
        Db::startTrans();
        try {
            // 删除广告
            $this->model->where('id', $id)->delete();
                       
            Db::commit();
            
            return $this->success([], '广告删除成功');
            
        } catch (\Exception $e) {
            Db::rollback();
            return $this->failed('广告删除失败：' . $e->getMessage());
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