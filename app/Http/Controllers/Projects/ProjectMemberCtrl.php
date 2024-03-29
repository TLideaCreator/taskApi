<?php


namespace App\Http\Controllers\Projects;


use App\Http\Controllers\ApiCtrl;
use App\Models\ProjectTaskRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Methods\ProjectMethod;

class ProjectMemberCtrl extends ApiCtrl
{

    public function getProjectMemberList(Request $request, $projectId)
    {
        return $this->projectMemberList($projectId);
    }

    public function addMemberToProject(Request $request, $projectId, $memberId, $roleId)
    {
        $user = $request->user;
        $userCount = User::where('id', $memberId)->count();
        if ($userCount == 0){
            $this->notFound404('user');
        }
        $roleCount = ProjectTaskRole::where('id', $roleId)
            ->where('project_id', $projectId)
            ->count();
        if($roleCount == 0){
            $this->notFound404('role');
        }

        $pu = DB::table('project_users as pu ')
            ->where([
                'project_id'=>$projectId,
                'user_id'=>$memberId
            ])->count();
        if($pu == 0){
            DB::table('project_users')
                ->insert([
                    'project_id'=>$projectId,
                    'user_id'=>$memberId,
                    'role_id'=> $roleId,
                    'last_time'=>time()
                ]);
        }else{
            DB::table('project_users as pu ')
                ->where([
                    'project_id'=>$projectId,
                    'user_id'=>$memberId
                ])->update([
                    'role_id'=>$roleId
                ]);
        }

        return $this->projectMemberList($projectId);
    }

    public function delProjectMembers(Request $request, $projectId, $memberId)
    {
        $user = $request->user;
        DB::table('project_users')
            ->where('user_id', $memberId)
            ->where('project_id', $projectId)
            ->delete();
        return $this->projectMemberList($projectId);
    }

    private function projectMemberList($projectId)
    {
        $user = DB::table('users')
            ->leftJoin(DB::raw("(select * from project_users where project_id = '{$projectId}') as pu"), 'users.id', '=', 'pu.user_id')
            ->leftJoin('project_task_roles', 'project_task_roles.id', '=', 'pu.role_id')
            ->select(
                'users.id as user_id',
                'users.nickname as name',
                'users.avatar',
                'users.phone',
                'users.email',
                'project_task_roles.id as role_id'
            )
            ->orderByRaw("ifnull(project_task_roles.id, 'z')",'asc')
            ->get();
        $roles = ProjectTaskRole::where('project_id',$projectId)->get();

        return ['data'=>$user,'meta'=>['roles'=>$roles]];
    }
}
