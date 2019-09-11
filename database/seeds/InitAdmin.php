<?php


class InitAdmin extends BaseSeeder
{
    protected function task()
    {
        \App\Models\User::create([
            'phone'=> '13333333333',
            'email'=> '13333333333@133.com',
            'password'=> \Illuminate\Support\Facades\Crypt::encrypt('123456'),
            'nickname'=> '管理员',
            'avatar'=> '1',
            'is_admin'=>1,
            'token'=>''
        ]);
    }

}
