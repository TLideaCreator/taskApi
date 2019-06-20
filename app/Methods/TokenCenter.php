<?php
/**
 * Created by PhpStorm.
 * UserDash: lqh
 * Date: 2018/5/22
 * Time: 下午2:53
 */

namespace App\Methods;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Parser;

class TokenCenter
{
    private static $_instance = null;

    /**
     * TokenMethod constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return TokenCenter|null
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new TokenCenter();
        }
        return self::$_instance;
    }

    /**
     * @param $userId
     * @param $time
     * @return string
     */
    public function generateToken($userId, $time=0)
    {
        $signer = new Sha256();
        $token = (new Builder())->setIssuer($_SERVER["SERVER_NAME"])
            ->setAudience($_SERVER["SERVER_NAME"])
            ->setIssuedAt($time)
            ->setNotBefore($time)
            ->setExpiration($time + 7 * 24 * 3600)
            ->setId(md5($time), true)
            ->set("uId", $userId)
            ->sign($signer, "key")
            ->getToken();
        return (string)$token;
    }

    /**
     * @param $token
     * @return string
     */
    public function authToken($token)
    {
        try {
            $token = (new Parser())->parse((string)$token); // Parses from a string
            $signer = new Sha256();
            $check = $token->verify($signer, 'key');
            if (!$check) {
                return null;
            }
            $id = $token->getClaim("uId");
            return $id;
        } catch (\Exception $ex) {
            return null;
        }

    }
}