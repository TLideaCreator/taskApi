<?php


namespace App\Http\Controllers\SystemTemp;


use App\Http\Controllers\ApiCtrl;
use App\Models\SystemTaskRole;
use App\Models\SystemTaskTemp;
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
        return ['data'=>$roleList];
    }

    public function createSystemTemplateRole($tempId)
    {
        $tempCount = SystemTaskTemp::where('id',$tempId)->count();
        if($tempCount < 1){
            $this->notFound404('temp');
        }
        $name = Input::get('name', null);
        $logo = Input::get('logo', null);
        $projectMgr = Input::get('project_mgr', null);
        $sprintMgr = Input::get('sprint_mgr', null);
        $taskMgr = Input::get('task_mgr', null);

        if(empty($name)){
            $this->notFound404('name');
        }
        if(empty($logo)){
            $this->notFound404('logo');
        }
        if($projectMgr != SystemTaskRole::ENABLE && $projectMgr != SystemTaskRole::DISABLE ){
            $this->notFound404('$project_mgr');
        }
        if($sprintMgr != SystemTaskRole::ENABLE && $sprintMgr != SystemTaskRole::DISABLE ){
            $this->notFound404('$sprint_mgr');
        }
        if($taskMgr != SystemTaskRole::ENABLE && $taskMgr != SystemTaskRole::DISABLE ){
            $this->notFound404('$task_mgr');
        }

        SystemTaskRole::create([
            'temp_id'=>$tempId,
            'name'=> $name,
            'logo'=>$logo,
            'project_mgr'=>$projectMgr,
            'sprint_mgr'=>$sprintMgr,
            'task_mgr'=>$taskMgr,
        ]);

        $roleList = SystemTaskRole::where('temp_id', $tempId)
            ->get();
        return ['data'=>$roleList];
    }

    public function deleteSystemTemplateRole($tempId, $roleId)
    {

    }
}
