# 后台管理系统接口说明文档

## 接口基本信息

**Base URL:** `/admin/`
**Content-Type:** `application/json` 或 `application/x-www-form-urlencoded`
**请求方式:** 主要使用 `POST` 方法

### 全局响应格式
```json
{
    "code": 1,          // 业务状态码，1=成功，0=失败
    "message": "操作成功", // 业务信息
    "data": {}          // 业务数据
}
```

### 全局请求Header
```
X-Csrf-Token: string (required) - 身份令牌Token
```

---

## 1. 认证相关接口

### 1.1 管理员登录
**URL:** `POST /admin/login/index`

**请求参数:**
```json
{
    "user_name": "string",  // 用户名
    "pwd": "string",        // 密码
    "captcha": "string"     // 验证码
}
```

**响应示例:**
```json
{
    "code": 1,
    "message": "登录成功",
    "data": {
        "token": "xxx",
        "user_info": {}
    }
}
```

### 1.2 获取验证码
**URL:** `POST /admin/login/captcha`

### 1.3 验证码验证
**URL:** `POST /admin/login/captcha_check`

### 1.4 更新K线数据
**URL:** `POST /admin/login/updateKLine`

---

## 2. 文件上传接口

### 2.1 图片上传
**URL:** `POST /admin/upload/image`

**请求参数:**
- Content-Type: `multipart/form-data`
- 文件字段: `image`
- 支持格式: jpg, png, gif
- 文件大小限制: 100KB

**响应示例:**
```json
{
    "code": 1,
    "message": "上传成功",
    "data": [
        "http://domain.com/storage/topic/xxx.jpg"
    ]
}
```

### 2.2 视频上传
**URL:** `POST /admin/upload/video`

**请求参数:**
- Content-Type: `multipart/form-data` 
- 文件字段: `video`
- 支持格式: mp4
- 文件大小限制: 100KB

---

## 3. 管理员用户管理

### 3.1 管理员列表
**URL:** `POST /admin/admin/list`

**请求参数:**
```json
{
    "page": 1,           // 当前页码，默认1
    "limit": 10,         // 每页数量，默认10
    "user_name": "string", // 用户名搜索（可选）
    "role": "int",       // 角色ID搜索（可选）
    "market_level": "int" // 市场等级搜索（可选）
}
```

### 3.2 添加管理员
**URL:** `POST /admin/admin/add`

**请求参数:**
```json
{
    "pid": "int",           // 父级ID
    "user_name": "string",  // 用户名
    "pwd": "string",        // 密码
    "role": "int",          // 角色ID
    "market_level": "int",  // 市场等级
    "remarks": "string",    // 备注
    "invitation_code": "string" // 邀请码
}
```

### 3.3 编辑管理员
**URL:** `POST /admin/admin/edit`

**请求参数:**
```json
{
    "id": "int",           // 用户ID
    "user_name": "string", // 用户名
    "pwd": "string",       // 密码（可选）
    "role": "int",         // 角色ID
    "market_level": "int", // 市场等级
    "remarks": "string"    // 备注
}
```

### 3.4 管理员详情
**URL:** `POST /admin/admin/detail`

**请求参数:**
```json
{
    "id": "int" // 用户ID
}
```

### 3.5 删除管理员
**URL:** `POST /admin/admin/del`

**请求参数:**
```json
{
    "id": "int" // 用户ID
}
```

### 3.6 统计数据
**URL:** `POST /admin/admin/statistics`

**请求参数:**
```json
{
    "start": "2024-01-01", // 开始日期（可选）
    "end": "2024-01-31"    // 结束日期（可选）
}
```

**响应数据:**
```json
{
    "code": 1,
    "data": {
        "user_login_times": 100,        // 登录次数
        "user_qiandao_times": 50,       // 签到次数
        "register_numbers": 20,         // 注册人数
        "buy_product_nums": 30,         // 购买产品数量
        "buy_product_money": 5000,      // 购买产品金额
        "recharge_money_nums": 10000,   // 充值金额
        "withdraw_money_nums": 8000,    // 提现金额
        "tprofit": 2000,                // 毛利
        "recharge_nums": 15,            // 充值笔数
        "withdraw_nums": 12,            // 提现笔数
        "recharge_person_nums": 10,     // 充值人数
        "withdraw_person_nums": 8       // 提现人数
    }
}
```

---

## 4. 菜单管理

### 4.1 菜单列表（树形结构）
**URL:** `POST /admin/menu/list`

### 4.2 菜单列表（表格形式）
**URL:** `POST /admin/menu/column`

