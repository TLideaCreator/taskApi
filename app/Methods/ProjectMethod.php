<?php


namespace App\Methods;


use Illuminate\Support\Facades\DB;

class ProjectMethod
{
    /**
     * @param $userId
     * @param $projectId
     * @return mixed
     */
    public static function authUserForProject($userId, $projectId)
    {
        $check = DB::table('project_users as pu')
            ->leftJoin('project_task_roles as ptr', 'pu.role_id', '=', 'ptr.id')
            ->where('pu.user_id', $userId)
            ->where('pu.project_id', $projectId)
            ->value(
                'ptr.project_mgr'
            );
        return $check == 1;
    }

    /**
     * @param $userId
     * @param $projectId
     * @return mixed
     */
    public static function authUserForSprint($userId, $projectId)
    {
        $check = DB::table('project_users as pu')
            ->leftJoin('project_task_roles as ptr', 'pu.role_id', '=', 'ptr.id')
            ->where('pu.user_id', $userId)
            ->where('pu.project_id', $projectId)
            ->where(function($query){
                $query->where('ptr.project_mgr',1)
                    ->orWhere('ptr.sprint_mgr',1);
            })
            ->count();
        return $check > 0;
    }
}
