<?php

use think\facade\Route;

// ====================
// 后台管理相关路由
// ====================
Route::rule('/$', 'Index/index');                              // 后台首页

// ====================
// 登录相关路由
// ====================
Route::rule('login/index$', 'login.Login/Login');                    // 登录


// ====================================================================
// 文件上传模块
// ====================================================================
Route::rule('upload/video$', '/upload.UploadData/video'); // 视频文件上传
Route::rule('upload/image$', '/upload.UploadData/image'); // 图片文件上传
Route::rule('upload/qrcode$', '/upload.UploadData/qrcode'); // 二维码图片上传
Route::rule('upload/qrcode_list$', '/upload.UploadData/qrcodeList'); // 二维码列表

// ====================================================================
// 后台管理员模块
// ====================================================================
Route::rule('admin/list$', '/user.Admins/index');    // 后台管理员列表
Route::rule('admin/add$', '/user.Admins/add');       // 添加后台管理员
Route::rule('admin/edit$', '/user.Admins/edit');     // 编辑后台管理员
Route::rule('admin/detail$', '/user.Admins/detail'); // 管理员信息查看
Route::rule('admin/del$', '/user.Admins/del');       // 删除后台管理员

// ====================
// 菜单管理相关路由
// ====================
Route::rule('menu/list$', 'auth.Menu/index');                  // 后台菜单列表
Route::rule('menu/column$', 'auth.Menu/lists');                // 后台表单列表

// ====================
// 权限控制相关路由
// ====================
Route::rule('action/list$', 'auth.Action/index');              // 后台控制列表
Route::rule('action/add$', 'auth.Action/add');                 // 后台控制添加
Route::rule('action/con$', 'auth.Action/list_con');            // 后台控制显示

// ====================
// 角色管理相关路由
// ====================
Route::rule('role/list$', 'auth.Role/index');                  // 角色列表
Route::rule('role/add$', 'auth.Role/add');                     // 角色添加
Route::rule('role/edit$', 'auth.Role/edit');                   // 角色修改
Route::rule('role/del$', 'auth.Role/del');                     // 角色删除
Route::rule('role/status$', 'auth.Role/status');               // 角色状态

// ====================
// 权限分配相关路由
// ====================
Route::rule('auth/action$', 'auth.BranchAuth/action_list');    // 控制器列表
Route::rule('auth/action_edit$', 'auth.BranchAuth/action_edit'); // 控制器修改
Route::rule('auth/menu$', 'auth.BranchAuth/menu_list');        // 菜单列表
Route::rule('auth/menu_edit$', 'auth.BranchAuth/menu_edit');   // 菜单修改
Route::rule('role_menu/list$', 'auth.RoleMenu/index');         // 角色菜单列表分组
Route::rule('role_menu/add$', 'auth.RoleMenu/add');            // 角色菜单添加
Route::rule('role_menu/edit$', 'auth.RoleMenu/edit');          // 角色菜单修改
Route::rule('power/list$', 'auth.RolePower/index');            // 角色API接口列表
Route::rule('power/add$', 'auth.RolePower/add');               // 角色API接口添加
Route::rule('power/edit$', 'auth.RolePower/edit');             // 角色API接口修改

// ====================
// 日志管理
// ====================
Route::rule('money/log$', 'log.MoneyLog/index');               // 资金流动日志

// ====================
// 提现管理
// ====================
Route::rule('pay/list$', 'log.PayWithdraw/index');             // 提现列表日志
Route::rule('pay/pass$', 'log.PayWithdraw/pass');              // 提现通过
Route::rule('pay/refuse$', 'log.PayWithdraw/refuse');          // 提现拒绝
Route::rule('pay/is_line$', 'log.PayWithdraw/is_line');        // 线上线下
Route::rule('pay/amount$', 'log.PayWithdraw/amount_edit');     // 修改金额

// ====================
// 充值管理
// ====================
Route::rule('recharge/list$', 'log.PayRecharge/index');        // 充值列表日志
Route::rule('recharge/status$', 'log.PayRecharge/status');     // 确认充值
Route::rule('recharge/pass$', 'log.PayRecharge/pass');         // 充值通过
Route::rule('recharge/refuse$', 'log.PayRecharge/refuse');     // 充值拒绝


// ====================================================================
// 用户管理模块
// ====================================================================
Route::rule('user/is_status$', '/user.User/is_status'); // 用户虚拟账号状态设置
Route::rule('user/list$', '/user.User/index');          // 用户列表
Route::rule('user/info$', '/user.User/user_info');      // 指定用户信息查看
Route::rule('user/edit$', '/user.User/edit');      // 编辑用户信息
Route::rule('user/add$', '/user.User/add');        // 添加用户
Route::rule('user/del$', '/user.User/del');        // 删除用户
Route::rule('user/detail$', '/user.User/detail');  // 用户详情查看
Route::rule('user/status$', '/user.User/status');  // 用户状态修改
Route::rule('money/edit$', '/user.User/money_edit');    // 用户余额修改

// ====================
// 注册统计
// ====================
Route::rule('register/all$', 'count.Register/index');         // 今日注册量与总注册列表
Route::rule('register/today$', 'count.Register/today_register'); // 今日注册量
Route::rule('register/total$', 'count.Register/total_register'); // 总注册

