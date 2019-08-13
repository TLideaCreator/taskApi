<?php


namespace App\Http\Controllers\Projects;


use App\Http\Controllers\ApiCtrl;
use App\Models\Project;
use App\Models\ProjectTaskType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ProjectTypeCtrl extends ApiCtrl
{

    /**
     * SystemTempCtrl constructor.
     */
    public function __construct()
    {

    }

    public function getProjectTypes($projectId)
    {
        $types = ProjectTaskType::where('project_id', $projectId)->get();
        return ['data'=>$types];
    }

    public function createProjectTypes($projectId)
    {
        $tempCount = Project::where('id', $projectId)->count();
        if($tempCount < 1){
            $this->notFound404('temp');
        }
        $name = Input::get('name', null);
        $icon = Input::get('icon', null);

        if(empty($name)){
            $this->notFound404('name');
        }
        if(empty($icon)){
            $this->notFound404('icon');
        }

        ProjectTaskType::create([
            'project_id'=>$projectId,
            'name'=>$name,
            'icon'=>$icon
        ]);

        $types = ProjectTaskType::where('project_id', $projectId)->get();
        return ['data'=>$types];
    }

    public function updateProjectTypes($projectId,$typeId)
    {
        $type = ProjectTaskType::where('id',$typeId)
            ->where('project_id',$projectId)
            ->first();
        if(empty($type)){
            $this->notFound404('type');
        }
        $name = Input::get('name',null);
        $icon = Input::get('icon',null);
        if(!is_null($name)){
            if(empty($name)){
                $this->notFound404('name');
            }
            $type->name = $name;
        }
        if(!is_null($icon)){
            if(empty($icon)){
                $this->notFound404('icon');
            }
            $type->icon = $icon;
        }
        if($type->save()){
            $types = ProjectTaskType::where('project_id', $projectId)->get();
            return ['data'=>$types];
        }else{
            $this->onDBError($type, 'system template type delete ');
        }
    }

    public function deleteProjectTypes($projectId,$typeId)
    {
        $type = ProjectTaskType::where('id',$typeId)
            ->where('project_id',$projectId)
            ->first();
        if(empty($type)){
            $this->notFound404('type');
        }
        $count = ProjectTaskType::where('project_id', $projectId)->count();
        if($count == 1){
            $this->onDateError('project task type limit 1');
        }
        if($type->delete()){
            $defaultType = ProjectTaskType::where('project_id',$projectId)
                ->first();
            DB::update('update tasks set type = ? where project_id= ? and type=?',
                [$defaultType->id, $projectId, $typeId]);
            $types = ProjectTaskType::where('project_id', $projectId)->get();
            return ['data'=>$types];
        }else {
            $this->onDBError($type, 'delete project type error ');
        }

    }
}
