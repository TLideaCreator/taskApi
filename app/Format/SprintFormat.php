<?php


namespace App\Format;


use App\Models\Sprint;
use App\Models\Task;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class SprintFormat extends TransformerAbstract
{

    protected $availableIncludes = ['tasks'];

    public function transform(Sprint $sprint)
    {
        $item = [
            'id' => $sprint->id,
            'name' => $sprint->name_index,
            'status' => $sprint->status,
            'project_id' => $sprint->project_id,
            'start_time' => $sprint->start_time,
            'end_time' => $sprint->end_time
        ];
        return $item;
    }

    public function includeTasks(Sprint $sprint)
    {
        $tasks = Task::where('project_id', $sprint->project_id)
            ->where('sprint_id', $sprint->id)
            ->get();
        return $this->collection($tasks, function (Task $task) {
            $executor = User::where('id', $task->exe_id)->first();
            $executorObj = null;
            if (!empty($executor)) {
                $executorObj = [
                    'data' => [
                        'id' => $executor->id,
                        'avatar' => $executor->avatar,
                        'name' => $executor->nickname
                    ]
                ];

            }
            return [
                'id' => $task->id,
                'title' => $task->title,
                'desc' => $task->desc,
                'type' => $task->type,
                'sprint_id' => $task->sprint_id,
                'points' => $task->points,
                'status' => $task->status,
                'exe_id'=> $task->exe_id,
                'report_id'=> $task->report_id,
                'priority' => $task->priority,
                'executor' => $executorObj
            ];
        });
    }
}