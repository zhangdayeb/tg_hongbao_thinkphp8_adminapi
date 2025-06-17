<?php

use think\facade\Route;

// ====================
// 登录相关路由
// ====================
Route::rule('login/index$', 'Login/index');                    // 登录
Route::rule('login/captcha$', 'Login/captcha');                // 验证码
Route::rule('login/captcha_check$', 'Login/captcha_check');    // 验证码验证
Route::rule('login/agent$', 'agentLogin/index');               // 服务商登录
Route::rule('login/she$', 'Login/she');                        // 服务商登录
Route::rule('login/updateKLine$', 'Login/updateKLine');        // 更新K线
Route::rule('login/log$', 'log.LoginLog/index');               // 登录日志

// ====================
// 文件上传相关路由
// ====================
Route::rule('upload/image$', 'UploadData/image');              // 上传图片
Route::rule('upload/video$', 'UploadData/video');              // 上传视频
Route::rule('upload/index$', 'VideoFf/index');                 // 上传首页

// ====================
// 后台管理相关路由
// ====================
Route::rule('/$', 'Index/index');                              // 后台首页
Route::rule('admin/list$', 'Index/index');                     // 后台用户列表
Route::rule('admin/add$', 'Index/add');                        // 后台用户添加
Route::rule('admin/edit$', 'Index/edit');                      // 后台用户修改
Route::rule('admin/detail$', 'Index/detail');                  // 后台用户信息查看
Route::rule('admin/del$', 'Index/del');                        // 后台用户删除
Route::rule('admin/excel$', 'Login/exportWithdraw');           // 提现账单导出
Route::rule('admin/log$', 'log.AdminLog/index');               // 后台操作日志

// ====================
// 菜单管理相关路由
// ====================
Route::rule('menu/list$', 'auth.Menu/index');                  // 后台菜单列表
Route::rule('menu/add$', 'auth.Menu/add');                     // 后台菜单添加
Route::rule('menu/edit$', 'auth.Menu/edit');                   // 后台菜单修改
Route::rule('menu/detail$', 'auth.Menu/detail');               // 后台菜单详情
Route::rule('menu/del$', 'auth.Menu/del');                     // 后台菜单删除
Route::rule('menu/column$', 'auth.Menu/lists');                // 后台表单列表
Route::rule('menu/status$', 'auth.Menu/status');               // 后台菜单状态

// ====================
// 权限控制相关路由
// ====================
Route::rule('action/list$', 'auth.Action/index');              // 后台控制列表
Route::rule('action/add$', 'auth.Action/add');                 // 后台控制添加
Route::rule('action/edit$', 'auth.Action/edit');               // 后台控制修改
Route::rule('action/del$', 'auth.Action/del');                 // 后台控制删除
Route::rule('action/status$', 'auth.Action/status');           // 后台控制状态
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



// ====================
// 银行卡管理
// ====================
Route::rule('bank/list$', 'PayBank/index');                   // 银行卡列表
Route::rule('bank/del$', 'PayBank/del');                      // 银行卡删除
Route::rule('bank/default$', 'PayBank/default');              // 银行卡修改默认卡


// ====================
// 系统配置管理
// ====================
Route::rule('config/list$', 'SysConfig/index');               // 后台配置文件列表
Route::rule('config/add$', 'SysConfig/add');                  // 后台配置添加
Route::rule('config/edit$', 'SysConfig/edit');                // 后台配置修改
Route::rule('config/detail$', 'SysConfig/detail');            // 配置详情
Route::rule('config/del$', 'SysConfig/del');                  // 配置删除


// ====================
// 用户管理
// ====================
Route::rule('money/edit$', 'Member/money_edit');              // 用户余额修改
Route::rule('money/caijin$', 'Member/caijin');                // 用户身份证审核
Route::rule('user/is_status$', 'Member/is_status');           // 用户是否虚拟账号设置
Route::rule('user/list$', 'Member/index');                    // 用户列表
Route::rule('user/agent$', 'Member/agent_data');              // 代理商信息
Route::rule('user/edit$', 'Member/edit');                     // 用户修改
Route::rule('user/add$', 'Member/add');                       // 用户添加
Route::rule('user/del$', 'Member/del');                       // 用户删除
Route::rule('user/detail$', 'Member/detail');                 // 用户详情
Route::rule('user/status$', 'Member/status');                 // 用户状态修改
Route::rule('user/teamlist$', 'Member/team_list');            // 团队状况
Route::rule('user/teamfeng$', 'Member/team_feng');            // 团队整线封印解封
Route::rule('user/teamshow$', 'Member/team_show');            // 团队整线显示
Route::rule('user/is_real_name$', 'Member/is_real_name');     // 用户实名状态修改
Route::rule('user/is_status_user$', 'Member/is_status_user'); // 用户状态修改
Route::rule('user/is_status_money$', 'Member/is_status_money'); // 用户金额状态修改
Route::rule('user/is_status_transfer$', 'Member/is_status_transfer'); // 用户转账状态修改
Route::rule('user/is_status_income$', 'Member/is_status_income'); // 用户收入状态修改
Route::rule('user/t_xiaxian$', 'Member/t_xiaxian');           // 用户下线
Route::rule('user/product_order_list$', 'Member/product_order_list'); // 用户产品订单列表
Route::rule('user/iplist$', 'Member/iplist');                 // 用户IP列表
Route::rule('user/updateIDCard$', 'Member/updateIDCard');     // 更新身份证
Route::rule('user/updateBankCard$', 'Member/updateBankCard'); // 更新银行卡
Route::rule('user/getAddress$', 'Member/getAddress');         // 用户地址获取
Route::rule('user/ranking$', 'Member/ranking');               // 用户排名


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


// ====================
// 其他功能
// ====================
Route::rule('index/statistics$', 'Index/statistics');                   // 统计


