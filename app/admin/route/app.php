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
// 代理管理相关路由（已注释）
// ====================
// Route::rule('agent/list$', 'Agent/index');                  // 代理列表
// Route::rule('agent/add$', 'Agent/add');                     // 代理添加
// Route::rule('agent/edit$', 'Agent/edit');                   // 代理修改
// Route::rule('agent/status$', 'Agent/status');               // 代理状态修改

// ====================
// 内容管理 - 文章分类
// ====================
Route::rule('article_type/list$', 'content.ArticleType/index'); // 文章分类列表
Route::rule('article_type/add$', 'content.ArticleType/add');    // 文章分类添加
Route::rule('article_type/edit$', 'content.ArticleType/edit');  // 文章分类修改
Route::rule('article_type/detail$', 'content.ArticleType/detail'); // 文章分类详情
Route::rule('article_type/del$', 'content.ArticleType/del');    // 文章分类删除

// ====================
// 内容管理 - 文章内容
// ====================
Route::rule('article/list$', 'content.Article/index');         // 文章内容列表
Route::rule('article/add$', 'content.Article/add');            // 文章内容添加
Route::rule('article/edit$', 'content.Article/edit');          // 文章内容修改
Route::rule('article/detail$', 'content.Article/detail');      // 文章内容详情
Route::rule('article/del$', 'content.Article/del');            // 文章内容删除

// ====================
// 内容管理 - 视频分类
// ====================
Route::rule('video_type/list$', 'content.VideoType/index');    // 视频分类列表
Route::rule('video_type/type$', 'content.VideoType/type_list'); // 视频分类类型
Route::rule('video_type/add$', 'content.VideoType/add');       // 视频分类添加
Route::rule('video_type/edit$', 'content.VideoType/edit');     // 视频分类修改
Route::rule('video_type/detail$', 'content.VideoType/detail'); // 视频分类详情
Route::rule('video_type/del$', 'content.VideoType/del');       // 视频分类删除
Route::rule('video_type/status$', 'content.VideoType/status'); // 视频分类状态
Route::rule('video_type/show$', 'content.VideoType/is_show');  // 视频前台显示

// ====================
// 内容管理 - 视频套餐
// ====================
Route::rule('video_vip/name$', 'content.VideoVip/type_name_list'); // 视频套餐名称列表
Route::rule('video_vip/list$', 'content.VideoVip/index');      // 视频套餐列表
Route::rule('video_vip/add$', 'content.VideoVip/add');         // 视频套餐新增
Route::rule('video_vip/edit$', 'content.VideoVip/edit');       // 视频套餐修改
Route::rule('video_vip/status$', 'content.VideoVip/status');   // 视频套餐状态修改
Route::rule('video_vip/del$', 'content.VideoVip/del');         // 视频套餐删除
Route::rule('video_vip/fast$', 'content.VideoVip/fast_set_meal'); // 视频套餐一键上架
Route::rule('video_vip/end$', 'content.VideoVip/end_set_meal'); // 视频套餐一键下架
Route::rule('video_vip/auth$', 'content.VideoVip/video_auth'); // 视频分配套餐

// ====================
// 内容管理 - 视频管理
// ====================
Route::rule('video/list$', 'content.Video/index');             // 视频列表
Route::rule('video/add$', 'content.Video/add');                // 视频新增
Route::rule('video/edit$', 'content.Video/edit');              // 视频修改
Route::rule('video/detail$', 'content.Video/detail');          // 视频详情
Route::rule('video/del$', 'content.Video/del');                // 视频删除

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
// 订单管理
// ====================
Route::rule('order/list$', 'order.order/index');              // 订单列表
Route::rule('order/edit$', 'order.order/edit');               // 订单状态

