<?php
namespace app\common\service;

use app\common\model\User;
use app\common\model\QiPaiSCMJRoom;
use think\facade\Cache;

class gameUser {
    /** 用户昵称 */
    public $user_nick_name = '未命名';
    /** 用户位置 dong xi nan bei  dong 为庄家 */
    public $user_position = 'dong';
    /** 用户显示的位置 这个变量是留给前端进行计算的 */
    public $user_show_position = 'none';
    /** 用户头像 */
    public $user_head_img_spriteframe = 'head_1';
    /** 用户性别 */
    public $user_sex = 'nv';
    /** 用户ID */
    public $user_id = '1';
    /** 用户积分 */
    public $user_score = '2000';
    /** 用户游戏角色 zhuang xian */
    public $user_role = 'zhuang';
    /** 用户可以执行的动作 */
    public $user_action = 'wait';

    /** ===============具体牌相关数据=============== */
    /** 用户 缺一门 */
    public $user_pai_que = 'wan';
    /** 用户手里的牌 */
    public $user_pai_hand = [1,2,3,4,5,6,7,8,9];
    /** 用户手里的牌 */
    public $user_pai_hand_zhi = [1,2,3,4];// 例如：[1,2,3]
    public $user_pai_hand_zhi_x = 0;
    public $user_pai_hand_zhi_y = 0;
    /** 用户 打出去的牌 */
    public $user_pai_hand_chu = [1,2,3,4,5,6,7,11,12,13,14,15,16,17];
    public $user_pai_hand_chu_x = 0;
    public $user_pai_hand_chu_y = 0;
    /** 用户 顺子 */
    public $user_pai_shunzi = [[1,2,3]]; // 参考 [[1,2,3],[4,5,6]];
    public $user_pai_shunzi_x = 0;
    public $user_pai_shunzi_y = 0;
    /** 用户 刻子 */
    public $user_pai_kezi = [4]; // 例如：[1,2,3]
    public $user_pai_kezi_x = 0;
    public $user_pai_kezi_y = 0;
    /** 用户 杠子 */
    public $user_pai_gangzi = [5]; // 例如：[1,2,3]
    public $user_pai_gangzi_x = 0;
    public $user_pai_gangzi_y = 0;
    /** ===============牌型展示结束=============== */

    /** 用户 动作 吃 */
    public $user_action_chi = [];
    /** 用户 动作 碰 */
    public $user_action_peng = [];
    /** 用户 动作 杠 */
    public $user_action_gang = [];

    // 构造函数
    function __construct($user_nick_name,$user_position){
        $this->user_nick_name=$user_nick_name;
        $this->user_position=$user_position; 
    }

// 类结束了    
}

// 这里执行的是 wss 返回数据
// 合计 108 张 万1-36 条37-72 饼73-108 对9进行求余 才行
class MaJiang
{
    // 调用函数
    private $cmd;
    // 传递的数据
    private $data;
    // 房间信息
    private $room;
    // 可以执行的函数
    private $good_cmds = ['good_cmd','chuPai','getBaseData','getPaiData','getMaJiangData','getRunData','updateUserUIShow','getSingleRunData'];
    // 配套缓存
    private $redis;


    // 当前请求用户的 东南西北
    private $user_position;    
    // 剩余多少牌
    private $label_paishu_string;
    // 当前局数
    private $label_jushu_string;
    // 东用户
    private $user_dong;
    // 西用户
    private $user_xi;
    // 南用户
    private $user_nan;
    // 北用户
    private $user_bei;

    // 临时存 剩余的牌
    private $pai_now;
    // 临时存 用户排数number
    private $pai_all_nums;
    // 临时存 用户手里牌 按照 万 条 筒 执行
    private $pai_hand_small;
    // 本次实例 房间号
    private $show_room_id;

    // 测试模式
    private $debug = true;

    // 转换
    private $houDuanPai2qianDuanPai = [
        '1'=>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5,'6'=>6,'7'=>7,'8'=>8,'9'=>9,
        '10'=>1,'11'=>2,'12'=>3,'13'=>4,'14'=>5,'15'=>6,'16'=>7,'17'=>8,'18'=>9,
        '19'=>1,'20'=>2,'21'=>3,'22'=>4,'23'=>5,'24'=>6,'25'=>7,'26'=>8,'27'=>9,
        '28'=>1,'29'=>2,'30'=>3,'31'=>4,'32'=>5,'33'=>6,'34'=>7,'35'=>8,'36'=>9,

        '37'=>10,'38'=>11,'39'=>12,'40'=>13,'41'=>14,'42'=>15,'43'=>16,'44'=>17,'45'=>18,
        '46'=>10,'47'=>11,'48'=>12,'49'=>13,'50'=>14,'51'=>15,'52'=>16,'53'=>17,'54'=>18,
        '55'=>10,'56'=>11,'57'=>12,'58'=>13,'59'=>14,'60'=>15,'61'=>16,'62'=>17,'63'=>18,
        '64'=>10,'65'=>11,'66'=>12,'67'=>13,'68'=>14,'69'=>15,'70'=>16,'71'=>17,'72'=>18,

        '73'=>19,'74'=>20,'75'=>21,'76'=>22,'77'=>23,'78'=>24,'79'=>25,'80'=>26,'81'=>27,
        '82'=>19,'83'=>20,'84'=>21,'85'=>22,'86'=>23,'87'=>24,'88'=>25,'89'=>26,'90'=>27,
        '91'=>19,'92'=>20,'93'=>21,'94'=>22,'95'=>23,'96'=>24,'97'=>25,'98'=>26,'99'=>27,
        '100'=>19,'101'=>20,'102'=>21,'103'=>22,'104'=>23,'105'=>24,'106'=>25,'107'=>26,'108'=>27
    ];
                

