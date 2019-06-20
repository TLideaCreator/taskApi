<?php


namespace App\Http\Controllers\Tasks;


use App\Format\TaskFormat;
use App\Http\Controllers\ApiCtrl;
use App\Models\ProjectTaskPriority;
use App\Models\ProjectTaskStatus;
use App\Models\ProjectTaskType;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class TaskCtrl extends ApiCtrl
{


    /**
     * TaskCtrl constructor.
     */
    public function __construct()
    {
        $this->formatTransfer = new TaskFormat();
    }

    public function createTask(Request $request, $sprintId)
    {
        $sprint = Sprint::where('id', $sprintId)->first();
        if(empty($sprint)){
            $this->notFound404('sprint');
        }

        $check = $this->authUserForProject($request->user->id, $sprint->project_id);
        if ($check->update != 1) {
            abort(403);
        }

        $title = Input::get('title', null);
        $desc = Input::get('desc', null);
        $type = Input::get('type', null);
        $exeId = Input::get('exe_id', null);
        $reportId = Input::get('report_id', null);
        $priority = Input::get('priority', null);
        $points = Input::get('points', 0.0);
        if (is_null($title)) {
            $this->notFound404('title');
        }
        if (is_null($desc)) {
            $this->notFound404('desc');
        }
        if (!Task::checkType($sprint->project_id, $type)) {
            $this->notFound404('type');
        }
        if (!Task::checkPriority($sprint->project_id,$priority)) {
            $this->notFound404('priority');
        }
        if (is_null($reportId)) {
            $reportId = $request->user->id;
        }else{
            $userCount = DB::table('project_users')
                ->where('project_id', $sprint->project_id)
                ->where('user_id', $reportId)
                ->count();
            if($userCount == 0){
                $this->notFound404('report');
            }
        }
        if (is_null($exeId)) {
            $exeId = '';
        } else {
            $userCount = DB::table('project_users')
                ->where('project_id', $sprint->project_id)
                ->where('user_id', $reportId)
                ->count();
            if($userCount == 0){
                $this->notFound404('exe');
            }
        }

        if(!is_numeric($points) || $points < 0){
            $this->notFound404( 'points');
        }

        $task = Task::create([
            'project_id'=>$sprint->project_id,
            'sprint_id'=>$sprintId,
            'title'=>$title,
            'desc'=>$desc,
            'type'=>$type,
            'priority'=>$priority,
            'exe_id'=>$exeId,
            'report_id'=>$reportId,
            'status'=>'0'
        ]);
        return $this->toJsonItem($task, ['executor' , 'reporter']);
    }

    public function getTaskDetail(Request $request,$taskId)
    {
        $task = Task::where('id', $taskId)->first();
        if(empty($task)){
            $this->notFound404('task');
        }
        $check = $this->authUserForProject($request->user->id,$task->project_id);
        if(is_null($check) || $check->update !== 1){
            abort(403);
        }
        $member = User::rightJoin('project_users as pu', 'pu.user_id', '=' , 'users.id')
            ->leftjoin('roles', 'roles.id', '=' ,'pu.role_id')
            ->where('pu.project_id', $task->project_id)
            ->select(
                'users.nickname as username',
                'users.id as user_id',
                'users.avatar',
                'roles.name as role_name',
                'roles.create',
                'roles.read',
                'roles.update',
                'roles.delete'
            )
            ->get();
        $priorities = ProjectTaskPriority::where('project_id', $task->project_id)->get();
        $status = ProjectTaskStatus::where('project_id', $task->project_id)->get();
        $types = ProjectTaskType::where('project_id', $task->project_id)->get();
        return $this->toJsonItem($task, ['executor' , 'reporter'])->setMeta([
            'project_members'=>$member,
            'project_priorities'=>$priorities,
            'project_status'=>$status,
            'project_types'=>$types
        ]);
    }

    public function updateTask(Request $request, $taskId)
    {
        $task = Task::where('id', $taskId)->first();
        if(empty($task)){
            $this->notFound404('task');
        }

        $check = $this->authUserForProject($request->user->id, $task->project_id);
        if ($check->update != 1) {
            abort(403);
        }
        $title = Input::get('title', null);
        $desc = Input::get('desc', null);
        $type = Input::get('type', null);
        $exeId = Input::get('exe_id', null);
        $reportId = Input::get('report_id', null);
        $priority = Input::get('priority', null);
        $points = Input::get('points', null);
        $status = Input::get('status', null);

        if (!empty($title) ) {
            $task->title = $title;
        }
        if (!empty($desc)) {
            $task->desc = $desc;
        }
        if (Task::checkType($task->project_id,$type)) {
            $task->type = $type;
        }
        if (Task::checkPriority($task->project_id, $priority)) {
            $task->priority = $priority;
        }
        if (Task::checkStatus($task->project_id, $status)) {
            $task->status = $status;
        }

        if (!empty($reportId)) {
            $userCount = DB::table('project_users')
                ->where('project_id', $task->project_id)
                ->where('user_id', $reportId)
                ->count();
            if($userCount != 0){
                $task->report_id = $reportId;
            }
        }
        if (!is_null($exeId)) {
            if(!empty($exeId)){
                $userCount = DB::table('project_users')
                    ->where('project_id', $task->project_id)
                    ->where('user_id', $reportId)
                    ->count();
                if($userCount != 0){
                    $task->exe_id = $exeId;
                }
            }else{
                $task->exe_id = $exeId;
            }
        }

        if(is_numeric($points) && $points >= 0){
            $task->points = $points;
        }
        $task->save();
        $member = User::rightJoin('project_users as pu', 'pu.user_id', '=' , 'users.id')
            ->leftjoin('roles', 'roles.id', '=' ,'pu.role_id')
            ->where('pu.project_id', $task->project_id)
            ->select(
                'users.nickname as username',
                'users.id as user_id',
                'users.avatar',
                'roles.name as role_name',
                'roles.create',
                'roles.read',
                'roles.update',
                'roles.delete'
            )
            ->get();
        return $this->toJsonItem($task, ['executor' , 'reporter'])->setMeta(['project_members'=>$member]);
    }

    public function moveTaskToSprint(Request $request, $taskId, $sprintId)
    {
        $task = Task::where('id', $taskId)->first();
        if(empty($task)){
            $this->notFound404('task');
        }

        $check = $this->authUserForProject($request->user->id, $task->project_id);
        if ($check->update != 1) {
            abort(403);
        }

        $sprintCount = Sprint::where('id', $sprintId)->where('project_id', $task->project_id)->count();
        if($sprintCount == 0){
            $this->notFound404('sprint');
        }
        $task->sprint_id = $sprintId;
        $task->save();
        return $this->toJsonItem($task, ['executor' , 'reporter']);
    }
}