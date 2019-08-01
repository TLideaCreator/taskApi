<?php



class InitSystemTemplateRoles extends BaseSeeder
{
    protected function task(){
        $roles = [
            [
                'name' => '管理员',
                'logo' => 'md-contact',
                'project_mgr'=>1,
                'sprint_mgr'=>1,
                'task_mgr'=>1,
            ],
            [
                'name' => '开发者',
                'logo' => 'md-git-branch',
                'project_mgr'=>0,
                'sprint_mgr'=>0,
                'task_mgr'=>1,
            ],
            [
                'name' => '测试',
                'logo' => 'md-thunderstorm',
                'project_mgr'=>0,
                'sprint_mgr'=>0,
                'task_mgr'=>1,
            ],
            [
                'name' => '观察者',
                'logo' => 'md-glasses',
                'project_mgr'=>0,
                'sprint_mgr'=>0,
                'task_mgr'=>0,
            ]
        ];
        $temps = \App\Models\SystemTaskTemp::all();
        foreach ($temps as $temp) {
            foreach ($roles as $role) {
                $role['temp_id'] = $temp->id;
                \App\Models\SystemTaskRole::create($role);
            }
        }

        $projects = \App\Models\Project::all();
        foreach ($projects as $project) {
            foreach ($roles as $role){
                $role['project_id'] = $project -> id;
                \App\Models\ProjectTaskRole::create($role);
            }
        }
    }
}