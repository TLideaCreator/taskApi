<?php


class InitAdmin extends BaseSeeder
{
    protected function task()
    {
        \App\Models\User::create([
            'phone'=> '17792961664',
            'email'=> '17792961664@133.com',
            'password'=> \Illuminate\Support\Facades\Crypt::encrypt('123456'),
            'nickname'=> '管理员',
            'avatar'=> '1',
            'is_admin'=>1,
            'token'=>''
        ]);
    }

}