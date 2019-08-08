<?php


namespace App\Http\Controllers\Sprints;


use App\Format\SprintFormat;
use App\Http\Controllers\ApiCtrl;
use App\Models\ProjectTaskPriority;
use App\Models\ProjectTaskStatus;
use App\Models\ProjectTaskType;
use App\Models\Sprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Methods\ProjectMethod;

class SprintCtrl extends ApiCtrl
{

    /**
     * SprintCtrl constructor.
     */
    public function __construct()
    {
        $this->formatTransfer = new SprintFormat();
    }

    public function getProjectSprintList(Request $request, $projectId)
    {
        $type = Input::get('type', null);
        $sprints = Sprint::where(function ($query) use ($type){
            if($type === 'active'){
                $query->where('status', Sprint::STATUS_ACTIVE);
            }else{
                $query->whereIn('status', [Sprint::STATUS_NORMAL, Sprint::STATUS_ACTIVE]);
            }
        })
            ->where('project_id', $projectId)
            ->orderBy('status', 'desc')
            ->orderBy('name_index', 'desc')
            ->get();

        $priorities = ProjectTaskPriority::where('project_id', $projectId)->get();
        $status = ProjectTaskStatus::where('project_id', $projectId)->get();
        $types = ProjectTaskType::where('project_id', $projectId)->get();

        return $this->toJsonArray($sprints, ['tasks'])->setMeta([
            'priorities'=>$priorities,
            'status' => $status,
            'types' => $types,
        ]);
    }

    public function createSprints(Request $request, $projectId)
    {
        if(ProjectMethod::authUserForProject($request->user->id, $projectId) !== 1){
            abort(403);
        }

        $max = Sprint::where('project_id', $projectId)->max('name_index');

        $sprint = Sprint::create([
            'name_index'=>($max+1),
            'project_id'=>$projectId,
            'status'=>Sprint::STATUS_NORMAL
        ]);
        return $this->toJsonItem($sprint);
    }

    public function makeSprintsActive(Request $request, $sprintId)
    {
        $sprint = Sprint::where('id',$sprintId)->first();
        if(empty($sprint)){
            $this->notFound404('sprint');
        }

        if(ProjectMethod::authUserForProject($request->user->id, $sprint->project_id) !== 1){
            abort(403);
        }

        $activeCount = Sprint::where('project_id', $sprint->project_id)
            ->where('status', Sprint::STATUS_ACTIVE)
            ->count();

        if($activeCount > 0){
            Sprint::where('project_id', $sprint->project_id)->where('status', Sprint::STATUS_ACTIVE)->udpate([
                'status'=>Sprint::STATUS_NORMAL
            ]);
        }

        $sprint = Sprint::where('project_id', $sprint->project_id)->where('id', $sprintId)->first();
        $sprint->status = Sprint::STATUS_ACTIVE;
        $sprint->start_time = time();
        $sprint->save();
        return $this->toJsonItem($sprint);
    }

}
