<?php


namespace App\Http\Controllers\SystemTemp;


use App\Http\Controllers\ApiCtrl;
use App\Models\SystemTaskRole;
use App\Models\SystemTaskTemp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class SystemTempRoleCtrl extends ApiCtrl
{

    /**
     * SystemTempCtrl constructor.
     */
    public function __construct()
    {

    }

    public function getSystemTemplateRole($tempId)
    {
        $roleList = SystemTaskRole::where('temp_id', $tempId)
            ->get();
        return ['data' => $roleList];
    }

    public function createSystemTemplateRole($tempId)
    {
        $tempCount = SystemTaskTemp::where('id', $tempId)->count();
        if ($tempCount < 1) {
            $this->notFound404('temp');
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
        if ($projectMgr != SystemTaskRole::ENABLE && $projectMgr != SystemTaskRole::DISABLE) {
            $this->notFound404('$project_mgr');
        }
        if ($sprintMgr != SystemTaskRole::ENABLE && $sprintMgr != SystemTaskRole::DISABLE) {
            $this->notFound404('$sprint_mgr');
        }
        if ($taskMgr != SystemTaskRole::ENABLE && $taskMgr != SystemTaskRole::DISABLE) {
            $this->notFound404('$task_mgr');
        }

        SystemTaskRole::create([
            'temp_id' => $tempId,
            'name' => $name,
            'logo' => $logo,
            'project_mgr' => $projectMgr,
            'sprint_mgr' => $sprintMgr,
            'task_mgr' => $taskMgr,
        ]);

        $roleList = SystemTaskRole::where('temp_id', $tempId)
            ->get();
        return ['data' => $roleList];
    }

    public function updateSystemTemplateRole($tempId, $roleId)
    {
        $role = SystemTaskRole::where('temp_id', $tempId)
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
                ($projectMgr != SystemTaskRole::ENABLE &&
                    $projectMgr != SystemTaskRole::DISABLE)) {
                $this->notFound404('projectMgr');
            }
            $role->project_mgr = $projectMgr;
        }
        if (!is_null($sprintMgr)) {
            if (!is_numeric($sprintMgr) ||
                ($sprintMgr != SystemTaskRole::ENABLE &&
                    $sprintMgr != SystemTaskRole::DISABLE)) {
                $this->notFound404('sprintMgr');
            }
            $role->sprint_mgr = $sprintMgr;
        }
        if (!is_null($taskMgr)) {
            if (!is_numeric($taskMgr) ||
                ($taskMgr != SystemTaskRole::ENABLE &&
                    $taskMgr != SystemTaskRole::DISABLE)) {
                $this->notFound404('taskMgr');
            }
            $role->task_mgr = $taskMgr;
        }

        if($role->save()){
            $roleList = SystemTaskRole::where('temp_id', $tempId)->get();
            return ['data' => $roleList];
        }else{
            $this->onDBError($role, 'system template role update error');
        }
    }

    public function deleteSystemTemplateRole($tempId, $roleId)
    {
        $role = SystemTaskRole::where('temp_id', $tempId)
            ->where('id', $roleId)
            ->first();
        if (empty($role)) {
            $this->notFound404('role');
        }
        if ($role->project_mgr == SystemTaskRole::ENABLE) {
            $projectMgrCount = SystemTaskRole::where('temp_id', $tempId)->count();
            if ($projectMgrCount <= 1) {
                $this->onDateError('project mgr limit');
            }
        }

        if ($role->delete()) {
            $roleList = SystemTaskRole::where('temp_id', $tempId)
                ->get();
            return ['data' => $roleList];
        } else {
            $this->onDBError($role, 'system template role delete error');
        }
    }
}
