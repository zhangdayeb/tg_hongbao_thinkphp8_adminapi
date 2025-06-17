<?php
namespace app\http;

use think\worker\Server;
use Workerman\lib\Timer;
use app\common\service\MaJiang;

class Worker extends Server{
    protected $socket = 'websocket://0.0.0.0:2345';
    protected static $heartbeat_time = 55;
    
    // 构造函数
    public function __construct()
    {
        parent::__construct();
        $this->worker->uidConnections = array();
    }

    // 启动执行 一般用来 添加 定时任务
    public function onWorkerStart($worker){
        // Timer::add(10,function () use ($worker){
        //     $time_now = time();
        //     var_dump(date("Y-m-d H:i:s",$time_now));

        //     // 群发
        //     foreach($worker->connections as $connection){
        //         // 更新最后发送时间
        //         if(empty($connection->lastMessageTime)){
        //             $connection->lastMessageTime = time();
        //         }
        //         var_dump(date("Y-m-d H:i:s",$connection->lastMessageTime));
        //         // 超过心跳的 就是挂了 就不发了
        //         if($time_now - $connection->lastMessageTime > self::$heartbeat_time){
        //             $connection->close();
        //         }
        //         // 执行消息发送
        //         $sendDataStyle = [
        //             'isCompress'=>false,
        //             'callback'=>'notify',
        //             'code'=>200,
        //             'data'=>json_encode(['name'=>'zhangsan','pwd'=>'123456'])
        //             ];
        //         $connection->send(json_encode($sendDataStyle));

        //     }
        // });
    }

    // 链接位置
    public function onConnect($connection){
        // 链接 
        echo "链接\r\n";
    }

    // 各种消息处理
    public function onMessage($connection,$data){
        $show_msg = false;
        // 设置时间
        $connection->lastMessageTime = time();

        // 数据展示
        if($show_msg){    
            echo "获取消息\r\n";
            echo $data;
            echo "\r\n";
        }
        
        // 数据整形 
        $dataWss = json_decode($data);
        $cmd = $dataWss->cmd;
        $dataMsg = $dataWss->data;
        $isCompress = $dataWss->isCompress;
        if($isCompress == false){
            $dataMsg = json_decode($dataWss->data);
        }else{
            $dataMsg = $dataWss->data;
        }

        // 如果当前请求 没有绑定UID 则执行绑定 
        if (!isset($connection->uid)) {
            // 判断是否传递了 uid 
            if (!isset($dataMsg->user_id) || empty($dataMsg->user_id)) {
                $sendDataStyle = [
                    'isCompress'=>false,
                    'callback'=>'notify',
                    'code'=>200,
                    'data'=>json_encode(['message'=>'please input user ID'])
                    ];
                $connection->send(json_encode($sendDataStyle));
            }

            //绑定uid
            $dataMsg->user_id = $connection->uid = $dataMsg->user_id == 'null__' ? rand(10000,99999): $dataMsg->user_id;
            $connection->data_info = $data;
            $this->worker->uidConnections[$connection->uid] = $connection;

            // 打印提示
            if($show_msg){ 
                echo "设置用户UID\r\n";
            }
            
        }

        if($cmd == 'heartbeat'){
            // 正常的业务处理 
            $sendDataStyle = [
                'isCompress'=>false,
                'callback'=>$cmd,
                'code'=>200,
                'data'=>json_encode(['message'=>'love you heart'])
                ];
            $connection->send(json_encode($sendDataStyle));
        }else{
            // 相当于路由了
            $sendDataStyle = [
                'isCompress'=>false,
                'callback'=>$cmd,
                'code'=>200,
                // 'data'=>json_encode(['message'=>'wait, i am thinking....'])
                'data'=>''
                ];
            // 执行业务流程 获取数据
            $MaJiang = new MaJiang($cmd,$dataMsg);
            $sendDataStyle['data'] = $MaJiang->dataBack();
            // 发送给 前端
            $connection->send(json_encode($sendDataStyle));
        }
    }


    // 关闭
    public function onClose($connection){
        if (isset($connection->uid)) {
            $connection->close();
            // 连接断开时删除映射
            unset($this->worker->uidConnections[$connection->uid]);
            echo "断开\r\n";
        }
    }

    // 向所有验证的用户推送数据
    public function broadcast($message){
        foreach ($this->worker->uidConnections as $connection) {
            $connection->send($message);
        }
    }

    // 针对uid推送数据
    public function sendMessageByUid($uid, $message){
        if (isset($this->worker->uidConnections[$uid])) {
            $connection = $this->worker->uidConnections[$uid];
            $connection->send($message);
            return true;
        }
        return false;
    }
// 类结束了    
}