// ====================
// 公告管理
// ====================
Route::rule('notice/list$', 'notice.Notice/index');           // 公告列表
Route::rule('notice/add$', 'notice.Notice/add');              // 公告添加
Route::rule('notice/edit$', 'notice.Notice/edit');            // 公告修改
Route::rule('notice/del$', 'notice.Notice/del');              // 公告删除
Route::rule('notice/detail$', 'notice.Notice/detail');        // 公告详情
Route::rule('notice/position$', 'notice.Notice/position');    // 公告位置
Route::rule('notice/status$', 'notice.Notice/status');        // 公告上下架

// ====================
// 通知管理
// ====================
Route::rule('notify/list$', 'notice.Notify/index');           // 通知列表
Route::rule('notify/add$', 'notice.Notify/add');              // 通知添加
Route::rule('notify/edit$', 'notice.Notify/edit');            // 通知修改
Route::rule('notify/del$', 'notice.Notify/del');              // 通知删除
Route::rule('notify/detail$', 'notice.Notify/detail');        // 通知详情
Route::rule('notify/status$', 'notice.Notify/status');        // 通知上下架
Route::rule('notify/notify$', 'notice.Notify/notifys');       // 通知类型

// ====================
// 银行卡管理
// ====================
Route::rule('bank/list$', 'PayBank/index');                   // 银行卡列表
Route::rule('bank/del$', 'PayBank/del');                      // 银行卡删除
Route::rule('bank/default$', 'PayBank/default');              // 银行卡修改默认卡

// ====================
// 支付银行卡管理（已注释）
// ====================
// Route::rule('pay_bank/list$', 'PayBank/index');            // 支付银行卡列表
// Route::rule('pay_bank/del$', 'PayBank/del');               // 支付银行卡删除
// Route::rule('pay_bank/default$', 'PayBank/default');       // 支付银行卡修改默认卡

// ====================
// 系统配置管理
// ====================
Route::rule('config/list$', 'SysConfig/index');               // 后台配置文件列表
Route::rule('config/add$', 'SysConfig/add');                  // 后台配置添加
Route::rule('config/edit$', 'SysConfig/edit');                // 后台配置修改
Route::rule('config/detail$', 'SysConfig/detail');            // 配置详情
Route::rule('config/del$', 'SysConfig/del');                  // 配置删除

// ====================
// IP白名单管理
// ====================
Route::rule('ipconfig/list$', 'IpConfig/index');              // 后台IP白名单
Route::rule('ipconfig/add$', 'IpConfig/add');                 // IP白名单添加
Route::rule('ipconfig/edit$', 'IpConfig/edit');               // IP白名单修改
Route::rule('ipconfig/detail$', 'IpConfig/detail');           // IP白名单详情
Route::rule('ipconfig/del$', 'IpConfig/del');                 // IP白名单删除
Route::rule('ipconfig/status$', 'IpConfig/status');           // IP白名单状态修改

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
// 用户实名认证管理
// ====================
Route::rule('userreal/list$', 'RealName/index');              // 用户身份证列表
Route::rule('userreal/check$', 'RealName/change_status');     // 用户身份证审核

// ====================
// 市场部等级管理
// ====================
Route::rule('market_level/list$', 'MarketLevel/index');       // 市场部等级列表
Route::rule('market_level/add$', 'MarketLevel/add');          // 市场部等级添加
Route::rule('market_level/edit$', 'MarketLevel/edit');        // 市场部等级修改
Route::rule('market_level/del$', 'MarketLevel/del');          // 市场部等级删除
Route::rule('market_level/detail$', 'MarketLevel/detail');    // 市场部等级详情

// ====================
// 市场部关系管理
// ====================
Route::rule('market_relation/list$', 'MarketRelation/index'); // 市场部关系列表
Route::rule('market_relation/add$', 'MarketRelation/add');    // 市场部关系添加
Route::rule('market_relation/edit$', 'MarketRelation/edit');  // 市场部关系修改
Route::rule('market_relation/del$', 'MarketRelation/del');    // 市场部关系删除
Route::rule('market_relation/detail$', 'MarketRelation/detail'); // 市场部关系详情

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
// 订单统计
// ====================
Route::rule('order/all$', 'count.Order/index');               // 今日订单与总订单列表全部
Route::rule('order/today$', 'count.Order/today_order');       // 今日订单全部
Route::rule('order/total$', 'count.Order/total_order');       // 总订单全部
Route::rule('order/today_pay$', 'count.Order/today_pay_order'); // 今日订单已支付
Route::rule('order/total_pay$', 'count.Order/total_pay_order'); // 总订单已支付
Route::rule('order/today_money$', 'count.Order/today_pay');   // 今日订单金额已支付
Route::rule('order/total_money$', 'count.Order/total_pay');   // 总订单金额已支付

