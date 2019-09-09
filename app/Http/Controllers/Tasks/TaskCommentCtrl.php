<?php


namespace App\Http\Controllers\Tasks;


use App\Format\CommentFormat;
use App\Http\Controllers\ApiCtrl;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;

class TaskCommentCtrl extends ApiCtrl
{

    /**
     * TaskCommentCtrl constructor.
     */
    public function __construct()
    {
        $this->formatTransfer = new CommentFormat();
    }

    public function getTaskComments(Request $request , $taskId)
    {
        $comments = TaskComment::where('task_id', $taskId)->get();
        return $this->toJsonArray($comments, ['creator']);
    }

    public function createTaskComments(Request $request ,$taskId)
    {
        $content = Input::get('content', null);
        $projectId = Task::where('id', $taskId)->value('project_id');

        if(empty($content)){
            $this->onDateError('content');
        }
        $comment = TaskComment::create([
            'task_id' => $taskId,
            'creator_id' => $request->user->id,
            'project_id' => $projectId,
            'content' => $content
        ]);
        $this->toJsonItem($comment,['creator']);
    }

    public function updateTaskComments(Request $request ,$taskId, $commentId)
    {
        $comment = TaskComment::where('id', $commentId)->first();
        if(empty($comment)){
            $this->notFound404('comment');
        }
        $content = Input::get('content', null);
        if(empty($content)){
            $this->notFound404('comment');
        }
        $comment->content = $content;
        if($comment->save()){
            $this->toJsonItem($comment,['creator']);
        }else{
            $this->onDBError($comment,'comment save error');
        }

    }

    public function delTaskComments(Request $request ,$taskId, $commentId)
    {
        TaskComment::where('id', $commentId)->where('task_id',$taskId)->delete();
        $comments = TaskComment::where('task_id', $taskId)->get();
        return $this->toJsonArray($comments, ['creator']);
    }
}
