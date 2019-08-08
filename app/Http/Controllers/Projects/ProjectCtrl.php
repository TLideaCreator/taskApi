<?php


namespace App\Http\Controllers\Projects;


use App\Format\ProjectFormat;
use App\Http\Controllers\ApiCtrl;
use App\Methods\ProjectMethod;
use App\Models\Project;
use App\Models\Sprint;
use App\Models\SystemTaskTemp;
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
            )->orderBy('projects.updated_at','desc');
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
            $this->notFound404('name');
        }
        if (empty($icon)) {
            $this->notFound404('icon');
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
            DB::insert("
                insert into project_task_priorities (id, project_id, name, color, is_default)
                select replace(uuid(), '-', ''), ? ,name , color, is_default from system_task_priorities where temp_id= ?;",
                [$project->id, $tempId]);
            DB::insert("insert into project_task_status (id, project_id, name, indexes, type)
                select replace(uuid(), '-', '') , ? , name , indexes, type from system_task_status where temp_id = ?;",
                [$project->id, $tempId]);
            DB::insert("insert into project_task_roles (id, project_id, name, logo, project_mgr, sprint_mgr, task_mgr)
                select replace(uuid(), '-', '') , ? , name, logo, project_mgr, sprint_mgr, task_mgr from system_task_roles where temp_id = ?;",
                [$project->id, $tempId]);
            DB::insert("insert into project_task_types (id, project_id, name, icon)
                select replace(uuid(), '-', '') , ? , name , icon from system_task_types where temp_id = ?;",
                [$project->id, $tempId]);

            $role = DB::table('project_task_roles')->where('project_id', $project->id)->where('project_mgr', 1)->first();
            \Log::info ('role id here is '.json_encode($role));
            DB::table('project_users')->insert([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'role_id' => $role->id
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

        if (ProjectMethod::authUserForProject($user->id, $projectId) != 1) {
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
        if (ProjectMethod::authUserForProject($user->id, $projectId) != 1) {
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
        DB::table('project_users')
            ->where('project_id', $projectId)
            ->where('user_id', $user->id)
            ->update(['last_time' => time()]);

        $project = Project::where('id', $projectId)->first();
        return $this->toJsonItem($project, ['mgr']);
    }
}
