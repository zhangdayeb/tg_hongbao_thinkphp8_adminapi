<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | Workerman设置 仅对 php think worker:server 指令有效
// +----------------------------------------------------------------------
return [
    // 加入这句话，下面所有的都失效了，然后转移到这里，启动命令 php think worker start 
    'worker_class'	=>	'app\http\Worker',
    // // 扩展自身需要的配置  如果使用上面的 下面的这些 需要注释掉才行
    // 'protocol'       => 'websocket', // 协议 支持 tcp udp unix http websocket text
    // 'host'           => '0.0.0.0', // 监听地址
    // 'port'           => 2343, // 监听端口
    // 'socket'         => '', // 完整监听地址
    // 'context'        => [], // socket 上下文选项
    // 'worker_class'   => '', // 自定义Workerman服务类名 支持数组定义多个服务

    // // 支持workerman的所有配置参数
    // 'name'           => 'thinkphp',
    // 'count'          => 4,
    // 'daemonize'      => false,
    // 'pidFile'        => '',

    // // 支持事件回调
    // // onWorkerStart
    // 'onWorkerStart'  => function ($worker) {

    // },
    // // onWorkerReload
    // 'onWorkerReload' => function ($worker) {

    // },
    // // onConnect
    // 'onConnect'      => function ($connection) {

    // },
    // // onMessage
    // 'onMessage'      => function ($connection, $data) {
    // },
    // // onClose
    // 'onClose'        => function ($connection) {

    // },
    // // onError
    // 'onError'        => function ($connection, $code, $msg) {
    //     echo "error [ $code ] $msg\n";
    // },
];
