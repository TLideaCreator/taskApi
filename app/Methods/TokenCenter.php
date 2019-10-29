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
use Lcobucci\JWT\Signer\Key;

class TokenCenter
{
    private static $_instance = null;
    private $key = null;
    /**
     * TokenMethod constructor.
     */
    private function __construct()
    {
        $this->key = new Key('tokenAuth');
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
        $token = (new Builder())->issuedBy($_SERVER["SERVER_NAME"])
            ->permittedFor($_SERVER["SERVER_NAME"])
            ->issuedAt($time)
            ->canOnlyBeUsedAfter($time)
            ->expiresAt($time + 7 * 24 * 3600)
            ->identifiedBy(md5($time), true)
            ->withClaim("uId", $userId)
            ->getToken($signer, $this->key);
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
            $check = $token->verify($signer, $this->key);
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