**请求参数:**
```json
{
    "page": 1,
    "limit": 10
}
```

### 4.3 添加菜单
**URL:** `POST /admin/menu/add`

**请求参数:**
```json
{
    "pid": "int",        // 父级菜单ID，0为顶级
    "title": "string",   // 菜单标题
    "path": "string",    // 菜单路径
    "icon": "string",    // 菜单图标（可选）
    "status": "int"      // 状态，1=启用，0=禁用
}
```

### 4.4 编辑菜单
**URL:** `POST /admin/menu/edit`

### 4.5 菜单详情
**URL:** `POST /admin/menu/detail`

### 4.6 删除菜单
**URL:** `POST /admin/menu/del`

### 4.7 菜单状态切换
**URL:** `POST /admin/menu/status`

---

## 5. 权限控制管理

### 5.1 权限控制器列表
**URL:** `POST /admin/action/list`

**请求参数:**
```json
{
    "page": 1,
    "limit": 10
}
```

### 5.2 添加权限控制器
**URL:** `POST /admin/action/add`

**请求参数:**
```json
{
    "title": "string", // 控制器标题
    "path": "string"   // 控制器路径
}
```

### 5.3 编辑权限控制器
**URL:** `POST /admin/action/edit`

### 5.4 删除权限控制器
**URL:** `POST /admin/action/del`

### 5.5 权限控制器状态
**URL:** `POST /admin/action/status`

### 5.6 获取当前用户可访问的控制器
**URL:** `POST /admin/action/con`

---

## 6. 角色管理

### 6.1 角色列表
**URL:** `POST /admin/role/list`

### 6.2 添加角色
**URL:** `POST /admin/role/add`

### 6.3 编辑角色
**URL:** `POST /admin/role/edit`

### 6.4 删除角色
**URL:** `POST /admin/role/del`

### 6.5 角色状态切换
**URL:** `POST /admin/role/status`

---

## 7. 权限分配管理

### 7.1 获取控制器列表（用于权限分配）
**URL:** `POST /admin/auth/action`

### 7.2 编辑角色控制器权限
**URL:** `POST /admin/auth/action_edit`

**请求参数:**
```json
{
    "id": "int",        // 角色ID
    "action": ["1","2","3"] // 控制器ID数组
}
```

### 7.3 获取菜单列表（用于权限分配）
**URL:** `POST /admin/auth/menu`

### 7.4 编辑角色菜单权限
**URL:** `POST /admin/auth/menu_edit`

**请求参数:**
```json
{
    "id": "int",        // 角色ID
    "menus": ["1","2","3"] // 菜单ID数组
}
```

### 7.5 角色菜单权限列表
**URL:** `POST /admin/role_menu/list`

### 7.6 添加角色菜单权限
**URL:** `POST /admin/role_menu/add`

### 7.7 编辑角色菜单权限
**URL:** `POST /admin/role_menu/edit`

### 7.8 角色API接口权限列表
**URL:** `POST /admin/power/list`

### 7.9 添加角色API接口权限
**URL:** `POST /admin/power/add`

### 7.10 编辑角色API接口权限
**URL:** `POST /admin/power/edit`

---

## 8. 日志管理

### 8.1 登录日志
**URL:** `POST /admin/login/log`

**请求参数:**
```json
{
    "page": 1,
    "limit": 10
}
```

### 8.2 后台操作日志
**URL:** `POST /admin/admin/log`

### 8.3 资金流动日志
**URL:** `POST /admin/money/log`

---

## 9. 提现管理

### 9.1 提现列表
**URL:** `POST /admin/pay/list`

**请求参数:**
```json
{
    "page": 1,
    "limit": 10,
    "user_name": "string", // 用户名搜索（可选）
    "status": "int",       // 状态搜索（可选）0=待处理,1=已通过,2=已拒绝
    "type": "int",         // 类型搜索（可选）
    "agents": "int",       // 代理商ID搜索（可选）
    "start": "2024-01-01", // 开始日期（可选）
    "end": "2024-01-31"    // 结束日期（可选）
}
```

### 9.2 提现通过
**URL:** `POST /admin/pay/pass`

**请求参数:**
```json
{
    "id": ["1","2","3"] // 提现订单ID数组
}
```

### 9.3 提现拒绝
**URL:** `POST /admin/pay/refuse`

### 9.4 设置线上线下
**URL:** `POST /admin/pay/is_line`

### 9.5 修改提现金额
**URL:** `POST /admin/pay/amount`

---

## 10. 充值管理

### 10.1 充值列表
**URL:** `POST /admin/recharge/list`

