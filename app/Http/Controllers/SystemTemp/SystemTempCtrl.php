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
}