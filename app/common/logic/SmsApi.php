<?php
namespace app\common\logic;
// /**
// SMS短信接口类
// @author sms.cn
// @link http://www.sms.cn
//  */
class SmsApi {
    /**
     * SMSAPI请求地址
     */
    const API_URL = 'http://api.sms.cn/sms/';
  //  const API_URL = 'http://api.sms.cn/sms/?ac=sendint';

    /**
     * 接口账号
     *
     * @var string
     */
    protected $uid;

    /**
     * 接口密码
     *
     * @var string
     * @link http://sms.sms.cn/ 请到此处（短信设置->接口密码）获取
     */
    protected $pwd;

    /**
     * sms api请求地址
     * @var string
     */
    protected $apiURL;
    /**
     * 短信发送请求参数
     * @var string
     */
    protected $smsParams;

    /**
     * 接口返回信息
     * @var string
     */
    protected $resultMsg;

    /**
     * 接口返回信息格式
     * @var string
     */
    protected $format;

    /**
     * 构造方法
     *
     * @param string $uid 接口账号
     * @param string $pwd 接口密码
     */
    public function __construct($uid = '', $pwd = '')
    {
//用户和密码可直接写在类里
        $def_uid = '';
        $def_pwd = '';
        $this->uid = $uid ?: $def_uid;
        $this->pwd = $pwd ?: $def_pwd;
        $this->apiURL = self::API_URL;
        $this->format = 'json';
    }
    /**
     * SMS公共参数
     * @return array
     */
    protected function publicParams()
    {
        return array(
            'uid' => $this->uid,
            'pwd' => md5($this->pwd.$this->uid),
            'format' => $this->format,
        );
    }
    /**
     * 发送变量模板短信
     *
     * @param string $mobile 手机号码
     * @param string $content 短信内容参数
     * @param string $template 短信模板ID
     * @return array
     */
    public function send($mobile, $contentParam,$template='') {
//短信发送参数
        $this->smsParams = [
            'ac' => 'sendint',
            'mobile' => $mobile,
            'content' => json_encode($contentParam),
            'template' => $template
        ];
        $this->resultMsg = $this->request();
        return $this->json_to_array($this->resultMsg, true);
    }

    /**
     * 发送全文模板短信
     *
     * @param string $mobile 手机号码
     * @param string $content 短信内容
     * @return array
     */
    public function sendAll($mobile, $content) {
//短信发送参数
        $this->smsParams = [
            'ac' => 'send',
            'mobile' => $mobile,
            'content' => $content,
        ];
        $this->resultMsg = $this->request();

        return $this->json_to_array($this->resultMsg, true);
    }

    /**
     * 取剩余短信条数
     *
     * @return array
     */
    public function getNumber() {
//参数
        $this->smsParams = [
            'ac' => 'number',
        ];
        $this->resultMsg = $this->request();
        return $this->json_to_array($this->resultMsg, true);
    }
    /**
     * 获取发送状态
     *
     * @return array
     */
    public function getStatus() {
//参数
        $this->smsParams = [
            'ac' => 'status',
        ];
        $this->resultMsg = $this->request();
        return $this->json_to_array($this->resultMsg, true);
    }
    /**
     * 接收上行短信（回复）
     *
     * @return array
     */
    public function getReply() {
//参数
        $this->smsParams = [
            'ac' => 'reply',
        ];
        $this->resultMsg = $this->request();
        return $this->json_to_array($this->resultMsg, true);
    }
    /**
     * 取已发送总条数
     *
     * @return array
     */
    public function getSendTotal() {
//参数
        $this->smsParams = [
            'ac' => 'number',
            'cmd' => 'send',
        ];
        $this->resultMsg = $this->request();
        return $this->json_to_array($this->resultMsg, true);
    }

    /**
     * 取发送记录
     *
     * @return array
     */
    public function getQuery() {
//参数
        $this->smsParams = [
            'ac' => 'query',
        ];
        $this->resultMsg = $this->request();
        return $this->json_to_array($this->resultMsg, true);
    }

    /**
     * 发送HTTP请求
     * @return string
     */
    private function request()
    {
        $params = array_merge($this->publicParams(),$this->smsParams);
        if( function_exists('curl_init') )
        {
            return $this->curl_request($this->apiURL,$params);
        }
        else
        {
            return $this->file_get_request($this->apiURL,$params);
        }
    }
    /**
     * 通过CURL发送HTTP请求
     * @param string $url //请求URL
     * @param array $postFields //请求参数
     * @return string
     */
    private function curl_request($url,$postFields){
        $postFields = http_build_query($postFields);
//echo $url.'?'.$postFields;
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postFields );
        $result = curl_exec ( $ch );
        curl_close ( $ch );
        return $result;
    }
    /**
     * 通过file_get_contents发送HTTP请求
     * @param string $url //请求URL
     * @param array $postFields //请求参数
     * @return string
     */
    private function file_get_request($url,$postFields)
    {
        $post='';
        while (list($k,$v) = each($postFields))
        {
            $post .= rawurlencode($k)."=".rawurlencode($v)."&"; //转URL标准码
        }
        return file_get_contents($url.'?'.$post);
    }
    /**
     * 获取当前HTTP请返回信息
     * @return string
     */
    public function getResult()
    {
        $this->resultMsg;
    }
    /**
     * 获取随机位数数字
     * @param integer $len 长度
     * @return string
     */
    public function randNumber($len = 6)
    {
        $chars = str_repeat('0123456789', 10);
        $chars = str_shuffle($chars);
        $str = substr($chars, 0, $len);
        return $str;
    }
//url转码
    function json_urlencode($p)
    {
        if( is_array($p) )
        {
            foreach( $p as $key => $value )$p[$key] = $this->json_urlencode($value);
        }
        else
        {
            $p = urlencode($p);
        }
        return $p;
    }

//把json字符串转数组
    function json_to_array($p)
    {
        if( mb_detect_encoding($p,array('ASCII','UTF-8','GB2312','GBK')) != 'UTF-8' )
        {
            $p = iconv('GBK','UTF-8',$p);
        }
        return json_decode($p, true);
    }
}