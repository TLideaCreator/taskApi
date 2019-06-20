<?php
/**
 * Created by PhpStorm.
 * User: lqh
 * Date: 2018/3/13
 * Time: 下午1:14
 */

namespace App\Methods\IM;


use App\Models\Sys\SysConfig;
use Exception;
use Illuminate\Support\Facades\File;

class TIMFactory
{
    private $privatePath = "";
    private $publicPath = "";
    private $private_key = false;
    private $public_key = false;
    private $appId = -1;
    private $acctType = -1;

    private static $_instance = null;

    /**
     * TIMFactory constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return TIMFactory|null
     * @throws Exception
     */
    public static function getInstance(){
        if(is_null(self::$_instance)){
            self::$_instance = new TIMFactory();
            self::$_instance->checkSetting();
        }
        return self::$_instance;
    }

    /**
     * @throws Exception
     */
    private function checkSetting(){
        $keyId = SysConfig::where('type',SysConfig::IM_SDK_ID)->first();
        $acctType = SysConfig::where('type',SysConfig::IM_SKD_ACCT_TYPE)->first();
        if(empty($keyId) || empty($acctType)){
            throw new Exception("set im first");
        }
        $this->setAppId($keyId->record);
        $this->setAcctType($acctType->record);
        $this->setPrivatePath(storage_path("im/private_key"));
        $this->setPublicPath(storage_path("im/public_key"));
    }


    /**
     * @param string $privatePath
     * @throws Exception
     */
    public function setPrivatePath(string $privatePath)
    {
        $this->private_key = openssl_pkey_get_private(File::get($privatePath));
        if ($this->private_key === false) {
            throw new Exception(openssl_error_string());
        }
        $this->privatePath = $privatePath;
    }

    /**
     * @param $publicPath
     * @throws Exception
     */
    public function setPublicPath($publicPath)
    {

        $this->public_key = openssl_pkey_get_public(File::get($publicPath));
        if ($this->public_key === false) {
            throw new Exception(openssl_error_string());
        }
        $this->publicPath = $publicPath;
    }

    /**
     * 设置Appid
     * @param int $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * @param $acctType
     */
    public function setAcctType($acctType)
    {
        $this->acctType = $acctType;
    }

    /**
     * @return int
     */
    public function getAppId(): int
    {
        return $this->appId;
    }

    /**
     * @return int
     */
    public function getAcctType(): int
    {
        return $this->acctType;
    }

    public function isSetting()
    {
        return $this->appId != -1 && !empty($this->privatePath)&& !empty($this->publicPath);
    }




    /**
     * 用于url的base64encode
     * '+' => '*', '/' => '-', '=' => '_'
     * @param string $string 需要编码的数据
     * @return string 编码后的base64串，失败返回false
     * @throws Exception
     */
    private function base64Encode($string)
    {
        static $replace = Array('+' => '*', '/' => '-', '=' => '_');
        $base64 = base64_encode($string);
        if ($base64 === false) {
            throw new Exception('base64_encode error');
        }
        return str_replace(array_keys($replace), array_values($replace), $base64);
    }

    /**
     * 用于url的base64decode
     * '+' => '*', '/' => '-', '=' => '_'
     * @param string $base64 需要解码的base64串
     * @return string 解码后的数据，失败返回false
     * @throws Exception
     */
    private function base64Decode($base64)
    {
        static $replace = Array('+' => '*', '/' => '-', '=' => '_');
        $string = str_replace(array_values($replace), array_keys($replace), $base64);
        $result = base64_decode($string);
        if ($result == false) {
            throw new Exception('base64_decode error');
        }
        return $result;
    }

    /**
     * 根据json内容生成需要签名的buf串
     * @param array $json 票据json对象
     * @return string 按标准格式生成的用于签名的字符串
     * 失败时返回false
     * @throws Exception
     */
    private function genSignContent(array $json)
    {
        static $members = Array(
            'TLS.appid_at_3rd',
            'TLS.account_type',
            'TLS.identifier',
            'TLS.sdk_appid',
            'TLS.time',
            'TLS.expire_after'
        );
        $content = '';
        foreach ($members as $member) {
            if (!isset($json[$member])) {
                throw new Exception('json need ' . $member);
            }
            $content .= "{$member}:{$json[$member]}\n";
        }
        return $content;
    }

