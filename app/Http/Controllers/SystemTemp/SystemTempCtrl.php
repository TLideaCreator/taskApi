<?php


namespace App\Http\Controllers\SystemTemp;


use App\Http\Controllers\ApiCtrl;
use App\Models\SystemTaskTemp;

class SystemTempCtrl extends ApiCtrl
{

    /**
     * SystemTempCtrl constructor.
     */
    public function __construct()
    {

    }

    public function getSystemTemplateList()
    {
        $tempList = SystemTaskTemp::all();
        return ['data'=>$tempList];
    }

    public function getSystemTemplateDetail($tempId){
        $template = SystemTaskTemp::where('id', $tempId)
            ->first();
        if(empty($template)){
            abort(404);
        }
        return ['data'=>$template];
    }
}
