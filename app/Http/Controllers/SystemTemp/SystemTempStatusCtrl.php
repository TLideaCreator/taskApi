<?php


namespace App\Http\Controllers\SystemTemp;


use App\Http\Controllers\ApiCtrl;
use App\Models\SystemTaskStatus;
use App\Models\SystemTaskTemp;
use Illuminate\Support\Facades\Input;

class SystemTempStatusCtrl extends ApiCtrl
{

    /**
     * SystemTempCtrl constructor.
     */
    public function __construct()
    {

    }

    public function getSystemTemplateStatus($tempId)
    {
        $status = SystemTaskStatus::where('temp_id', $tempId)->get();
        return ['data' => $status];
    }

    public function createSystemTemplateStatus($tempId)
    {
        $name = Input::get('name', null);
        $color = Input::get('color', null);
        if(empty($name) || empty($color)){
            $this->notFound404('params');
        }
        $tempCount = SystemTaskTemp::where('id',$tempId)->count();
        if($tempCount < 1 ){
            $this->notFound404('status');
        }
        $tempMax =  SystemTaskStatus::where('temp_id',$tempId)->max('indexes');
        if(empty($tempMax)){
            $tempMax = 0;
        }
        SystemTaskStatus::create([
            'temp_id'=>$tempId,
            'name'=>$name,
            'color'=>$color,
            'indexes'=>$tempMax
        ]);
        $status = SystemTaskStatus::where('temp_id', $tempId)->get();
        return ['data' => $status];
    }

    public function updateSystemTemplateStatus($tempId,$statusId)
    {
        $status = SystemTaskStatus::where('id', $statusId)
            ->where('temp_id',$tempId)
            ->first();
        if (empty($status)) {
            $this->notFound404('status');
        }
        $name = Input::get('name', null);
        $color = Input::get('color', null);
        if (!is_null($name)) {
            if (empty($name)) {
                $this->notFound404('name');
            } else {
                $status->name = $name;
            }
        }
        if (!is_null($color)) {
            if (empty($color)) {
                $this->notFound404('color');
            } else {
                $status->color = $color;
            }
        }
        $status->save();
        $status = SystemTaskStatus::where('temp_id', $tempId)->get();
        return ['data' => $status];
    }

    public function deleteSystemTemplateStatus($tempId,$statusId)
    {
        SystemTaskStatus::where('id', $statusId)
            ->where('temp_id',$tempId)
            ->delete();
        $status = SystemTaskStatus::where('temp_id', $tempId)->get();
        return ['data' => $status];
    }
}