### 10.2 充值状态确认
**URL:** `POST /admin/recharge/status`

### 10.3 充值通过
**URL:** `POST /admin/recharge/pass`

### 10.4 充值拒绝
**URL:** `POST /admin/recharge/refuse`

---

## 11. 统计数据接口

### 11.1 注册统计

#### 今日注册与总注册
**URL:** `POST /admin/register/all`

**响应示例:**
```json
{
    "code": 1,
    "data": {
        "today": 10,  // 今日注册量
        "total": 1000 // 总注册量
    }
}
```

#### 今日注册量
**URL:** `POST /admin/register/today`

#### 总注册量
**URL:** `POST /admin/register/total`

### 11.2 充值统计

#### 今日充值与总充值
**URL:** `POST /admin/recharge/all`

**响应示例:**
```json
{
    "code": 1,
    "data": {
        "today": 5000,  // 今日充值金额
        "total": 100000 // 总充值金额
    }
}
```

#### 今日充值
**URL:** `POST /admin/recharge/today`

#### 总充值
**URL:** `POST /admin/recharge/total`

### 11.3 提现统计

#### 今日提现与总提现
**URL:** `POST /admin/withdrawal/all`

**响应示例:**
```json
{
    "code": 1,
    "data": {
        "today": 3000,  // 今日提现金额
        "total": 80000  // 总提现金额
    }
}
```

#### 今日提现
**URL:** `POST /admin/withdrawal/today`

#### 总提现
**URL:** `POST /admin/withdrawal/total`

---

## 12. Google验证码相关

### 12.1 获取二维码地址
**URL:** `POST /admin/google/qrcode`

**请求参数:**
```json
{
    "secret": "string" // Google密钥
}
```

### 12.2 生成Google密钥
**URL:** `POST /admin/google/secret`

---

## 13. 特殊管理功能

### 13.1 洗码记录列表
**URL:** `POST /admin/xima/list`

### 13.2 代理授权列表
**URL:** `POST /admin/agent_auth/list`

### 13.3 下注结算记录列表
**URL:** `POST /admin/record_money/list`

### 13.4 收款账户管理

#### 收款账户列表
**URL:** `POST /admin/zhanghu/list`

#### 收款账户详情
**URL:** `POST /admin/zhanghu/detail`

#### 添加收款账户
**URL:** `POST /admin/zhanghu/add`

#### 编辑收款账户
**URL:** `POST /admin/zhanghu/edit`

#### 删除收款账户
**URL:** `POST /admin/zhanghu/del`

#### 切换账户状态
**URL:** `POST /admin/zhanghu/status`

#### 批量操作状态
**URL:** `POST /admin/zhanghu/batch_status`

#### 获取统计数据
**URL:** `POST /admin/zhanghu/statistics`

#### 导出账户列表
**URL:** `POST /admin/zhanghu/export`

#### 获取支付方式配置
**URL:** `POST /admin/zhanghu/payment_methods`

#### 更新使用统计
**URL:** `POST /admin/zhanghu/update_usage`

### 13.5 通知消息模块

#### 获取最新记录
**URL:** `POST /admin/api/notification/latest-records`

#### 获取最新充值
**URL:** `POST /admin/api/notification/latest-recharges`

#### 获取最新提现
**URL:** `POST /admin/api/notification/latest-withdraws`

---

## 错误码说明

| Code | Message | 说明 |
|------|---------|------|
| 1 | 操作成功 | 请求成功 |
| 0 | 操作失败 | 一般性错误 |
| -1 | 参数错误 | 请求参数不正确 |
| -2 | 权限不足 | 没有访问权限 |
| -3 | 用户未登录 | 需要重新登录 |

---

## 注意事项

1. **权限验证**: 大部分接口需要管理员登录权限，通过session验证
2. **角色权限**: 不同角色的管理员可访问的接口不同，超级管理员(ID=2)拥有所有权限
3. **分页参数**: 列表接口通常支持`page`和`limit`参数进行分页
4. **搜索条件**: 列表接口支持多种搜索条件，通过POST参数传递
5. **数据格式**: 日期格式统一为`Y-m-d`或`Y-m-d H:i:s`
6. **文件上传**: 支持图片和视频上传，有文件大小和格式限制
7. **批量操作**: 部分接口支持批量操作，通过数组形式传递多个ID

## 开发建议

1. 建议使用统一的HTTP客户端库进行接口调用
2. 实现统一的错误处理机制
3. 对于列表接口，建议实现通用的分页组件
4. 文件上传建议添加进度显示功能
5. 重要操作建议添加二次确认
6. 建议实现接口请求的loading状态管理