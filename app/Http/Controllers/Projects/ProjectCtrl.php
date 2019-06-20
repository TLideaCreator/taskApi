<?php


namespace App\Http\Controllers\Projects;


use App\Format\ProjectFormat;
use App\Http\Controllers\ApiCtrl;
use App\Models\Project;
use App\Models\ProjectTaskPriority;
use App\Models\ProjectTaskStatus;
use App\Models\ProjectTaskType;
use App\Models\Sprint;
use App\Models\SystemTaskPriority;
use App\Models\SystemTaskStatus;
use App\Models\SystemTaskTemp;
use App\Models\SystemTaskType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ProjectCtrl extends ApiCtrl
{


    /**
     * ProjectCtrl constructor.
     */
    public function __construct()
    {
        $this->formatTransfer = new ProjectFormat();
    }

    public function projectList(Request $request)
    {
        $page = $this->validatePage(Input::get('page', null));
        $perPage = $this->validatePageCount(Input::get('per_page', null));
        $searchKey = Input::get('searchKey', null);
        $user = $request->user;
        $projectQuery = Project::leftjoin('project_users as pu', 'pu.project_id', '=', 'projects.id')
            ->where('pu.user_id', $user->id)
            ->where('projects.status', '!=', Project::STATUS_CANCEL)
            ->where(function ($query) use ($searchKey){
                if(!is_null($searchKey)){
                    $query->where('projects.name','like', "%{$searchKey}%");
                }
            })
            ->select(
                'projects.id',
                'projects.name',
                'projects.icon',
                'projects.desc',
                'projects.cur_sprint_id'
            );
        $count = $projectQuery->count();
        $projects = $projectQuery->skip(($page - 1) * $perPage)->take($perPage)->get();
        return $this->toJsonArray($projects, ['mgr'])
            ->setMeta([
                'total'=>$count,
                'page'=>$page,
                'size'=>$perPage
                ]);
    }

    public function getLastProjectList(Request $request)
    {
        $user = $request->user;
        $projects = Project::leftjoin('project_users as pu', 'pu.project_id', '=', 'projects.id')
            ->where('pu.user_id', $user->id)
            ->where('projects.status', '!=', Project::STATUS_CANCEL)
            ->select(
                'projects.id',
                'projects.name',
                'projects.icon',
                'projects.desc',
                'projects.cur_sprint_id'
            )
            ->take(8)
            ->orderBy('pu.last_time','desc')
            ->get();
        return $this->toJsonArray($projects, ['mgr']);
    }

    public function createProject(Request $request)
    {
        $name = Input::get('name', null);
        $icon = Input::get('icon', null);
        $desc = Input::get('desc', null);
        $tempId = Input::get('temp', null);
        if (empty($name)) {
            abort(400);
        }
        if (empty($icon)) {
            abort(400);
        }
        $temp = SystemTaskTemp::where('id', $tempId)->first();
        if (empty($temp)) {
            $this->notFound404('template');
        }
        $user = $request->user;
        $project = DB::transaction(function () use ($user, $name, $desc, $icon, $tempId) {

            $project = Project::create([
                'name' => $name,
                'icon' => $icon,
                'status' => 0,
                'creator_id' => $user->id,
                'cur_sprint_id' => '',
                'desc' => $desc
            ]);
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

            DB::table('project_users')->insert([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'role_id' => DB::table('roles')->where('type', 1)->value('id')
            ]);
            Sprint::create([
                'name_index' => 0,
                'project_id' => $project->id,
                'status' => Sprint::STATUS_NORMAL
            ]);
            return $project;
        });

        return $this->toJsonItem($project, ['mgr']);
    }

    public function updateProject(Request $request, $projectId)
    {
        $user = $request->user;

        $check = $this->authUserForProject($user->id, $projectId);
        if (empty($check) || $check->delete != 1) {
            abort(403);
        }

        $name = Input::get('name', null);
        $icon = Input::get('icon', null);
        $desc = Input::get('desc', null);
        $project = Project::where('id', $projectId)->first();
        if (!empty($name)) {
            $project->name = $name;
        }
        if (!empty($icon)) {
            $project->icon = $icon;
        }
        if (!empty($desc)) {
            $project->desc = $desc;
        }
        $project->save();
        return $this->toJsonItem($project, ['mgr']);
    }

    public function removeProject(Request $request, $projectId)
    {
        $user = $request->user;
        $check = $this->authUserForProject($user->id, $projectId);
        if (empty($check) || $check->delete != 1) {
            abort(403);
        }
        $project = Project::where('id', $projectId)->first();
        $project->status = Project::STATUS_CANCEL;
        $project->save();
        return [];
    }

    public function projectDetail(Request $request, $projectId)
    {
        $user = $request->user;
        $check = $this->authUserForProject($user->id, $projectId);
        if (empty($check) || $check->read != 1) {
            abort(403, 'project');
        }
        DB::table('project_users')
            ->where('project_id', $projectId)
            ->where('user_id', $user->id)
            ->update(['last_time' => time()]);

        $project = Project::where('id', $projectId)->first();
        return $this->toJsonItem($project, ['mgr']);
    }
}