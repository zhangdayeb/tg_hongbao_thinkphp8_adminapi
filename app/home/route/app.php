<?php

use think\facade\Route;

/**
 * ====================================
 * ✅ 系统基础功能
 * ====================================
 */

// 存活检测
Route::rule('check/check$', 'api/check'); // 服务器存活检测

// 首页配置相关
Route::rule('index/ads$', 'common.Index/ads');                     // 轮播图
Route::rule('index/notice$', 'common.Index/get_notice');          // 公告
Route::rule('index/get_config$', 'common.Index/get_config');      // 单个配置
Route::rule('index/get_config_all$', 'icommon.Index/get_config_all'); // 所有配置
Route::rule('index/get_group_cfg$', 'common.Index/get_group_cfg');    // 分组配置


/**
 * ====================================
 * ✅ 文章模块
 * ====================================
 */
Route::rule('article/article_list$', 'article.Article/get_article_list');     // 获取文章列表
Route::rule('article/get_article_detail$', 'article.Article/get_article_detail'); // 获取文章详情


/**
 * ====================================
 * ✅ 二维码模块
 * ====================================
 */
Route::rule('qrcode$', 'common.qrcode/qrcode'); // 生成邀请二维码


/**
 * ====================================
 * ✅ 登录注册模块
 * ====================================
 */
Route::rule('login/login$', 'login.Login/login');                         // 登录
Route::rule('login/register_auto$', 'login.Login/register_auto');         // 自动注册（游客）
Route::rule('login/register_user$', 'login.Login/register_user');         // 注册普通用户
Route::rule('login/get_xieyi_detail$', 'login.Login/get_xieyi_detail');   // 用户协议
Route::rule('login/captcha$', 'login.Login/make_captcha');                // 图形验证码
Route::rule('login/forgot_captcha', 'login.Login/forgot_captcha');        // 忘记密码手机短信验证码
Route::rule('login/register_captcha', 'login.Login/register_captcha');    // 注册手机短信验证码 
Route::rule('login/check_captcha', 'login.Login/check_captcha');          // 检测手机短信验证码 
Route::rule('login/reset_password', 'login.Login/reset_password');        // 重置密码 
Route::rule('login/uploadUserData', 'login.Login/uploadUserData');        // 用户数据上传 


/**
 * ====================================
 * ✅ 用户基础信息
 * ====================================
 */
Route::rule('user/user_info$', 'user.Member/user_info');                 // 获取用户信息
Route::rule('user/be_vip$', 'user.Member/be_vip');                       // 成为VIP
Route::rule('user/update_avatar$', 'user.Member/updateAvatar');          // 更新头像
Route::rule('user/fenxiang$', 'user.Member/fenxiang');                   // 分享
Route::rule('user/update_pwd$', 'user.Member/update_pwd');               // 修改密码
Route::rule('user/update_withdraw_pwd$', 'user.Member/update_withdraw_pwd'); // 设置提现密码
Route::rule('user/update_nickname$', 'user.Member/update_nickname');     // 修改昵称 普通客户版本
Route::rule('user/update_nickname_tencent$', 'user.Member/update_nickname_tencent');     // 修改昵称 tencent 版本 需要传递 user_id
Route::rule('user/update_sex$', 'user.Member/update_sex');               // 修改性别
Route::rule('user/withdraw_captcha$', 'user.Member/withdraw_captcha');   // 提现验证码
Route::rule('user/uploads$', 'user.Member/uploads');                     // 上传身份证/银行卡
Route::post('user/update/:userId', 'user.Member/update');                // 更新用户背景图（POST）

// 用户实名
Route::rule('user/get_realName$', 'user.RealName/get_realName');        // 获取实名信息
Route::rule('user/add_realName$', 'user.RealName/add_realName_jiekou'); // 添加实名
Route::rule('user/edit_realName$', 'user.RealName/edit_realName');      // 编辑实名

// 用户银行卡
Route::rule('user/my_bank$', 'user.Bank/my_bank');                      // 银行账户列表
Route::rule('user/my_bank_one$', 'user.Bank/my_bank_one');             // 获取单个银行账户
Route::rule('user/add_bank$', 'user.Bank/add_bank');                   // 添加银行账户
Route::rule('user/edit_bank$', 'user.Bank/edit_bank');                 // 编辑银行账户
Route::rule('user/del_bank$', 'user.Bank/del_bank');                   // 删除银行账户

// 用户地址管理
Route::rule('user/my_address$', 'user.Address/my_address');              // 地址列表
Route::rule('user/set_default_address$', 'user.Address/set_default_address'); // 设置默认地址
Route::rule('user/add_address$', 'user.Address/add_address');            // 添加地址
Route::rule('user/edit_address$', 'user.Address/edit_address');          // 编辑地址
Route::rule('user/del_address$', 'user.Address/del_address');            // 删除地址

// 产品记录
Route::rule('user/product_buy_records$', 'user.Product/product_buy_records');           // 购买记录
Route::rule('user/product_buy_records_gongfu$', 'user.Product/product_buy_records_gongfu'); // 功夫记录
Route::rule('user/product_income_records$', 'user.Product/product_income_records');     // 收益记录

// 签到模块
Route::rule('user/qiandao_records$', 'user.QianDao/qiandao_records');   // 签到记录
Route::rule('user/qiandao$', 'user.QianDao/qiandao');                   // 签到操作


