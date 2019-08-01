<?php


namespace App\Http\Controllers\SystemTemp;


use App\Format\TempFormat;
use App\Http\Controllers\ApiCtrl;
use App\Models\SystemTaskPriority;
use App\Models\SystemTaskRole;
use App\Models\SystemTaskStatus;
use App\Models\SystemTaskTemp;
use App\Models\SystemTaskType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class SystemTempCtrl extends ApiCtrl
{

    /**
     * SystemTempCtrl constructor.
     */
    public function __construct()
    {
        $this->formatTransfer = new TempFormat();
    }

    public function getSystemTemplateList()
    {
        $tempList = SystemTaskTemp::all();
        return $this->toJsonArray($tempList);
    }

    public function getSystemTemplateDetail($tempId)
    {
        $template = SystemTaskTemp::where('id', $tempId)
            ->first();
        if (empty($template)) {
            abort(404);
        }
        $tempRolesCount = SystemTaskRole::where('temp_id', $tempId)->count();
        $tempStatusCount = SystemTaskStatus::where('temp_id', $tempId)->count();
        $tempPrioritiesCount = SystemTaskPriority::where('temp_id', $tempId)->count();
        $tempTypeCount = SystemTaskType::where('temp_id', $tempId)->count();
        return $this->toJsonItem($template)->setMeta([
                'role_count'=>$tempRolesCount,
                'status_count'=>$tempStatusCount,
                'priority_count'=>$tempPrioritiesCount,
                'type_count'=>$tempTypeCount,
        ]);
    }

    public function updateSystemTemplate($tempId){
        $template = SystemTaskTemp::where('id', $tempId)
            ->first();
        if (empty($template)) {
            abort(404);
        }
        $title = Input::get('name',null);
        $desc = Input::get('desc',null);
        $img = Input::get('img',null);
        if(!is_null($title)){
            if(empty($title)){
                $this->notFound404('title');
            }
            $template->name = $title;
        }
        if(!is_null($desc)){
            if(empty($desc)){
                $this->notFound404('desc');
            }
            $template->desc = $desc;
        }
        if(!is_null($img)){
            if(empty($img)){
                $this->notFound404('img');
            }
            $template->img = $img;
        }
        if($template->save()){
            return $this->toJsonItem($template);
        }else{
            \Log::error('save template error '.json_encode($template));
            abort(404);
        };
        return null;
    }

    public function deleteTemplate($tempId)
    {
        try{
            DB::transaction(function () use ($tempId){
                SystemTaskRole::where('temp_id', $tempId)->delete();
                SystemTaskType::where('temp_id', $tempId)->delete();
                SystemTaskStatus::where('temp_id', $tempId)->delete();
                SystemTaskPriority::where('temp_id', $tempId)->delete();
                SystemTaskTemp::where('id', $tempId)->delete();
            });
            return [];
        }catch(\Exception $ex){
            \Log::error('delete error '.json_encode($ex));
            abort(404);
        }
    }
}
