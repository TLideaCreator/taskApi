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
                'is_default' => 0
            ],
            [
                'name' => '低',
                'color' => '#43D6BC',
                'is_default' => 0
            ],
            [
                'name' => '普通',
                'color' => '#4AE8B8',
                'is_default' => 1
            ],
            [
                'name' => '高',
                'color' => '#FD785E',
                'is_default' => 0
            ],
            [
                'name' => '最高',
                'color' => '#EF4A37',
                'is_default' => 0
            ]
        ];
        $status = [
            [
                'name'=>'待开发',
                'indexes'=> 1,
                'type' => \App\Models\SystemTaskStatus::TYPE_START
            ],
            [
                'name'=>'开发中',
                'indexes'=> 2,
                'type' => \App\Models\SystemTaskStatus::TYPE_PROGRESS
            ],
            [
                'name'=>'开发完成',
                'indexes'=> 3,
                'type' => \App\Models\SystemTaskStatus::TYPE_PROGRESS
            ],
            [
                'name'=>'测试完成',
                'indexes'=> 4,
                'type' => \App\Models\SystemTaskStatus::TYPE_FINISH
            ]
        ];
        $types = [
            [
                'name'=> '任务',
                'icon'=> 'md-bookmark'
            ]  ,
            [
                'name'=> 'BUG',
                'icon'=> 'md-bug'
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