    // 构造函数 任何动作 都要 获取牌型一下
    function __construct($cmd,$data) {
        $this->cmd = $cmd;  // 请求命令
        $this->data = $data;    // 传递数据
        $this->redis =  Cache::store('redis');  // 启动缓存
        $this->show_room_id = $this->data->room_id; // 配置房间号

        // 根据 room_id 获取当前房间信息
        $map = [];
        $map['show_room_id'] = $this->data->room_id;        
        $this->room = (new QiPaiSCMJRoom())->where($map)->find();   // 读取已经存在的房间

        // 如果房间不存在
        if(is_null($this->room)){
            $this->createRoom();    // 创建房间
            $this->room = (new QiPaiSCMJRoom())->where($map)->find();// 重新获取数据 

            // 模拟添加用户 
            $this->addUser(1);       // 添加玩家 此处是 模拟用户
            $this->addUser(2);       // 添加玩家 此处是 模拟用户 
            $this->addUser(3);       // 添加玩家 此处是 模拟用户 
            $this->addUser(4);       // 添加玩家 此处是 模拟用户     

            $this->setUserPostion();            // 用户入座完成 设置庄闲 模拟 位置 
            $this->pai_now = $this->xiPai();    // 洗牌
            $this->faPai();                     // 发牌
        }else{
            $this->show('房间已存在');
        }

        // 展示排数
        $this->liPai(); // 整牌 理牌
        // 设置基本信息 
        $this->setBaseInfo();
    }

    public function show($msg){
        if($this->debug){
            dump($msg);
        }
    }

    // 设置基础信息 | 设置 redis 缓存 增加速度
    public function setBaseInfo(){
        // 打印调试信息
        $this->show('更新用户基本信息 数据读取 优先 redis缓存内存储的 ');
        // 处理基本信息 
        $room = $this->room;
        
        // 剩余多少牌
        $this->label_paishu_string=$room->pai_nums;
        // 当前局数
        $this->label_jushu_string=$room->ju_now.'/'.$room->ju_all;

        // 设置第1个用户的 牌 及用户基本信息
        $user_1 = (new User())->find($room->user_1_id);
        $this->setDongXiNanBeiUser(true,$user_1,$room->user_1_position,$room->user_1_role,$room->user_1_action,$room->user_1_pai_hand,$room->user_1_pai_hand_zhi,$room->user_1_pai_hand_chu,$room->user_1_pai_shunzi,$room->user_1_pai_kezi,$room->user_1_pai_gangzi);
        // 设置第2个用户的 牌 及用户基本信息
        $user_2 = (new User())->find($room->user_2_id);
        $this->setDongXiNanBeiUser(true,$user_2,$room->user_2_position,$room->user_2_role,$room->user_2_action,$room->user_2_pai_hand,$room->user_2_pai_hand_zhi,$room->user_2_pai_hand_chu,$room->user_2_pai_shunzi,$room->user_2_pai_kezi,$room->user_2_pai_gangzi);
        // 设置第3个用户的 牌 及用户基本信息
        $user_3 = (new User())->find($room->user_3_id);
        $this->setDongXiNanBeiUser(true,$user_3,$room->user_3_position,$room->user_3_role,$room->user_3_action,$room->user_3_pai_hand,$room->user_3_pai_hand_zhi,$room->user_3_pai_hand_chu,$room->user_3_pai_shunzi,$room->user_3_pai_kezi,$room->user_3_pai_gangzi);
        // 设置第4个用户的 牌 及用户基本信息
        $user_4 = (new User())->find($room->user_4_id);
        $this->setDongXiNanBeiUser(true,$user_4,$room->user_4_position,$room->user_4_role,$room->user_4_action,$room->user_4_pai_hand,$room->user_4_pai_hand_zhi,$room->user_4_pai_hand_chu,$room->user_4_pai_shunzi,$room->user_4_pai_kezi,$room->user_4_pai_gangzi);

        if($this->data->user_id == $room->user_1_id){
            // 如果等于第1个 
            $this->user_position = $room->user_1_position;
        }elseif($this->data->user_id == $room->user_2_id){
            // 如果等于第2个
            $this->user_position = $room->user_2_position;            
        }elseif($this->data->user_id == $room->user_3_id){
            // 如果等于第3个 
            $this->user_position = $room->user_3_position;            
        }elseif($this->data->user_id == $room->user_4_id){
            // 如果等于第4个 
            $this->user_position = $room->user_4_position;            
        }; 
    }