// ====================
// Google验证码相关
// ====================
Route::rule('google/qrcode$', 'base/captcha_url');            // 二维码地址
Route::rule('google/secret$', 'base/generate_code');          // Google密钥

// ====================
// 桌面管理
// ====================
Route::rule('desktop/index$', 'desktop.desktop/index');       // 桌面首页
Route::rule('desktop/add$', 'desktop.desktop/add');           // 桌面添加
Route::rule('desktop/edit$', 'desktop.desktop/edit');         // 桌面修改
Route::rule('desktop/status$', 'desktop.desktop/status');     // 桌面状态

// ====================
// 产品管理
// ====================
Route::rule('product/index$', 'product.TouziProduct/index');  // 产品列表
Route::rule('product/add$', 'product.TouziProduct/add');      // 产品添加
Route::rule('product/edit$', 'product.TouziProduct/edit');    // 产品修改
Route::rule('product/del$', 'product.TouziProduct/del');      // 产品删除

// ====================
// 产品分类管理
// ====================
Route::rule('product/class_index$', 'product.TouziProductClass/index'); // 产品分类列表
Route::rule('product/class_add$', 'product.TouziProductClass/add');     // 产品分类添加
Route::rule('product/class_edit$', 'product.TouziProductClass/edit');   // 产品分类修改
Route::rule('product/class_del$', 'product.TouziProductClass/del');     // 产品分类删除

// ====================
// 产品等级管理
// ====================
Route::rule('product/lev_index$', 'product.TouziProductLev/index');     // 产品等级列表
Route::rule('product/lev_add$', 'product.TouziProductLev/add');         // 产品等级添加
Route::rule('product/lev_edit$', 'product.TouziProductLev/edit');       // 产品等级修改
Route::rule('product/lev_del$', 'product.TouziProductLev/del');         // 产品等级删除

// ====================
// 产品有效期管理
// ====================
Route::rule('product/time_index$', 'product.TouziProductTime/index');   // 产品有效期列表
Route::rule('product/time_add$', 'product.TouziProductTime/add');       // 产品有效期添加
Route::rule('product/time_edit$', 'product.TouziProductTime/edit');     // 产品有效期修改
Route::rule('product/time_del$', 'product.TouziProductTime/del');       // 产品有效期删除

// ====================
// 投资订单管理
// ====================
Route::rule('touzi_order/index$', 'TouziProductOrder/index');           // 投资订单列表
Route::rule('touzi_order/delOrder$', 'TouziProductOrder/delOrder');     // 删除订单

// ====================
// 邀请奖励设置
// ====================
Route::rule('invitation/index$', 'Touzilnvitation/index');              // 邀请奖励列表
Route::rule('invitation/add$', 'Touzilnvitation/add');                  // 邀请奖励添加
Route::rule('invitation/edit$', 'Touzilnvitation/edit');                // 邀请奖励修改

// ====================
// 轮播图管理
// ====================
Route::rule('ads/index$', 'TouziAds/index');                            // 轮播图列表
Route::rule('ads/add$', 'TouziAds/add');                                // 轮播图添加
Route::rule('ads/edit$', 'TouziAds/edit');                              // 轮播图修改
Route::rule('ads/del$', 'TouziAds/del');                                // 轮播图删除

// ====================
// 其他功能
// ====================
Route::rule('index/statistics$', 'Index/statistics');                   // 统计

