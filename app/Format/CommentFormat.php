<?php


namespace App\Format;


use App\Models\TaskComment;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class CommentFormat extends TransformerAbstract
{
    protected $availableIncludes = ['creator'];


    public function transform(TaskComment $taskComment)
    {
        return [
            'id' => $taskComment->id,
            'creator_id' => $taskComment->creator_id,
            'content' => $taskComment->content,
            'created_at' => strtotime($taskComment->created_at),
            'updated_at' => strtotime($taskComment->updated_at),
            'task_id' => $taskComment->task_id,
        ];
    }

    public function includeCreator(TaskComment $taskComment)
    {
        $user = User::where('id', $taskComment->creator_id)->first();
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
