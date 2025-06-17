<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

return [
    // 腾讯管理员 账号
    'tencent_adminuser' => 'administrator',
    // 腾讯 配置 用法 sdkapidi $this->app->config->get('app.sdkapiid') 
    // 腾讯 配置 密钥
    'tencent_sdkapiid'  => 20011222,
    'tencent_key'       => 'c2adf5369adda1b686ce91b0e90d9a9d2360fc72c0683d41395d555ec57ae5ec',
    //音频
    'audio_sdkappid'    =>20011222,
    'audio_sdkkey'      =>'c2adf5369adda1b686ce91b0e90d9a9d2360fc72c0683d41395d555ec57ae5ec',
    // 默认是否 添加
    'tencent_is_add_friend' => false,
    // 默认添加好友的 ID / 就是用户的名字 多用户 用 | 分割
    'tencent_add_friends_list' => 'test001',
    // 应用地址
    'app_host'         => env('app.host', ''),

    // 应用的命名空间
    'app_namespace'    => '',
    // 是否启用路由
    'with_route'       => true,
    // 默认应用
    'default_app'      => 'index',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',

    // 应用映射（自动多应用模式有效）
    'app_map'          => [],
    // 域名绑定（自动多应用模式有效）
    'domain_bind'      => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'    => [],

    // 异常页面的模板文件
    'exception_tmpl'   => app()->getThinkPath() . 'tpl/think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'    => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'   => true,
];
