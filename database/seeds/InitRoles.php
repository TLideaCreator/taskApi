<?php


class InitRoles extends BaseSeeder
{
    protected function task()
    {
        $roles = [
            [
                'name' => '管理员',
                'icon' => 'md-contact',
                'type' => 1,
                'create' => 1,
                'read' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'name' => '开发者',
                'icon' => 'md-git-branch',
                'create' => 1,
                'read' => 1,
                'update' => 1,
                'delete' => 0
            ],
            [
                'name' => '测试',
                'icon' => 'md-thunderstorm',
                'create' => 1,
                'read' => 1,
                'update' => 1,
                'delete' => 0
            ],
            [
                'name' => '观察者',
                'icon' => 'md-glasses',
                'create' => 0,
                'read' => 1,
                'update' => 0,
                'delete' => 0
            ]
        ];
        foreach ($roles as $role){
            \App\Models\Role::create(
                $role
            );
        }

    }
}