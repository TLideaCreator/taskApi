<?php


namespace App\Http\Controllers\Projects;


use App\Http\Controllers\ApiCtrl;
use App\Models\Project;
use App\Models\ProjectTaskStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ProjectStatusCtrl extends ApiCtrl
{

    /**
     * SystemTempCtrl constructor.
     */
    public function __construct()
    {

    }

    public function getProjectStatus($projectId)
    {
        $status = ProjectTaskStatus::where('project_id', $projectId)->get();
        return ['data' => $status];
    }

    public function createProjectStatus($projectId)
    {
        $name = Input::get('name', null);
        $color = Input::get('color', null);
        if (empty($name) || empty($color)) {
            $this->notFound404('params');
        }
        $tempCount = Project::where('id', $projectId)->count();
        if ($tempCount < 1) {
            $this->notFound404('status');
        }
        $tempMax = ProjectTaskStatus::where('project_id', $projectId)->max('indexes');
        if (empty($tempMax)) {
            $tempMax = 0;
        }
        ProjectTaskStatus::create([
            'project_id' => $projectId,
            'name' => $name,
            'color' => $color,
            'indexes' => $tempMax
        ]);
        $status = ProjectTaskStatus::where('project_id', $projectId)->get();
        return ['data' => $status];
    }

    public function updateProjectStatusSequence($projectId)
    {
        $fromIndex = Input::get('from', null);
        $toIndex = Input::get('to', null);
        if (!is_numeric($fromIndex)) {
            $this->notFound404('from');
        }
        if (!is_numeric($toIndex)) {
            $this->notFound404('to');
        }
        if ($fromIndex != $toIndex) {
            $status = ProjectTaskStatus::where('project_id', $projectId)
                ->where('indexes', $fromIndex)
                ->first();
            if (empty($status)) {
                $this->notFound404('status');
            }
            DB::transaction(function () use ($fromIndex, $toIndex, $projectId, $status) {
                if ($fromIndex > $toIndex) {
                    DB::update('
                        update project_task_status set indexes = indexes + 1 
                        where indexes >= ? and indexes < ? and id != ? and project_id = ?
                    ', [$toIndex, $fromIndex, $status->id, $projectId]);
                } else {
                    DB::update('
                        update project_task_status set indexes = indexes - 1 
                        where indexes > ? and indexes <= ? and id != ? and project_id = ?
                    ', [$fromIndex, $toIndex, $status->id, $projectId]);
                }
                DB::update('
                        update project_task_status set indexes = ? where id = ? and project_id = ?
                    ', [$toIndex, $status->id, $projectId]);
            });
        }
        $status = ProjectTaskStatus::where('project_id', $projectId)->get();
        return ['data' => $status];
    }

    public function updateProjectStatus($projectId, $statusId)
    {
        $status = ProjectTaskStatus::where('id', $statusId)
            ->where('project_id', $projectId)
            ->first();
        if (empty($status)) {
            $this->notFound404('status');
        }
        $name = Input::get('name', null);
        $color = Input::get('color', null);
        if (!is_null($name)) {
            if (empty($name)) {
                $this->notFound404('name');
            } else {
                $status->name = $name;
            }
        }
        if (!is_null($color)) {
            if (empty($color)) {
                $this->notFound404('color');
            } else {
                $status->color = $color;
            }
        }
        if ($status->save()) {
            $status = ProjectTaskStatus::where('project_id', $projectId)->get();
            return ['data' => $status];
        } else {
            $this->onDBError($status, 'update system template status error');
        }
    }

    public function deleteProjectStatus($projectId,$statusId)
    {
        $status = ProjectTaskStatus::where('id', $statusId)
            ->where('project_id', $projectId)
            ->first();
        if(empty($status)){
            $this->notFound404('status');
        }
        $count = ProjectTaskStatus::where('project_id', $projectId)
            ->count();
        if($count == 1){
            $this->onDateError('must limit 1');
        }
        if($status->delete()){
            $defaultStatus = ProjectTaskStatus::where('project_id')->orderBy('indexes')->first();
            if(!empty($defaultStatus)){
                DB::update('update tasks set status = ? where project_id =? and status = ?',
                    [$defaultStatus->id, $projectId, $statusId]);
            }


            $status = ProjectTaskStatus::where('project_id', $projectId)->get();
            return ['data' => $status];
        }else{
            $this->onDBError($status,'delete project status error');
        }
    }

}
