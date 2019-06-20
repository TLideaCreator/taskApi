<?php

namespace App\Models;

class User extends TM
{

    protected $fillable = [
        'phone', 'email', 'password', 'nickname', 'avatar', 'is_admin', 'token'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'created_at', 'updated_at'
    ];
}
