<?php


namespace App\Http\Controllers\Projects;


use App\Http\Controllers\ApiCtrl;
use App\Models\Project;
use App\Models\ProjectTaskRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ProjectRoleCtrl extends ApiCtrl
{

    /**
     * SystemTempCtrl constructor.
     */
    public function __construct()
    {

    }

    public function getProjectRole($projectId)
    {
        $roleList = ProjectTaskRole::where('project_id',$projectId)
            ->get();
        return ['data' => $roleList];
    }

    public function createProjectRole($projectId)
    {
        $projectCount = Project::where('id', $projectId)->count();
        if ($projectCount < 1) {
            $this->notFound404('project');
        }
        $name = Input::get('name', null);
        $logo = Input::get('logo', null);
        $projectMgr = Input::get('project_mgr', null);
        $sprintMgr = Input::get('sprint_mgr', null);
        $taskMgr = Input::get('task_mgr', null);

        if (empty($name)) {
            $this->notFound404('name');
        }
        if (empty($logo)) {
            $this->notFound404('logo');
        }
        if ($projectMgr != ProjectTaskRole::ENABLE && $projectMgr != ProjectTaskRole::DISABLE) {
            $this->notFound404('$project_mgr');
        }
        if ($sprintMgr != ProjectTaskRole::ENABLE && $sprintMgr != ProjectTaskRole::DISABLE) {
            $this->notFound404('$sprint_mgr');
        }
        if ($taskMgr != ProjectTaskRole::ENABLE && $taskMgr != ProjectTaskRole::DISABLE) {
            $this->notFound404('$task_mgr');
        }

        ProjectTaskRole::create([
            'project_id' => $projectId,
            'name' => $name,
            'logo' => $logo,
            'project_mgr' => $projectMgr,
            'sprint_mgr' => $sprintMgr,
            'task_mgr' => $taskMgr,
        ]);

        $roleList = ProjectTaskRole::where('project_id', $projectId)
            ->get();
        return ['data' => $roleList];
    }

    public function updateProjectRole($projectId,$roleId)
    {
        $role = ProjectTaskRole::where('project_id', $projectId)
            ->where('id', $roleId)
            ->first();
        if (empty($role)) {
            $this->notFound404('role');
        }
        $name = Input::get('name', null);
        $logo = Input::get('logo', null);
        $projectMgr = Input::get('project_mgr', null);
        $sprintMgr = Input::get('sprint_mgr', null);
        $taskMgr = Input::get('task_mgr', null);

        if (!is_null($name)) {
            if (empty($name)) {
                $this->notFound404('name');
            }
            $role->name = $name;
        }
        if (!is_null($logo)) {
            if (empty($logo)) {
                $this->notFound404('logo');
            }
            $role->logo = $logo;
        }
        if (!is_null($projectMgr)) {
            if (!is_numeric($projectMgr) ||
                ($projectMgr != ProjectTaskRole::ENABLE &&
                    $projectMgr != ProjectTaskRole::DISABLE)) {
                $this->notFound404('projectMgr');
            }
            $role->project_mgr = $projectMgr;
        }
        if (!is_null($sprintMgr)) {
            if (!is_numeric($sprintMgr) ||
                ($sprintMgr != ProjectTaskRole::ENABLE &&
                    $sprintMgr != ProjectTaskRole::DISABLE)) {
                $this->notFound404('sprintMgr');
            }
            $role->sprint_mgr = $sprintMgr;
        }
        if (!is_null($taskMgr)) {
            if (!is_numeric($taskMgr) ||
                ($taskMgr != ProjectTaskRole::ENABLE &&
                    $taskMgr != ProjectTaskRole::DISABLE)) {
                $this->notFound404('taskMgr');
            }
            $role->task_mgr = $taskMgr;
        }

        if($role->save()){
            $roleList = ProjectTaskRole::where('project_id', $projectId)->get();
            return ['data' => $roleList];
        }else{
            $this->onDBError($role, 'project role update error');
        }
    }

    public function deleteProjectRole($projectId, $roleId)
    {
        $role = ProjectTaskRole::where('project_id', $projectId)
            ->where('id', $roleId)
            ->first();
        if (empty($role)) {
            $this->notFound404('role');
        }
        if ($role->project_mgr == ProjectTaskRole::ENABLE) {
            $projectMgrCount = ProjectTaskRole::where('project_id', $projectId)->count();
            if ($projectMgrCount <= 1) {
                $this->onDateError('project mgr limit');
            }
        }

        if ($role->delete()) {
            $roleList = ProjectTaskRole::where('project_id', $projectId)
                ->get();
            DB::delete('delete from project_users where project_id = ? and role_id = ?',
                [$projectId, $roleId]);
            return ['data' => $roleList];
        } else {
            $this->onDBError($role, 'project role delete error');
        }
    }
}