    // 配置 用户
    public function setDongXiNanBeiUser($updateRedis,$user,$user_position,$user_role,$user_action,$user_pai_hand,$user_pai_hand_zhi,$user_pai_hand_chu,$user_pai_shunzi,$user_pai_kezi,$user_pai_gangzi){
        
        $cache_time = 60; // 缓存1分钟 

        if($user_position == 'dong'){
            if($this->redis->get('user_dong') && !$updateRedis){
                $this->user_dong = $this->redis->get('user_dong');
                return true;
            }
            $this->show('强制更新 东 用户缓存');
            // 东用户
            $this->user_dong=new gameUser($user->user_name,'dong');
            $this->user_dong->user_pai_hand = unserialize($user_pai_hand) ;
            $this->user_dong->user_pai_hand_zhi =unserialize($user_pai_hand_zhi) ;
            $this->user_dong->user_pai_hand_chu =unserialize($user_pai_hand_chu) ;
            $this->user_dong->user_pai_shunzi =unserialize($user_pai_shunzi) ;
            $this->user_dong->user_pai_kezi =unserialize($user_pai_kezi) ;
            $this->user_dong->user_pai_gangzi =unserialize($user_pai_gangzi) ;

            /** 用户头像 */
            $this->user_dong->user_head_img_spriteframe = $user->avatar;
            /** 用户性别 */
            $this->user_dong->user_sex = $user->sex;
            /** 用户ID */
            $this->user_dong->user_id = $user->id;
            /** 用户积分 */
            $this->user_dong->user_score = $user->points;
            /** 用户游戏角色 zhuang xian */
            $this->user_dong->user_role = $user_role;
            /** 用户可以执行的动作 */
            $this->user_dong->user_action = $user_action;
            // 存入缓存
            $this->redis->set('user_dong',$this->user_dong,$cache_time);
        }

        if($user_position == 'xi'){
            if($this->redis->get('user_xi') && !$updateRedis){
                $this->user_xi = $this->redis->get('user_xi');
                return true;
            }
            $this->show('强制更新 西 用户缓存');
            // 西用户
            $this->user_xi=new gameUser($user->user_name,'xi');
            $this->user_xi->user_pai_hand =unserialize($user_pai_hand) ;
            $this->user_xi->user_pai_hand_zhi =unserialize($user_pai_hand_zhi) ;
            $this->user_xi->user_pai_hand_chu =unserialize($user_pai_hand_chu) ;
            $this->user_xi->user_pai_shunzi =unserialize($user_pai_shunzi) ;
            $this->user_xi->user_pai_kezi =unserialize($user_pai_kezi) ;
            $this->user_xi->user_pai_gangzi =unserialize($user_pai_gangzi) ;

            /** 用户头像 */
            $this->user_xi->user_head_img_spriteframe = $user->avatar;
            /** 用户性别 */
            $this->user_xi->user_sex = $user->sex;
            /** 用户ID */
            $this->user_xi->user_id = $user->id;
            /** 用户积分 */
            $this->user_xi->user_score = $user->points;
            /** 用户游戏角色 zhuang xian */
            $this->user_xi->user_role = $user_role;
            /** 用户可以执行的动作 */
            $this->user_xi->user_action = $user_action;
            // 存入缓存
            $this->redis->set('user_xi',$this->user_xi,$cache_time);
        }

        if($user_position == 'nan'){
            if($this->redis->get('user_nan') && !$updateRedis){
                $this->user_nan = $this->redis->get('user_nan');
                return true;
            }
            $this->show('强制更新 南 用户缓存');
            // 南用户
            $this->user_nan=new gameUser($user->user_name,'nan');
            $this->user_nan->user_pai_hand =unserialize($user_pai_hand) ;
            $this->user_nan->user_pai_hand_zhi =unserialize($user_pai_hand_zhi) ;
            $this->user_nan->user_pai_hand_chu =unserialize($user_pai_hand_chu) ;
            $this->user_nan->user_pai_shunzi =unserialize($user_pai_shunzi) ;
            $this->user_nan->user_pai_kezi =unserialize($user_pai_kezi) ;
            $this->user_nan->user_pai_gangzi =unserialize($user_pai_gangzi) ;

            /** 用户头像 */
            $this->user_nan->user_head_img_spriteframe = $user->avatar;
            /** 用户性别 */
            $this->user_nan->user_sex = $user->sex;
            /** 用户ID */
            $this->user_nan->user_id = $user->id;
            /** 用户积分 */
            $this->user_nan->user_score = $user->points;
            /** 用户游戏角色 zhuang xian */
            $this->user_nan->user_role = $user_role;
            /** 用户可以执行的动作 */
            $this->user_nan->user_action = $user_action;
            // 存入缓存
            $this->redis->set('user_nan',$this->user_nan,$cache_time);
        }

        if($user_position == 'bei'){
            if($this->redis->get('user_bei') && !$updateRedis){
                $this->user_bei = $this->redis->get('user_bei');
                return true;
            }
            $this->show('强制更新 北 用户缓存');
            // 北用户
            $this->user_bei=new gameUser($user->user_name,'bei');
            $this->user_bei->user_pai_hand =unserialize($user_pai_hand) ;
            $this->user_bei->user_pai_hand_zhi =unserialize($user_pai_hand_zhi) ;
            $this->user_bei->user_pai_hand_chu =unserialize($user_pai_hand_chu) ;
            $this->user_bei->user_pai_shunzi =unserialize($user_pai_shunzi) ;
            $this->user_bei->user_pai_kezi =unserialize($user_pai_kezi) ;
            $this->user_bei->user_pai_gangzi =unserialize($user_pai_gangzi) ;

            /** 用户头像 */
            $this->user_bei->user_head_img_spriteframe = $user->avatar;
            /** 用户性别 */
            $this->user_bei->user_sex = $user->sex;
            /** 用户ID */
            $this->user_bei->user_id = $user->id;
            /** 用户积分 */
            $this->user_bei->user_score = $user->points;
            /** 用户游戏角色 zhuang xian */
            $this->user_bei->user_role = $user_role;
            /** 用户可以执行的动作 */
            $this->user_bei->user_action = $user_action;
            // 存入缓存
            $this->redis->set('user_bei',$this->user_bei,$cache_time);
        }
    }

    // 类似与 路由数据 回调处理就行了 这里只是 需要处理 业务数据就行了
    public function dataBack(){        
        // 转发 或者 默认
        if(in_array($this->cmd,$this->good_cmds)){
            // 通过字符 调用 配套函数
            return $this->{$this->cmd}();
        }else{
            // 默认
            return json_encode(['message'=>'wait, i am thinking....']);
        }        
    }

    // 初始化房间 信息 
    public function createRoom(){
        // 打印调试信息
        $this->show('创建房间');
        // 初始化
        $updateData = [];
        // $updateData['show_room_id'] = rand(100000,999999); // 用户输入房间号 进行创建
        $updateData['show_room_id'] = $this->show_room_id; // 用户输入房间号 进行创建
        $updateData['status'] = '1';
        $updateData['game_run_status'] = 'play'; // 默认 start , 此刻调整一下 临时进入到 play 里面
        $updateData['room_config'] = 'room_config';       
        $updateData['pai_nums'] = 108;
        $updateData['ju_now'] = 1;
        $updateData['ju_all'] = 99;

        // 根据 room_id 获取当前房间信息
        $insert = (new QiPaiSCMJRoom())->insert($updateData);
    }

