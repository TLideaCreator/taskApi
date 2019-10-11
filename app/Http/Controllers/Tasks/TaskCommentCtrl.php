<?php


namespace App\Http\Controllers\Tasks;


use App\Format\CommentFormat;
use App\Http\Controllers\ApiCtrl;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

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
        $page = $this->validatePage(Input::get('page', null));
        $perPage = $this->validatePageCount(Input::get('per_page', null));

        $commentsSql = TaskComment::where('task_id', $taskId);

        $comments = $commentsSql->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->orderBy('created_at','desc')
            ->get();
        return $this->toJsonArray($comments, ['creator'])->setMeta([
            'total' => $commentsSql->count(),
            'page' => $page,
            'per_page'=>$perPage
        ]);
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
        return $this->toJsonItem($comment,['creator']);
    }

    public function updateTaskComments(Request $request ,$taskId, $commentId)
    {

        $comment = TaskComment::where('id', $commentId)->first();
        if(empty($comment)){
            $this->notFound404('comment');
        }
        if($comment->creator_id != $request->user->id){
            $this->noPermission('no right to update');
        }
        $content = Input::get('content', null);
        if(empty($content)){
            $this->notFound404('comment');
        }
        $comment->content = $content;
        if($comment->save()){
            return $this->toJsonItem($comment,['creator']);
        }else{
            $this->onDBError($comment,'comment save error');
        }
    }

    public function delTaskComments(Request $request ,$taskId, $commentId)
    {
        TaskComment::where('id', $commentId)
            ->where('task_id',$taskId)
            ->where('creator_id',$request->user->id)
            ->delete();
        $comments = TaskComment::where('task_id', $taskId)->get();
        return $this->toJsonArray($comments, ['creator']);
    }
}
