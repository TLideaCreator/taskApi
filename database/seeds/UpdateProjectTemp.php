<?php


use App\Models\ProjectTaskPriority;
use App\Models\ProjectTaskStatus;
use App\Models\ProjectTaskType;
use App\Models\SystemTaskPriority;
use App\Models\SystemTaskStatus;
use App\Models\SystemTaskType;

class UpdateProjectTemp extends BaseSeeder
{
    protected function task()
    {
        $projects = \App\Models\Project::all();
        $temp = \App\Models\SystemTaskTemp::first();
        $tempId = $temp->id;
        foreach ($projects as $project) {
            $status = SystemTaskStatus::where('temp_id', $tempId)->get();
            foreach ($status as $item) {
                $item->project_id = $project->id;
                unset($item->temp_id);
                ProjectTaskStatus::create(json_decode(json_encode($item), true));
            }

            $priorities = SystemTaskPriority::where('temp_id', $tempId)->get();
            foreach ($priorities as $priority) {
                $priority->project_id = $project->id;
                unset($priority->temp_id);
                ProjectTaskPriority::create(json_decode(json_encode($priority), true));
            }

            $types = SystemTaskType::where('temp_id', $tempId)->get();
            foreach ($types as $type) {
                $type->project_id = $project->id;
                unset($type->temp_id);
                ProjectTaskType::create(json_decode(json_encode($type), true));
            }

        }
    }
}