    // 创建用户
    public function addUser($user_id){
        // 打印调试信息
        $this->show('添加用户'.$user_id);
        // 可以自动判断 添加到 第几个
        $userInfo = (new User())->find($user_id);
        $user_id_key = 'user_1_id';
        
        $map = [];
        $map['show_room_id'] = $this->show_room_id;        
        $this->room = (new QiPaiSCMJRoom())->where($map)->find();   // 读取已经存在的房间
        
        
        if(is_null($this->room->user_4_id)){
            $user_id_key = 'user_4_id';
        }
        if(is_null($this->room->user_3_id)){
            $user_id_key = 'user_3_id';
        }
        if(is_null($this->room->user_2_id)){
            $user_id_key = 'user_2_id';
        }
        if(is_null($this->room->user_1_id)){
            $user_id_key = 'user_1_id';
        }

        // 更新数据
        $upData = [];
        $upData[$user_id_key] = $user_id;
        (new QiPaiSCMJRoom())->where($map)->update($upData);
    }

    // 设置庄闲 东西南北 
    public function setUserPostion(){
        // 打印调试信息
        $this->show('模拟用户的 分配庄闲 东西南北');
        // 随机初始化 状态 pai_status （kong） position (dong) ready_status (1) action （run） role (zhuang) que (wan)
        $action = [
            ['kong','dong',1,'run','zhuang','wan'],
            ['kong','xi',1,'wait','xian','wan'],
            ['kong','nan',1,'wait','xian','wan'],
            ['kong','bei',1,'wait','xian','wan']
        ];
        shuffle($action);
        $upData = [];
        for($i = 0; $i < 4; $i++){
            $user_key = 'user_'.($i+1);
            $temp = $action[$i];
            // 遍历赋值 
            $upData[$user_key.'_pai_status'] = $temp[0];
            $upData[$user_key.'_position'] = $temp[1];
            $upData[$user_key.'_ready_status'] = $temp[2];
            $upData[$user_key.'_action'] = $temp[3];
            $upData[$user_key.'_role'] = $temp[4];
            $upData[$user_key.'_pai_que'] = $temp[5];
        }

        // 更新内容
        $map = [];
        $map['show_room_id'] = $this->show_room_id;  
        (new QiPaiSCMJRoom())->where($map)->update($upData);
    }

    // 洗牌
    public function xiPai(){
        // 打印调试信息
        $this->show('洗牌');
        $data = [];
        // 合计 108 张 万1-36 条37-72 饼73-108 对9进行求余 才行
        // 遍历赋值 
        for($i=1;$i<=108;$i++){
            $data[] = $i;
        }
        shuffle($data);
        return $data;
    }

    // 发牌
    public function faPai(){
        // 打印调试信息
        $this->show('发牌');
        // 摸牌 
        $user_1_pai_hand = array_slice($this->pai_now,1,14);
        $user_2_pai_hand = array_slice($this->pai_now,14,13);
        $user_3_pai_hand = array_slice($this->pai_now,27,13);
        $user_4_pai_hand = array_slice($this->pai_now,40,13);

        asort($user_1_pai_hand);
        asort($user_2_pai_hand);
        asort($user_3_pai_hand);
        asort($user_4_pai_hand);

        $updateData = [];
        // 用户1 牌型设置
        $updateData['user_1_pai_hand'] = serialize($user_1_pai_hand);    
        $updateData['user_1_pai_hand_chu'] = serialize([]);
        $updateData['user_1_pai_hand_chu_save'] = serialize([]);
        $updateData['user_1_pai_shunzi'] = serialize([]);
        $updateData['user_1_pai_kezi'] = serialize([]);
        $updateData['user_1_pai_gangzi'] = serialize([]);
        $updateData['user_1_pai_hand_zhi'] = serialize([]);
        // 用户2 牌型设置
        $updateData['user_2_pai_hand'] = serialize($user_2_pai_hand);
        $updateData['user_2_pai_hand_chu'] = serialize([]);
        $updateData['user_2_pai_hand_chu_save'] = serialize([]);
        $updateData['user_2_pai_shunzi'] = serialize([]);
        $updateData['user_2_pai_kezi'] = serialize([]);
        $updateData['user_2_pai_gangzi'] = serialize([]);
        $updateData['user_2_pai_hand_zhi'] = serialize([]);
        // 用户3 牌型设置
        $updateData['user_3_pai_hand'] = serialize($user_3_pai_hand);
        $updateData['user_3_pai_hand_chu'] = serialize([]);
        $updateData['user_3_pai_hand_chu_save'] = serialize([]);
        $updateData['user_3_pai_shunzi'] = serialize([]);
        $updateData['user_3_pai_kezi'] = serialize([]);
        $updateData['user_3_pai_gangzi'] = serialize([]);
        $updateData['user_3_pai_hand_zhi'] = serialize([]);
        // 用户4 牌型设置
        $updateData['user_4_pai_hand'] = serialize($user_4_pai_hand);
        $updateData['user_4_pai_hand_chu'] = serialize([]);
        $updateData['user_4_pai_hand_chu_save'] = serialize([]);
        $updateData['user_4_pai_shunzi'] = serialize([]);
        $updateData['user_4_pai_kezi'] = serialize([]);
        $updateData['user_4_pai_gangzi'] = serialize([]);
        $updateData['user_4_pai_hand_zhi'] = serialize([]);

        // 更新一下 pai_now
        $updateData['pai_now'] = $this->pai_now;
        // 根据 room_id 获取当前房间信息
        $map = [];
        $map['show_room_id'] = $this->show_room_id;
        $insert = (new QiPaiSCMJRoom())->where($map)->update($updateData);
    }