/**
 * ====================================
 * ✅ 金钱相关模块
 * ====================================
 */
Route::rule('user/withdraw$', 'user.Money/withdraw');                        // 提现
Route::rule('user/withdraw_records$', 'user.Money/withdraw_records');        // 提现记录
Route::rule('user/withdraw_status$', 'user.Money/withdraw_status');          // 提现状态
Route::rule('user/check_recharge$', 'user.Money/check_recharge');            // 检查充值
Route::rule('user/recharge$', 'user.Money/recharge');                        // 充值
Route::rule('user/recharge_records$', 'user.Money/recharge_records');        // 充值记录
Route::rule('user/money_balance_transfer$', 'user.Money/money_balance_transfer'); // 用户间转账
Route::rule('user/money_gongfu_change$', 'user.Money/money_gongfu_change');       // 可提现 → 可用
Route::rule('user/point_to_fund$', 'user.Money/point_to_fund');                  // 健康点转基金
Route::rule('user/money_change_records$', 'user.Money/money_change_records');    // 账变记录
Route::rule('user/money_records_by_type$', 'user.Money/money_records_by_type');  // 资金类型记录


/**
 * ====================================
 * ✅ 团队模块
 * ====================================
 */
Route::rule('team/team$', 'team.team/team');                                // 我的团队
Route::rule('team/team_level_member$', 'team.team/team_level_member');      // 团队等级成员
Route::rule('team/team_level_nums$', 'team.team/team_level_nums');          // 团队人数
Route::rule('team/team_jiangli$', 'team.team/team_jiangli');                // 团队奖励
Route::rule('team/team_paiming$', 'team.team/team_paiming');                // 团队排名
Route::rule('team/team_jiangli_lingqu$', 'team.team/team_jiangli_lingqu');  // 奖励领取


/**
 * ====================================
 * ✅ 产品模块
 * ====================================
 */
Route::rule('product/product_type_user$', 'product.product/product_type_user'); // 用户可用产品类别
Route::rule('product/product_type_all$', 'product.product/product_type_all');   // 所有产品类别
Route::rule('product/product_list$', 'product.product/product_list');           // 产品列表
Route::rule('product/product_detail$', 'product.product/product_detail');       // 产品详情
Route::rule('product/product_buy$', 'product.product/product_buy');             // 购买产品


/**
 * ====================================
 * ✅ 抽奖模块
 * ====================================
 */
Route::rule('lottery/lottery_list$', 'lottery.lottery/lottery_list');                   // 奖品列表
Route::rule('lottery/wuPin_list$', 'lottery.lottery/wuPin_list');                       // 中奖物品列表
Route::rule('lottery/lottery_draw$', 'lottery.lottery/lottery_draw');                   // 抽奖操作
Route::rule('lottery/lottery_record$', 'lottery.lottery/lottery_record');               // 抽奖记录
Route::rule('lottery/lottery_draw_record$', 'lottery.lottery/lottery_draw_record');     // 抽奖记录详情
Route::rule('lottery/ling_or_juan$', 'lottery.lottery/ling_or_juan');                   // 领奖或捐赠
Route::rule('lottery/lottery_times_record$', 'lottery.lottery/lottery_times_record');   // 抽奖机会记录


/**
 * ====================================
 * ✅ 支付模块（回调接口）
 * ====================================
 */
Route::rule('pay/choice$', 'pay.index/choice');                        // 选择支付方式
Route::rule('pay/asyncbacktest$', 'pay.back/async_back_test');        // 支付通道测试
Route::rule('pay/asyncback2$', 'pay.back/async_passage2');            // 小语支付
Route::rule('pay/asyncback3$', 'pay.back/async_passage3');            // 巅峰支付
Route::rule('pay/asyncback4$', 'pay.back/async_passage4');            // 苍龙支付
Route::rule('pay/asyncback5$', 'pay.back/async_passage5');            // 山河支付
Route::rule('pay/asyncback8$', 'pay.back/async_passage8');            // 金子支付
Route::rule('pay/asyncbackyian$', 'pay.back/asyncbackyian');          // 易安支付
Route::rule('pay/asyncbackjiguang$', 'pay.back/asyncbackjiguang');    // 极光支付


/**
 * ====================================
 * ✅ 麻将接口
 * ====================================
 */
Route::rule('majiang/info$', 'game.MaJiang/info'); // 获取麻将游戏信息


/**
 * ====================================
 * ✅ 朋友圈模块
 * ====================================
 */
Route::rule('im/index', 'im.MomentController/index');                // 获取朋友圈动态列表
Route::rule('im/read/:id', 'im.MomentController/read');              // 获取动态详情
Route::rule('im/sendmoment', 'im.MomentController/sendmoment');      // 发布动态
Route::rule('im/delete/:id', 'im.MomentController/delete');          // 删除动态
Route::rule('im/like/:id', 'im.MomentController/like');              // 点赞
Route::rule('im/unlike/:id', 'im.MomentController/unlike');          // 取消点赞
Route::rule('im/comment', 'im.MomentController/comment');            // 添加评论
Route::rule('im/comment/:id', 'im.MomentController/deleteComment');  // 删除评论
Route::rule('im/report/:id', 'im.MomentController/report');          // 举报动态
Route::rule('im/search_user', 'im.MomentController/search_user');    // 搜索用户跟群