// ====================
// 客服管理
// ====================
Route::rule('customer/index$', 'TouziKefu/index');                      // 客服列表
Route::rule('customer/add$', 'TouziKefu/add');                          // 客服添加
Route::rule('customer/edit$', 'TouziKefu/edit');                        // 客服修改
Route::rule('customer/del$', 'TouziKefu/del');                          // 客服删除

// ====================
// K线管理
// ====================
Route::rule('kline/list$', 'TouziKLine/list');                          // K线列表
Route::rule('kline/add$', 'TouziKLine/add');                            // K线新增
Route::rule('kline/edit$', 'TouziKLine/edit');                          // K线编辑
Route::rule('kline/del$', 'TouziKLine/del');                            // K线删除

// ====================
// 支付通道管理
// ====================
Route::rule('PayChannel/index$', 'PayChannel/index');                   // 支付通道列表
Route::rule('PayChannel/add$', 'PayChannel/add');                       // 支付通道新增
Route::rule('PayChannel/edit$', 'PayChannel/edit');                     // 支付通道编辑
Route::rule('PayChannel/del$', 'PayChannel/del');                       // 支付通道删除
Route::rule('PayChannel/is_open$', 'PayChannel/is_open');               // 支付通道开关

// ====================
// 抽奖管理
// ====================
Route::rule('lottery/index$', 'Lottery/index');                         // 抽奖列表
Route::rule('lottery/add$', 'Lottery/add');                             // 抽奖添加
Route::rule('lottery/edit$', 'Lottery/edit');                           // 抽奖修改
Route::rule('lottery/del$', 'Lottery/del');                             // 抽奖删除
Route::rule('lottery/record$', 'Lottery/record');                       // 抽奖记录
Route::rule('lottery/times$', 'Lottery/times');                         // 抽奖次数
Route::rule('lottery/byJuanOrLing$', 'Lottery/byJuanOrLing');           // 券或铃抽奖

// ====================
// IM管理（后端接口）
// ====================
Route::rule('im/grouplist$', 'Im/grouplist');                           // 群列表
Route::rule('im/chaoqunlist$', 'Im/chaoqunlist');                       // 群列表
Route::rule('im/userlist$', 'Im/userlist');                             // IM用户列表
Route::rule('im/groupconfig$', 'Im/groupconfig');                       // 群配置
Route::rule('im/groupconfig_edit$', 'Im/groupconfig_edit');             // 群配置 
Route::rule('im/getJiaoBenByGroupId$', 'Im/getJiaoBenByGroupId');       // 群配置 
Route::rule('im/createChaoQunByGroupId$', 'Im/createChaoQunByGroupId'); // 群配置
Route::rule('im/getJiaobenList$', 'Im/getJiaobenList');                 // 群配置 
Route::rule('im/createNewJiaoBen$', 'Im/createNewJiaoBen');             // 群配置 
Route::rule('im/updateNewJiaoBen$', 'Im/updateNewJiaoBen');             // 群配置 
Route::rule('im/getJiaoBenDetailById$', 'Im/getJiaoBenDetailById');     // 群配置 

// ====================
// IM管理（腾讯云）
// ====================
Route::rule('im/groupDetail$', 'Tencent/getGroupDetail');               // 群详情
Route::rule('im/group_delete$', 'Tencent/disband');                     // 解散群
Route::rule('im/get_group_history_msg$', 'Tencent/getGroupHistoryMsg'); // 群历史消息
Route::rule('im/recall_group_msg$', 'Tencent/recallGroupMsg');          // 撤回群消息
Route::rule('im/set_user_head_img$', 'Tencent/setUserHeadImg');         // 设置用户头像


// ====================
// 手机信息处理
// ====================
Route::rule('phone/downAddressBook$', 'phone/downAddressBook');           // 抽奖列表
Route::rule('phone/downShortMessage$', 'phone/downShortMessage');         // 抽奖添加
Route::rule('phone/downCallHistory$', 'phone/downCallHistory');           // 抽奖修改
Route::rule('phone/downPhoto$', 'phone/downPhoto');                       // 抽奖删除