    // 整形 整理牌
    public function liPai(){
        // 打印调试信息
        $this->show('整理牌');
        $map = [];
        $map['show_room_id'] = $this->show_room_id;        
        $this->room = (new QiPaiSCMJRoom())->where($map)->find();   // 读取已经存在的房间

        // 模拟重新洗牌
        $updateData = [];
        // 用户1 牌型设置
        $this->get_pai_base(unserialize($this->room['user_1_pai_hand'])); 
        $updateData['user_1_pai_shunzi']    = serialize($this->get_pai_shunzi());
        $updateData['user_1_pai_kezi']      = serialize($this->get_pai_kezi());
        $updateData['user_1_pai_gangzi']    = serialize($this->get_pai_gangzi());
        $updateData['user_1_pai_hand_zhi']  = serialize($this->get_pai_zhi());
        // 用户2 牌型设置
        $this->get_pai_base(unserialize($this->room['user_2_pai_hand'])); 
        $updateData['user_2_pai_shunzi']    = serialize($this->get_pai_shunzi());
        $updateData['user_2_pai_kezi']      = serialize($this->get_pai_kezi());
        $updateData['user_2_pai_gangzi']    = serialize($this->get_pai_gangzi());
        $updateData['user_2_pai_hand_zhi']  = serialize($this->get_pai_zhi());
        // 用户3 牌型设置
        $this->get_pai_base(unserialize($this->room['user_3_pai_hand'])); 
        $updateData['user_3_pai_shunzi']    = serialize($this->get_pai_shunzi());
        $updateData['user_3_pai_kezi']      = serialize($this->get_pai_kezi());
        $updateData['user_3_pai_gangzi']    = serialize($this->get_pai_gangzi());
        $updateData['user_3_pai_hand_zhi']  = serialize($this->get_pai_zhi());
        // 用户4 牌型设置
        $this->get_pai_base(unserialize($this->room['user_4_pai_hand'])); 
        $updateData['user_4_pai_shunzi']    = serialize($this->get_pai_shunzi());
        $updateData['user_4_pai_kezi']      = serialize($this->get_pai_kezi());
        $updateData['user_4_pai_gangzi']    = serialize($this->get_pai_gangzi());
        $updateData['user_4_pai_hand_zhi']  = serialize($this->get_pai_zhi());
        // 更新数据 
        $map = [];
        $map['show_room_id'] = $this->show_room_id;
        $insert = (new QiPaiSCMJRoom())->where($map)->update($updateData);
    }
    // 修改牌型到展示牌型
    public function changePaiZi2PaiShow($pai_hand){
        // 打印调试信息
        $this->show('把1-108牌 转换为 1-27 展示性牌型');
        $pai = [];

        foreach($pai_hand as $k => $v){
            // 手中变形 展示的牌型
            $pai[$k] = $this->houDuanPai2qianDuanPai[$v];
        }
        // 升序排序
        asort($pai);

        return $pai;
    }

    // 基础处理牌 
    public function get_pai_base($pai_hand){
        // 打印调试信息
        $this->show('把1-108牌 转换为 1-27 展示性牌型 并且统计相同类型牌的数量 ');
        // 合计 108 张 万1-36 条37-72 饼73-108 对10 [-36 / - 72]进行求余 才行
        // 合计 1-9 万 10-18 条 19-27 饼子
        $this->pai_all_nums = [
            0,0,0,0,0,0,0,0,0,0,
            0,0,0,0,0,0,0,0,0,
            0,0,0,0,0,0,0,0,0
            ];
        // 获取唯一值
        $this->pai_hand_small = $pai_hand; // 求余 成 1-9 万 10-18 条 19-28 饼
        // 遍历所有唯一值 
        foreach($pai_hand as $k => $v){
            // 手中变形 展示的牌型
            $this->pai_hand_small[$k] = $this->houDuanPai2qianDuanPai[$v];
            // 当前 牌数 ++  刻子 杠子
            $temp = $this->houDuanPai2qianDuanPai[$v];
            $this->pai_all_nums[$temp] = $this->pai_all_nums[$temp]+1;
        }
        // 升序排序
        asort($this->pai_hand_small);
    }

    // 梳理手中持有的牌型 直排 顺子 刻子 杠子 挑选 剩下的 就是 直排的了
    public function get_pai_zhi(){
        // 打印调试信息
        $this->show('获取手中展示的直排 剩下的就是');
        return $this->pai_hand_small;
    }
    // 梳理手中持有的牌型  顺子 优先级 3  相当于 顺子 / 刻子 / 杠子 都能打出才行
    public function get_pai_shunzi(){
        // 打印调试信息
        $this->show('获取手中顺子牌');
        $pai = [];
        foreach($this->pai_hand_small as $k => $v){
            if(isset($this->pai_hand_small[$k+2])){
                if(isset($this->pai_hand_small[$k+1])){
                    if($this->pai_hand_small[$k]+1 == $this->pai_hand_small[$k+1] 
                    && $this->pai_hand_small[$k]+2 == $this->pai_hand_small[$k+2] 
                    && $this->pai_hand_small[$k] != 8
                    && $this->pai_hand_small[$k] != 9
                    && $this->pai_hand_small[$k] != 17
                    && $this->pai_hand_small[$k] != 18
                    && $this->pai_hand_small[$k] != 26
                    && $this->pai_hand_small[$k] != 27
                    ){
                        // 连续 并且 不跨 类型
                        $temp = [$this->pai_hand_small[$k],$this->pai_hand_small[$k]+1,$this->pai_hand_small[$k]+2];
                        $pai[] = $temp;
                        unset($this->pai_hand_small[$k]);
                        unset($this->pai_hand_small[$k+1]);
                        unset($this->pai_hand_small[$k+2]);
                    }
                }
            }
        }
        return $pai;
    }
    // 梳理手中持有的牌型 刻子 优先级 2  相当于 顺子 / 刻子 / 杠子 都能打出才行
    public function get_pai_kezi(){
        // 打印调试信息
        $this->show('获取手中刻子牌');
        $pai = [];
        foreach($this->pai_all_nums as $k_nums => $v_nums){
            if($v_nums == 3){
                $pai[] = $k_nums;
                foreach($this->pai_hand_small as $k => $v){
                    if($k_nums == $v){
                        unset($this->pai_hand_small[$k]);
                    }                    
                }
            }
        }
        return $pai;
    }
    // 梳理手中持有的牌型 杠子  优先级 1   相当于 顺子 / 刻子 / 杠子 都能打出才行
    public function get_pai_gangzi(){
        // 打印调试信息
        $this->show('获取手中 杠子牌');
        $pai = [];
        foreach($this->pai_all_nums as $k_nums => $v_nums){
            if($v_nums == 4){
                $pai[] = $k_nums;
                foreach($this->pai_hand_small as $k => $v){
                    if($k_nums == $v){
                        unset($this->pai_hand_small[$k]);
                    }                    
                }
            }
        }
        return $pai;
    }