    /**
     * ECDSA-SHA256签名
     * @param string $data 需要签名的数据
     * @return string 返回签名 失败时返回false
     * @throws Exception
     */
    private function sign($data)
    {
        $signature = '';
        if (!openssl_sign($data, $signature, $this->private_key, 'sha256')) {
            throw new Exception(openssl_error_string());
        }
        return $signature;
    }

    /**
     * 验证ECDSA-SHA256签名
     * @param string $data 需要验证的数据原文
     * @param string $sig 需要验证的签名
     * @return int 1验证成功 0验证失败
     * @throws Exception
     */
    private function verify($data, $sig)
    {
        $ret = openssl_verify($data, $sig, $this->public_key, 'sha256');
        if ($ret == -1) {
            throw new Exception(openssl_error_string());
        }
        return $ret;
    }

    /**
     * 生成usersig
     * @param string $identifier 用户名
     * @param float|int $expire 有效期 默认为180天
     * @return string 生成的UserSig 失败时为false
     * @throws Exception
     */
    public function genSig($identifier, $expire = 180 * 24 * 3600)
    {
        $json = Array(
            'TLS.account_type' => '0',
            'TLS.identifier' => (string)$identifier,
            'TLS.appid_at_3rd' => '0',
            'TLS.sdk_appid' => (string)$this->appId,
            'TLS.expire_after' => (string)$expire,
            'TLS.version' => '201512300000',
            'TLS.time' => (string)time()
        );
        $err = '';
        $content = $this->genSignContent($json, $err);
        $signature = $this->sign($content, $err);
        $json['TLS.sig'] = base64_encode($signature);
        if ($json['TLS.sig'] === false) {
            throw new Exception('base64_encode error');
        }
        $json_text = json_encode($json);
        if ($json_text === false) {
            throw new Exception('json_encode error');
        }
        $compressed = gzcompress($json_text);
        if ($compressed === false) {
            throw new Exception('gzcompress error');
        }
        return $this->base64Encode($compressed);
    }

    /**
     * 验证usersig
     * @param string $sig usersig
     * @param string $identifier 需要验证用户名
     * @param string $init_time usersig中的生成时间
     * @param int $expire_time usersig中的有效期 如：3600秒
     * @param string $error_msg 失败时的错误信息
     * @return boolean 验证是否成功
     */
    public function verifySig($sig, $identifier, &$init_time, &$expire_time, &$error_msg)
    {
        try {
            $error_msg = '';
            $decoded_sig = $this->base64Decode($sig);
            $uncompressed_sig = gzuncompress($decoded_sig);
            if ($uncompressed_sig === false) {
                throw new Exception('gzuncompress error');
            }
            $json = json_decode($uncompressed_sig);
            if ($json == false) {
                throw new Exception('json_decode error');
            }
            $json = (array)$json;
            if ($json['TLS.identifier'] !== $identifier) {
                throw new Exception("identifier error sigid:{$json['TLS.identifier']} id:{$identifier}");
            }
            if ($json['TLS.sdk_appid'] != $this->appId) {
                throw new Exception("appid error sigappid:{$json['TLS.appid']} thisappid:{$this->appId}");
            }
            $content = $this->genSignContent($json);
            $signature = base64_decode($json['TLS.sig']);
            if ($signature == false) {
                throw new Exception('sig json_decode error');
            }
            $succ = $this->verify($content, $signature);
            if (!$succ) {
                throw new Exception('verify failed');
            }
            $init_time = $json['TLS.time'];
            $expire_time = $json['TLS.expire_after'];
            return true;
        } catch (Exception $ex) {
            $error_msg = $ex->getMessage();
            return false;
        }
    }

    public static function destroyInstance(){
        self::$_instance = null;
    }
}