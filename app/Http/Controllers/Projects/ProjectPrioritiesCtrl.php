<?php


namespace App\Http\Controllers\Projects;


use App\Http\Controllers\ApiCtrl;
use App\Models\Project;
use App\Models\ProjectTaskPriority;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ProjectPrioritiesCtrl extends ApiCtrl
{

    /**
     * SystemTempCtrl constructor.
     */
    public function __construct()
    {

    }

    public function getProjectPriority($projectId)
    {
        $priorities = ProjectTaskPriority::where('project_id', $projectId)->get();
        return ['data' => $priorities];
    }

    public function createProjectPriority($projectId)
    {
        $project = Project::where('id', $projectId)
            ->first();
        if (empty($project)) {
            $this->notFound404('project');
        }

        $name = Input::get('name', null);
        $color = Input::get('color', null);
        $isDefault = Input::get('is_default', ProjectTaskPriority::UN_DEFAULT);

        if (empty($name)) {
            $this->notFound404('name');
        }
        if (empty($color)) {
            $this->notFound404('color');
        }
        if (!is_numeric($isDefault) || (
                $isDefault != ProjectTaskPriority::BE_DEFAULT &&
                $isDefault != ProjectTaskPriority::UN_DEFAULT
            )) {
            $this->notFound404('is_default');
        }

        $max = ProjectTaskPriority::where('project_id', $projectId)->max('indexes');
        if(is_null($max)){
            $max=1;
        }else {
            $max = $max +1;
        }

        $priority = ProjectTaskPriority::create([
            'name' => $name,
            'color' => $color,
            'is_default' => $isDefault,
            'indexes'=>$max,
            'project_id' => $projectId
        ]);


        if ($isDefault == ProjectTaskPriority::BE_DEFAULT) {
            ProjectTaskPriority::where('project_id', $projectId)
                ->where('id', '!=', $priority->id)
                ->update(['is_default' => ProjectTaskPriority::UN_DEFAULT]);
        }
        $priorities = ProjectTaskPriority::where('project_id', $projectId)->get();
        return ['data' => $priorities];
    }

    public function updateProjectPrioritySequence($projectId)
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
            $priority = ProjectTaskPriority::where('project_id', $projectId)
                ->where('indexes', $fromIndex)
                ->first();
            if (empty($priority)) {
                $this->notFound404('priority');
            }
            DB::transaction(function () use ($fromIndex, $toIndex, $projectId, $priority) {
                if ($fromIndex > $toIndex) {
                    DB::update('
                        update project_task_priorities set indexes = indexes + 1
                        where indexes >= ? and indexes < ? and id != ? and project_id =?
                    ', [$toIndex, $fromIndex, $priority->id,$projectId]);
                } else {
                    DB::update('
                        update project_task_priorities set indexes = indexes - 1 
                        where indexes > ? and indexes <= ? and id != ? and project_id =? 
                    ', [$fromIndex, $toIndex, $priority->id,$projectId]);
                }
                DB::update('
                        update project_task_priorities set indexes = ? where id = ? and project_id =?
                    ', [$toIndex, $priority->id,$projectId]);
            });
        }
        $priority = ProjectTaskPriority::where('project_id', $projectId)->get();
        return ['data' => $priority];
    }

    public function updateProjectPriority()
    {

    }

    public function deleteProjectPriority($projectId, $priorityId)
    {
        $priority = ProjectTaskPriority::where('id', $priorityId)
            ->where('project_id', $projectId)->first();
        if (empty($priority)) {
            $this->notFound404('priority');
        }
        $count = ProjectTaskPriority::where('project_id', $projectId)->count();
        if ($count <= 1) {
            $this->onDateError('must has a priority');
        }

        if ($priority->delete()) {
            if(ProjectTaskPriority::BE_DEFAULT == $priority->is_default){
                $tempPriority = ProjectTaskPriority::where('project_id',$projectId)->first();
                $tempPriority->is_default = ProjectTaskPriority::BE_DEFAULT;
                $tempPriority->save();
            }
            $priorities = ProjectTaskPriority::where('project_id', $projectId)->get();
            return ['data' => $priorities];
        } else {
            $this->onDBError($priority, 'delete project priority error');
        }
    }
}