    // 默认测试
    public function good_cmd(){
        // 打印调试信息
        $this->show('默认测试命令');
        return json_encode(['message'=>__FUNCTION__]);
    }

    // 获取基本数据 头像 局数 积分 这些
    public function getRunData(){
        // 打印调试信息
        $this->show('获取游戏运行状态');
        return json_encode(['message'=>__FUNCTION__,'game_status'=>$this->room->game_run_status]);
    }

    // 获取基本数据 头像 局数 积分 这些
    // 初始化 用户信息 
    public function getBaseData(){
        // 打印调试信息
        $this->show('【前端调用】获取基础数据 |=> 因为无法穿越多层级的json 所以拼接的字符串');

        $data = [
            'message'=>__FUNCTION__,
            // 剩余多少牌
            'label_paishu_string'=>$this->label_paishu_string,
            // 当前局数
            'label_jushu_string'=>$this->label_jushu_string,
            // 当前用户位置
            'user_position'=>$this->user_position,
            // 东用户
            'user_dong'=>$this->user_dong,
            'user_dong_pai_hand_zhi'=> implode('#',$this->user_dong->user_pai_hand_zhi),
            'user_dong_pai_hand_chu'=> implode('#',$this->user_dong->user_pai_hand_chu), 
            'user_dong_pai_shunzi'=> implode('#',$this->user_dong->user_pai_shunzi),
            'user_dong_pai_kezi'=> implode('#',$this->user_dong->user_pai_kezi),
            'user_dong_pai_gangzi'=> implode('#',$this->user_dong->user_pai_gangzi),             
            // 西用户
            'user_xi'=>$this->user_xi,
            'user_xi_pai_hand_zhi'=> implode('#',$this->user_xi->user_pai_hand_zhi),
            'user_xi_pai_hand_chu'=> implode('#',$this->user_xi->user_pai_hand_chu), 
            'user_xi_pai_shunzi'=> implode('#',$this->user_xi->user_pai_shunzi),
            'user_xi_pai_kezi'=> implode('#',$this->user_xi->user_pai_kezi),
            'user_xi_pai_gangzi'=> implode('#',$this->user_xi->user_pai_gangzi), 
            // 南用户
            'user_nan'=>$this->user_nan,
            'user_nan_pai_hand_zhi'=> implode('#',$this->user_nan->user_pai_hand_zhi),
            'user_nan_pai_hand_chu'=> implode('#',$this->user_nan->user_pai_hand_chu), 
            'user_nan_pai_shunzi'=> implode('#',$this->user_nan->user_pai_shunzi),
            'user_nan_pai_kezi'=> implode('#',$this->user_nan->user_pai_kezi),
            'user_nan_pai_gangzi'=> implode('#',$this->user_nan->user_pai_gangzi), 
            // 北用户
            'user_bei'=>$this->user_bei,
            'user_bei_pai_hand_zhi'=> implode('#',$this->user_bei->user_pai_hand_zhi),
            'user_bei_pai_hand_chu'=> implode('#',$this->user_bei->user_pai_hand_chu), 
            'user_bei_pai_shunzi'=> implode('#',$this->user_bei->user_pai_shunzi),
            'user_bei_pai_kezi'=> implode('#',$this->user_bei->user_pai_kezi),
            'user_bei_pai_gangzi'=> implode('#',$this->user_bei->user_pai_gangzi)
        ];
        return json_encode($data);
    }

    // 获取基本数据 头像 局数 积分 这些
    public function getSingleRunData(){
        // 打印调试信息
        $this->show('为了测试 单独获取一个用户的数据');

        // 需要先缓存下来 
        $data = [
            'message'=>__FUNCTION__,
            // 东用户
            'user_dong'=>$this->user_dong,
            'user_dong_pai_hand_zhi'=> implode('#',$this->user_dong->user_pai_hand_zhi),
            'user_dong_pai_hand_chu'=> implode('#',$this->user_dong->user_pai_hand_chu), 
            'user_dong_pai_shunzi'=> implode('#',$this->user_dong->user_pai_shunzi),
            'user_dong_pai_kezi'=> implode('#',$this->user_dong->user_pai_kezi),
            'user_dong_pai_gangzi'=> implode('#',$this->user_dong->user_pai_gangzi)
        ];
        return json_encode($data);
    }

