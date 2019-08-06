<?php


namespace App\Http\Controllers\SystemTemp;


use App\Http\Controllers\ApiCtrl;
use App\Models\SystemTaskTemp;
use App\Models\SystemTaskType;
use Illuminate\Support\Facades\Input;

class SystemTempTypeCtrl extends ApiCtrl
{
    /**
     * SystemTempCtrl constructor.
     */
    public function __construct()
    {

    }

    public function getSystemTemplateTypes($tempId)
    {
        $types = SystemTaskType::where('temp_id', $tempId)->get();
        return ['data'=>$types];
    }

    public function createSystemTemplateTypes($tempId)
    {
        $tempCount = SystemTaskTemp::where('id', $tempId)->count();
        if($tempCount < 1){
            $this->notFound404('temp');
        }
        $name = Input::get('name', null);
        $icon = Input::get('icon', null);

        if(empty($name)){
            $this->notFound404('name');
        }
        if(empty($icon)){
            $this->notFound404('icon');
        }

        SystemTaskType::create([
            'temp_id'=>$tempId,
            'name'=>$name,
            'icon'=>$icon
        ]);

        $types = SystemTaskType::where('temp_id', $tempId)->get();
        return ['data'=>$types];
    }

    public function updateSystemTemplateTypes($tempId,$typeId)
    {
        $type = SystemTaskType::where('id',$typeId)
            ->where('temp_id',$tempId)
            ->first();
        if(empty($type)){
            $this->notFound404('type');
        }
        $name = Input::get('name',null);
        $icon = Input::get('icon',null);
        if(is_null($name)){
            if(empty($name)){
                $this->notFound404('name');
            }
            $type->name = $name;
        }
        if(is_null($icon)){
            if(empty($icon)){
                $this->notFound404('icon');
            }
            $type->icon = $icon;
        }
        if($type->save()){
            $types = SystemTaskType::where('temp_id', $tempId)->get();
            return ['data'=>$types];
        }else{
            $this->onDBError($type, 'system template type delete ');
        }
    }

    public function deleteSystemTemplateTypes($tempId, $typeId)
    {
        SystemTaskType::where('id',$typeId)
            ->where('temp_id',$tempId)
            ->delete();
        $types = SystemTaskType::where('temp_id', $tempId)->get();
        return ['data'=>$types];
    }
}
