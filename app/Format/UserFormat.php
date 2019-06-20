<?php


namespace App\Format;


use App\Methods\TokenCenter;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserFormat extends TransformerAbstract
{

    public function transform(User $user)
    {
        $item = [
            'id' => $user->id,
            'name' => $user->nickname,
            'avatar' => $user->avatar,
            'phone' => $user->phone,
            'email' => $user->email,
            'admin'=> $user->is_admin == 1 ? 'Yes':'No'
        ];
        return $item;
    }

}