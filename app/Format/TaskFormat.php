<?php


namespace App\Format;


use App\Models\Task;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class TaskFormat extends TransformerAbstract
{

    protected $availableIncludes = ['executor', 'reporter'];

    public function transform(Task $task)
    {
        return [
            'id' => $task->id,
            'indexes' => $task->indexes,
            'title' => $task->title,
            'desc' => $task->desc,
            'type' => $task->type,
            'sprint_id' => $task->sprint_id,
            'project_id' => $task->project_id,
            'points' => $task->points,
            'exe_id' => $task->exe_id,
            'report_id' => $task->report_id,
            'status' => $task->status,
            'priority' => $task->priority,
        ];
    }

    public function includeExecutor(Task $task)
    {
        $user = User::where('id', $task->exe_id)->first();
        if (empty($user)) {
            return null;
        }
        return $this->item($user, function (User $user) {
            return $this->userTransform($user);
        });
    }

    public function includeReporter(Task $task)
    {
        $user = User::where('id', $task->report_id)->first();
        if (empty($user)) {
            return null;
        }
        return $this->item($user, function (User $user) {
            return $this->userTransform($user);
        });
    }

    private function userTransform(User $user)
    {

        $item = [
            'id' => $user->id,
            'name' => $user->nickname,
            'avatar' => $user->avatar,
        ];
        return $item;

    }
}
