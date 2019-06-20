<?php


namespace App\Format;


use App\Methods\TokenCenter;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class LoginFormat extends TransformerAbstract
{

    public function transform(User $user)
    {
        $item = [
            'id' => $user->id,
            'name' => $user->nickname,
            'avatar' => $user->avatar,
            'phone' => $user->phone,
            'admin'=> $user->is_admin == 1 ? 'Yes':'No'
        ];
        if(!empty($user->token)){
            $item['token'] = TokenCenter::getInstance()->generateToken($user->token);
        }
        return $item;
    }

}