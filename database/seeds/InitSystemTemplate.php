<?php


class InitSystemTemplate extends BaseSeeder
{
    protected function task()
    {
        $template = [
            'name' => '敏捷开发',
            'desc' => '敏捷开发',
            'img'=> 'https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=1302664204,3230515576&fm=26&gp=0.jpg'
        ];

        $priorities = [
            [
                'name' => '最低',
                'color' => '#D58AA0',
                'is_default' => 0,
                'indexes'=> 1
            ],
            [
                'name' => '低',
                'color' => '#43D6BC',
                'is_default' => 0,
                'indexes'=> 2
            ],
            [
                'name' => '普通',
                'color' => '#4AE8B8',
                'is_default' => 1,
                'indexes'=> 3
            ],
            [
                'name' => '高',
                'color' => '#FD785E',
                'is_default' => 0,
                'indexes'=> 4
            ],
            [
                'name' => '最高',
                'color' => '#EF4A37',
                'is_default' => 0,
                'indexes'=> 5
            ]
        ];
        $status = [
            [
                'name'=>'待开发',
                'indexes'=> 1,
            ],
            [
                'name'=>'开发中',
                'indexes'=> 2,
            ],
            [
                'name'=>'开发完成',
                'indexes'=> 3,
            ],
            [
                'name'=>'测试完成',
                'indexes'=> 4,
            ]
        ];
        $types = [
            [
                'name'=> '任务',
                'icon'=> 'bookmark'
            ]  ,
            [
                'name'=> 'BUG',
                'icon'=> 'bug_report'
            ]
        ];

        $temp = \App\Models\SystemTaskTemp::create($template);
        foreach ($priorities as $priority) {
            $priority['temp_id'] = $temp->id;
            \App\Models\SystemTaskPriority::create($priority);
        }
        foreach ($status as $state) {
            $state['temp_id'] = $temp->id;
            \App\Models\SystemTaskStatus::create($state);
        }
        foreach ($types as $type) {
            $type['temp_id'] = $temp->id;
            \App\Models\SystemTaskType::create($type);
        }
    }
}
