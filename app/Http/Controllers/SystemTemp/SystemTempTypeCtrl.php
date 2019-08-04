<?php


namespace App\Http\Controllers\SystemTemp;


use App\Http\Controllers\ApiCtrl;
use App\Models\SystemTaskType;

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
}