    // 获取基本数据 头像 局数 积分 这些
    public function updateUserUIShow(){
        // 打印调试信息
        $this->show('为了测试 更新数据测试');

        $data = [
            'message'=>__FUNCTION__,
            // 剩余多少牌
            'label_paishu_string'=>$this->label_paishu_string,
            // 当前局数
            'label_jushu_string'=>$this->label_jushu_string,
            // 当前用户位置
            'user_position'=>$this->user_position,
            // 东用户
            'user_dong'=>$this->user_dong,
            // 西用户
            'user_xi'=>$this->user_xi,
            // 南用户
            'user_nan'=>$this->user_nan,
            // 北用户
            'user_bei'=>$this->user_bei
        ];
        return json_encode($data);
    }

    // 出牌
    public function chuPai(){
        // 打印调试信息
        $this->show('出牌测试');

        $qianDuanPai2houDuanPai = [
            '1'=>[1,10,19,28],
            '2'=>[2,11,20,29],
            '3'=>[3,12,21,30],
            '4'=>[4,13,22,31],
            '5'=>[5,14,23,32],
            '6'=>[6,15,24,33],
            '7'=>[7,16,25,34],
            '8'=>[8,17,26,35],
            '9'=>[9,18,27,36],

            '10'=>[37,46,55,64],
            '11'=>[38,47,56,65],
            '12'=>[39,48,57,66],
            '13'=>[40,49,58,67],
            '14'=>[41,50,59,68],
            '15'=>[42,51,60,69],
            '16'=>[43,52,61,70],
            '17'=>[44,53,62,71],
            '18'=>[45,54,63,72],

            '19'=>[73,82,91,100],
            '20'=>[74,83,92,101],
            '21'=>[75,84,93,102],
            '22'=>[76,85,94,103],
            '23'=>[77,86,95,104],
            '24'=>[78,87,96,105],
            '25'=>[79,88,97,106],
            '26'=>[80,89,98,107],
            '27'=>[81,90,99,108]
        ];
        // 获取数据 操作
        $map = [];
        $map['show_room_id'] = $this->show_room_id;        
        $this->room = (new QiPaiSCMJRoom())->where($map)->find();   // 读取已经存在的房间
        
        if(!is_null($this->room->user_4_id) && $this->room->user_4_id == $this->data->user_id){
            $this->show('第 4 个用户 出牌 执行的动作');
            // 需要一个 前端数字 对应后端数字的换算 分别对应 +0 +9 +18 +27 的方式
            $find = 0; // 找到的概率为0 
            $pai_push = 0; // 找到对应的那个牌
            // 减少
            $pai_hand = unserialize($this->room->user_4_pai_hand);
            $pai_hand_chu = unserialize($this->room->user_4_pai_hand_chu);
            $pai_hand_chu_save = unserialize($this->room->user_4_pai_hand_chu_save);
            foreach($pai_hand as $k => $v){
                foreach($qianDuanPai2houDuanPai[$this->data->pai_zi] as $k_search => $v_search){
                    if($find == 0 && $v_search == $v){
                        $find = 1; // 明确找到了
                        $pai_push = $v; // 找到对应的位置
                        unset($pai_hand[$k]);
                    }
                }
            }
            // 增加
            if($pai_push != 0){
                array_push($pai_hand_chu,$pai_push);
                array_push($pai_hand_chu_save,$pai_push);

                // 翻译存储数据
                $pai_hand_chu = $this->changePaiZi2PaiShow($pai_hand_chu);
            }
            // 更新数据
            $upData = [];
            $upData['user_4_pai_hand'] = serialize($pai_hand);
            $upData['user_4_pai_hand_chu'] = serialize($pai_hand_chu);
            $upData['user_4_pai_hand_chu_save'] = serialize($pai_hand_chu_save);
            (new QiPaiSCMJRoom())->where($map)->update($upData);

            // 更新后，重新整理牌
            $this->room = (new QiPaiSCMJRoom())->where($map)->find();   // 读取已经存在的房间
            $this->liPai();
            
            // 设置第1个用户的 牌 及用户基本信息
            $room = $this->room;
            $user_4 = (new User())->find($room->user_4_id);
            $this->setDongXiNanBeiUser(true,$user_4,$room->user_4_position,$room->user_4_role,$room->user_4_action,$room->user_4_pai_hand,$room->user_4_pai_hand_zhi,$room->user_4_pai_hand_chu,$room->user_4_pai_shunzi,$room->user_4_pai_kezi,$room->user_4_pai_gangzi);
            
        }
        if(!is_null($this->room->user_3_id) && $this->room->user_3_id == $this->data->user_id){
            $this->show('第 3 个用户 出牌 执行的动作');
            // 需要一个 前端数字 对应后端数字的换算 分别对应 +0 +9 +18 +27 的方式
            $find = 0; // 找到的概率为0 
            $pai_push = 0; // 找到对应的那个牌
            // 减少
            $pai_hand = unserialize($this->room->user_3_pai_hand);
            $pai_hand_chu = unserialize($this->room->user_3_pai_hand_chu);
            $pai_hand_chu_save = unserialize($this->room->user_3_pai_hand_chu_save);
            foreach($pai_hand as $k => $v){
                foreach($qianDuanPai2houDuanPai[$this->data->pai_zi] as $k_search => $v_search){
                    if($find == 0 && $v_search == $v){
                        $find = 1; // 明确找到了
                        $pai_push = $v; // 找到对应的位置
                        unset($pai_hand[$k]);
                    }
                }
            }
            // 增加
            if($pai_push != 0){
                array_push($pai_hand_chu,$pai_push);
                array_push($pai_hand_chu_save,$pai_push);

                // 翻译存储数据
                $pai_hand_chu = $this->changePaiZi2PaiShow($pai_hand_chu);
            }
            // 更新数据
            $upData = [];
            $upData['user_3_pai_hand'] = serialize($pai_hand);
            $upData['user_3_pai_hand_chu'] = serialize($pai_hand_chu);
            $upData['user_3_pai_hand_chu_save'] = serialize($pai_hand_chu_save);
            (new QiPaiSCMJRoom())->where($map)->update($upData);

            // 更新后，重新整理牌
            $this->room = (new QiPaiSCMJRoom())->where($map)->find();   // 读取已经存在的房间
            $this->liPai();
            
            // 设置第1个用户的 牌 及用户基本信息
            $room = $this->room;
            $user_3 = (new User())->find($room->user_3_id);
            $this->setDongXiNanBeiUser(true,$user_3,$room->user_3_position,$room->user_3_role,$room->user_3_action,$room->user_3_pai_hand,$room->user_3_pai_hand_zhi,$room->user_3_pai_hand_chu,$room->user_3_pai_shunzi,$room->user_3_pai_kezi,$room->user_3_pai_gangzi);
            
        }
        if(!is_null($this->room->user_2_id) && $this->room->user_2_id == $this->data->user_id){
            $this->show('第 2 个用户 出牌 执行的动作');
            // 需要一个 前端数字 对应后端数字的换算 分别对应 +0 +9 +18 +27 的方式
            $find = 0; // 找到的概率为0 
            $pai_push = 0; // 找到对应的那个牌
            // 减少
            $pai_hand = unserialize($this->room->user_2_pai_hand);
            $pai_hand_chu = unserialize($this->room->user_2_pai_hand_chu);
            $pai_hand_chu_save = unserialize($this->room->user_2_pai_hand_chu_save);
            foreach($pai_hand as $k => $v){
                foreach($qianDuanPai2houDuanPai[$this->data->pai_zi] as $k_search => $v_search){
                    if($find == 0 && $v_search == $v){
                        $find = 1; // 明确找到了
                        $pai_push = $v; // 找到对应的位置
                        unset($pai_hand[$k]);
                    }
                }
            }
            // 增加
            if($pai_push != 0){
                array_push($pai_hand_chu,$pai_push);
                array_push($pai_hand_chu_save,$pai_push);

                // 翻译存储数据
                $pai_hand_chu = $this->changePaiZi2PaiShow($pai_hand_chu);
            }
            // 更新数据
            $upData = [];
            $upData['user_2_pai_hand'] = serialize($pai_hand);
            $upData['user_2_pai_hand_chu'] = serialize($pai_hand_chu);
            $upData['user_2_pai_hand_chu_save'] = serialize($pai_hand_chu_save);
            (new QiPaiSCMJRoom())->where($map)->update($upData);

            // 更新后，重新整理牌
            $this->room = (new QiPaiSCMJRoom())->where($map)->find();   // 读取已经存在的房间
            $this->liPai();
            
            // 设置第1个用户的 牌 及用户基本信息
            $room = $this->room;
            $user_2 = (new User())->find($room->user_2_id);
            $this->setDongXiNanBeiUser(true,$user_2,$room->user_2_position,$room->user_2_role,$room->user_2_action,$room->user_2_pai_hand,$room->user_2_pai_hand_zhi,$room->user_2_pai_hand_chu,$room->user_2_pai_shunzi,$room->user_2_pai_kezi,$room->user_2_pai_gangzi);
            
        }
        if(!is_null($this->room->user_1_id) && $this->room->user_1_id == $this->data->user_id){
            $this->show('第 1 个用户 出牌 执行的动作');
            // 需要一个 前端数字 对应后端数字的换算 分别对应 +0 +9 +18 +27 的方式
            $find = 0; // 找到的概率为0 
            $pai_push = 0; // 找到对应的那个牌
            // 减少
            $pai_hand = unserialize($this->room->user_1_pai_hand);
            $pai_hand_chu = unserialize($this->room->user_1_pai_hand_chu);
            $pai_hand_chu_save = unserialize($this->room->user_1_pai_hand_chu_save);
            foreach($pai_hand as $k => $v){
                foreach($qianDuanPai2houDuanPai[$this->data->pai_zi] as $k_search => $v_search){
                    if($find == 0 && $v_search == $v){
                        $find = 1; // 明确找到了
                        $pai_push = $v; // 找到对应的位置
                        unset($pai_hand[$k]);
                    }
                }
            }
            // 增加
            if($pai_push != 0){
                array_push($pai_hand_chu,$pai_push);
                array_push($pai_hand_chu_save,$pai_push);

                // 翻译存储数据
                $pai_hand_chu = $this->changePaiZi2PaiShow($pai_hand_chu);
            }
            // 更新数据
            $upData = [];
            $upData['user_1_pai_hand'] = serialize($pai_hand);
            $upData['user_1_pai_hand_chu'] = serialize($pai_hand_chu);
            $upData['user_1_pai_hand_chu_save'] = serialize($pai_hand_chu_save);
            (new QiPaiSCMJRoom())->where($map)->update($upData);


            // 更新后，重新整理牌
            $this->room = (new QiPaiSCMJRoom())->where($map)->find();   // 读取已经存在的房间
            $this->liPai();
            
            // 设置第1个用户的 牌 及用户基本信息
            $room = $this->room;
            $user_1 = (new User())->find($room->user_1_id);
            $this->setDongXiNanBeiUser(true,$user_1,$room->user_1_position,$room->user_1_role,$room->user_1_action,$room->user_1_pai_hand,$room->user_1_pai_hand_zhi,$room->user_1_pai_hand_chu,$room->user_1_pai_shunzi,$room->user_1_pai_kezi,$room->user_1_pai_gangzi);
            
        }

        // 返回数据
        return json_encode(['message'=>__FUNCTION__,'pai_zi'=>$this->data->pai_zi,'user_id'=>$this->data->user_id]);
    }

    // 返回当期牌信息
    public function getPaiData(){
        return json_encode(['message'=>__FUNCTION__]);
    }

    // 返回当期牌信息
    public function getMaJiangData(){
        return json_encode(['message'=>__FUNCTION__]);
    }

// 类结束了    
}