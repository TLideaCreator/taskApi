<?php


namespace App\Http\Controllers\SystemTemp;


use App\Http\Controllers\ApiCtrl;
use App\Models\SystemTaskPriority;

class SystemTaskPriorityCtrl extends ApiCtrl
{

    /**
     * SystemTempCtrl constructor.
     */
    public function __construct()
    {

    }

    public function getSystemTemplatePriority($tempId)
    {

    }

    public function createSystemTemplatePriority($tempId)
    {

    }

    public function updateSystemTemplatePriority($tempId, $priorityId)
    {
        $priority = SystemTaskPriority::where('id',$priorityId)->where('temp_id',$tempId)->first();
        if(empty($priority)){
            $this->notFound404('priority');
        }
        if($priority->save()){
            $priorities = SystemTaskPriority::where('temp_id', $tempId)->get();
            return ['data'=> $priorities];
        }else{
            $this->onDBError($priority, 'update system template priority error');
        }
    }

    public function deleteSystemTemplatePriority($tempId, $priorityId)
    {
        $priority = SystemTaskPriority::where('id',$priorityId)->where('temp_id',$tempId)->first();
        if(empty($priority)){
            $this->notFound404('priority');
        }
        if($priority->delete()){
            $priorities = SystemTaskPriority::where('temp_id', $tempId)->get();
            return ['data'=> $priorities];
        }else{
            $this->onDBError($priority, 'delete system template priority error');
        }
    }
}
