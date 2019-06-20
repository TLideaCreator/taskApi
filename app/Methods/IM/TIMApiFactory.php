<?php
/**
 * Created by PhpStorm.
 * User: lqh
 * Date: 2018/3/15
 * Time: 上午10:56
 */

namespace App\Methods\IM;


use App\Models\Sys\SysConfig;

class TIMApiFactory
{

    private static $_instance = null;

    private $identifier = '';

    const HOST = "https://console.tim.qq.com";
    const VERSION= "v4";

    const GROUP_GET_INFO = self::HOST."/".self::VERSION."/group_open_http_svc/get_group_info";
    const GROUP_CREATE = self::HOST."/".self::VERSION."/group_open_http_svc/create_group";
    const GROUP_ADD_MEMBER = self::HOST."/".self::VERSION."/group_open_http_svc/add_group_member";
    const GROUP_DESTROY = self::HOST."/".self::VERSION."/group_open_http_svc/destroy_group";
    const GROUP_SEND_SYS_MSG = self::HOST."/".self::VERSION."/group_open_http_svc/send_group_system_notification";
    const GROUP_SEND_GROUP_MSG = self::HOST."/".self::VERSION."/group_open_http_svc/send_group_msg";
    const P2P_SEND_MSG = self::HOST."/".self::VERSION."/openim/sendmsg";
    const P2M_SEND_MSG = self::HOST."/".self::VERSION."/openim/batchsendmsg";


    /**
     * @param $rIds
     * @param $content
     * @param string $msgType
     * @throws \Exception
     * @return mixed
     */
    public function sendP2PMsg($rIds,$content,$msgType="TIMTextElem")
    {

        $api = self::P2P_SEND_MSG;
        if(is_array($rIds)){
            $api = self::P2M_SEND_MSG;
        }
        $param = [
            "SyncOtherMachine" => 2,//消息不同步至发送方
            "To_Account" => $rIds,
            "MsgLifeTime" => 60, //消息保存60秒
            "MsgRandom" => random_int(0, 10000000),
            "MsgTimeStamp" => time(),
            "MsgBody" => [
                [
                    "MsgType" => $msgType,
                    "MsgContent" => [
                        "Text" => $content
                    ]
                ]

            ]
        ];
        return $this->api($api,$param);
    }


    /**
     * TIMApiFactory constructor.
     */
    public function __construct()
    {
    }
    /**
     * @return TIMApiFactory|null
     */
    public static function getInstance()
    {
        if(is_null(self::$_instance)){
            self::$_instance = new TIMApiFactory();
        }
        return self::$_instance;
    }

    public static function destroyInstance(){
        self::$_instance = null;
    }

    /**
     * @throws \Exception
     */
    public function setAdmin()
    {
        if(empty($this->identifier)){
            $admin = SysConfig::where("type",SysConfig::IM_SKD_ADMIN)->first();
            if(empty($admin)){
                throw new \Exception("im setting error");
            }
            $this->identifier = $admin->record;
        }
    }


    /**
     * @param $api
     * @param $reqData
     * @return string
     * @throws \Exception
     */
    public function api($api, $reqData)
    {
        $this->setAdmin();
        $parameter =  "usersig=" . TIMFactory::getInstance()->genSig($this->identifier)
            . "&identifier=" . $this->identifier
            . "&sdkappid=" . TIMFactory::getInstance()->getAppId()
            . "&contenttype=json";
        $url = $api."?".$parameter;
        return $this->http_req("https","post",$url,json_encode($reqData));
    }


    /**
     * 向Rest服务器发送请求
     * @param string $http_type http类型,比如https
     * @param string $method 请求方式，比如POST
     * @param string $url 请求的url
     * @param $data
     * @return string $data 请求的数据
     */
    private function http_req($http_type, $method, $url, $data)
    {
        $ch = curl_init();
        if (strstr($http_type, 'https'))
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        if ($method == 'post')
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else
        {
            $url = $url . '?' . $data;
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT,100000);//超时时间

        try
        {
            $ret=curl_exec($ch);
        }catch(\Exception $e)
        {
            curl_close($ch);
            return json_encode(array('ret'=>0,'msg'=>'failure'));
        }
        curl_close($ch);
        return $ret;
    }
}