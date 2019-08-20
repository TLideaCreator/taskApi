<?php


namespace App\Http\Controllers\Sprints;


use App\Format\SprintFormat;
use App\Http\Controllers\ApiCtrl;
use App\Models\ProjectTaskPriority;
use App\Models\ProjectTaskStatus;
use App\Models\ProjectTaskType;
use App\Models\Sprint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $sprints = Sprint::where(function ($query) use ($type) {
            if ($type === 'active') {
                $query->where('status', Sprint::STATUS_ACTIVE);
            } else {
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
            'priorities' => $priorities,
            'status' => $status,
            'types' => $types,
        ]);
    }

    public function createSprints(Request $request, $projectId)
    {
        $max = Sprint::where('project_id', $projectId)->max('name_index');

        $sprint = Sprint::create([
            'name_index' => ($max + 1),
            'project_id' => $projectId,
            'status' => Sprint::STATUS_NORMAL
        ]);
        return $this->toJsonItem($sprint, ['tasks']);
    }

    public function makeSprintsActive(Request $request, $sprintId)
    {
        $sprint = Sprint::where('id', $sprintId)
            ->where('name_index', '!=', 0)
            ->first();
        if (empty($sprint)) {
            $this->notFound404('sprint');
        }

        if (!ProjectMethod::authUserForSprint($request->user->id, $sprint->project_id)) {
            abort(403);
        }

        $activeCount = Sprint::where('project_id', $sprint->project_id)
            ->where('status', Sprint::STATUS_ACTIVE)
            ->count();

        if ($activeCount > 0) {
            $this->onDateError('all ready exist active sprint ');
        }
        $sprint->status = Sprint::STATUS_ACTIVE;
        $sprint->start_time = time();
        $sprint->save();
        return $this->toJsonItem($sprint);
    }

    public function makeSprintsFinish(Request $request, $sprintId)
    {
        $sprint = Sprint::where('id', $sprintId)
            ->where('status', Sprint::STATUS_ACTIVE)
            ->first();
        if (empty($sprint)) {
            $this->notFound404('sprint');
        }

        if (!ProjectMethod::authUserForSprint($request->user->id, $sprint->project_id)) {
            abort(403);
        }
        try {
            DB::transaction(function () use ($sprint) {
                $startStatus = ProjectTaskStatus::where('project_id', $sprint->project_id)
                    ->orderBy('indexes', 'asc')
                    ->first();
                if (empty($startStatus)) {
                    throw new \Exception(404);
                }
                $startSprint = Sprint::where('project_id', $sprint->project_id)->where('name_index', 0)->first();

                DB::update('update tasks set status = ? , sprint_id = ? where sprint_id = ? ',
                    [$startStatus->id, $startSprint->id, $sprint->id]);

                $sprint->status = Sprint::STATUS_FINISH;
                $sprint->end_time = time();
                if (!$sprint->save()) {
                    throw new \Exception(500);
                }
                return $sprint;
            });
            return $this->toJsonItem($sprint);
        } catch (\Exception $ex) {
            switch ($ex->getMessage()) {
                case '404':
                    $this->notFound404('task status');
                    break;
                default :
                    $this->onDBError($ex, 'save sprint');
                    break;
            }
        }
    }
}