// ====================
// 充值统计
// ====================
Route::rule('recharge/all$', 'count.Recharge/index');         // 今日充值与总充值列表
Route::rule('recharge/today$', 'count.Recharge/today_recharge'); // 今日充值
Route::rule('recharge/total$', 'count.Recharge/total_recharge'); // 总充值

// ====================
// 提现统计
// ====================
Route::rule('withdrawal/all$', 'count.Withdrawal/index');     // 今日提现与总提现列表
Route::rule('withdrawal/today$', 'count.Withdrawal/today_withdrawal'); // 今日提现
Route::rule('withdrawal/total$', 'count.Withdrawal/total_withdrawal'); // 总提现

// ====================
// Google验证码相关
// ====================
Route::rule('google/qrcode$', 'base/captcha_url');            // 二维码地址
Route::rule('google/secret$', 'base/generate_code');          // Google密钥


// ====================================================================
// 公司收款账户管理模块
// ====================================================================
Route::rule('zhanghu/list$', '/log.ZhangHu/list');                             // 收款账户列表
Route::rule('zhanghu/detail$', '/log.ZhangHu/detail');                         // 收款账户详情
Route::rule('zhanghu/add$', '/log.ZhangHu/add');                               // 添加收款账户
Route::rule('zhanghu/edit$', '/log.ZhangHu/edit');                             // 编辑收款账户
Route::rule('zhanghu/del$', '/log.ZhangHu/del');                               // 删除收款账户
Route::rule('zhanghu/status$', '/log.ZhangHu/status');                         // 切换账户状态
Route::rule('zhanghu/batch_status$', '/log.ZhangHu/batchStatus');              // 批量操作状态
Route::rule('zhanghu/statistics$', '/log.ZhangHu/statistics');                 // 获取统计数据
Route::rule('zhanghu/export$', '/log.ZhangHu/export');                         // 导出账户列表
Route::rule('zhanghu/payment_methods$', '/log.ZhangHu/paymentMethods');        // 获取支付方式配置
Route::rule('zhanghu/update_usage$', '/log.ZhangHu/updateUsage');              // 更新使用统计

// ====================================================================
// 通知消息模块
// ====================================================================
Route::rule('api/notification/latest-records$', 'log.TongZhi/getLatestRecords');    // 获取最新记录
Route::rule('api/notification/latest-recharges$', 'log.TongZhi/getLatestRecharges'); // 获取最新充值
Route::rule('api/notification/latest-withdraws$', 'log.TongZhi/getLatestWithdraws'); // 获取最新提现
Route::rule('api/notification/mark-read$', 'log.TongZhi/markNotificationsRead');     // 标记已读
Route::rule('api/notification/test$', 'log.TongZhi/test');                           // 测试接口
Route::rule('api/notification/trigger$', 'log.TongZhi/triggerNotification');        // 触发通知



// ====================
// Telegram群组管理
// ====================
Route::rule('telegram/group/list$', 'telegram.TGTelegram/index');              // Telegram群组列表
Route::rule('telegram/group/detail$', 'telegram.TGTelegram/detail');           // Telegram群组详情
Route::rule('telegram/group/add$', 'telegram.TGTelegram/add');                 // 添加Telegram群组
Route::rule('telegram/group/edit$', 'telegram.TGTelegram/edit');               // 编辑Telegram群组
Route::rule('telegram/group/delete$', 'telegram.TGTelegram/delete');           // 删除Telegram群组
Route::rule('telegram/group/batch_delete$', 'telegram.TGTelegram/batchDelete'); // 批量删除Telegram群组
Route::rule('telegram/group/change_status$', 'telegram.TGTelegram/changeStatus'); // 修改群组状态
Route::rule('telegram/group/change_broadcast$', 'telegram.TGTelegram/changeBroadcast'); // 修改广播状态
Route::rule('telegram/group/statistics$', 'telegram.TGTelegram/statistics');   // Telegram群组统计
Route::rule('telegram/group/activity_ranking$', 'telegram.TGTelegram/activityRanking'); // 群组活跃度排行
Route::rule('telegram/group/export$', 'telegram.TGTelegram/export');           // 导出群组列表

// ====================
// Telegram红包管理
// ====================
Route::rule('telegram/redpacket/list$', 'telegram.TGRedPacket/index');         // 红包列表
Route::rule('telegram/redpacket/detail$', 'telegram.TGRedPacket/detail');      // 红包详情
Route::rule('telegram/redpacket/create_system$', 'telegram.TGRedPacket/createSystem'); // 创建系统红包
Route::rule('telegram/redpacket/revoke$', 'telegram.TGRedPacket/revoke');      // 撤回红包
Route::rule('telegram/redpacket/extend$', 'telegram.TGRedPacket/extend');      // 延期红包
Route::rule('telegram/redpacket/change_status$', 'telegram.TGRedPacket/changeStatus'); // 修改红包状态
Route::rule('telegram/redpacket/statistics$', 'telegram.TGRedPacket/statistics'); // 红包统计
Route::rule('telegram/redpacket/export$', 'telegram.TGRedPacket/export');      // 导出红包数据

// ====================
// Telegram红包记录管理
// ====================
Route::rule('telegram/redpacket/records$', 'telegram.TGRedPacket/records');    // 红包领取记录列表