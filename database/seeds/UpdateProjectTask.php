<?php


use App\Models\ProjectTaskPriority;
use App\Models\ProjectTaskStatus;
use App\Models\ProjectTaskType;
use App\Models\SystemTaskPriority;
use App\Models\SystemTaskStatus;
use App\Models\SystemTaskType;

class UpdateProjectTask extends BaseSeeder
{
    protected function task()
    {
        $tasks = \App\Models\Task::all();
        foreach ($tasks as $task) {
            $type = ProjectTaskType::where('project_id',$task->project_id)->first();
            $status = ProjectTaskStatus::where('project_id', $task->project_id)->where('indexes', 1)->first();
            $priority = ProjectTaskPriority::where('project_id', $task->project_id)->where('is_default', 1)->first();
            echo 'type is '.$type;
            $task->type = $type->id;
            $task->status = $status->id;
            $task->priority = $priority->id;
            $task->save();
        }
    }
}