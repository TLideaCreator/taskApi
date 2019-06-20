<?php


namespace App\Format;


use App\Models\Project;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class ProjectFormat extends TransformerAbstract
{
    protected $availableIncludes = ['mgr','sprints'];
    public function transform(Project $prj)
    {
        $item = [
            'id' => $prj->id,
            'name' => $prj->name,
            'icon' => $prj->icon,
            'desc' => $prj->desc
        ];

        return $item;
    }

    public function includeMgr(Project $prj){
        $user = Project::leftjoin('project_users as pu', 'pu.project_id', '=', 'projects.id')
            ->leftjoin('roles', 'roles.id', '=' ,'pu.role_id')
            ->leftjoin('users', 'users.id', '=' ,'pu.user_id')
            ->where('pu.project_id', $prj->id)
            ->select('users.id')->first();
        $mgr = User::where('id', $user->id)->first();
        return $this->item($mgr, function(User $user){
            return [
                'id'=> $user->id,
                'name'=> $user->nickname,
                'avatar'=> $user->avatar,
            ];
        });
    }

//    public function includeSprint(Project $prj)
//    {
//
//    }
}