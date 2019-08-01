<?php


namespace App\Http\Controllers\SystemTemp;


use App\Http\Controllers\ApiCtrl;
use App\Models\SystemTaskRole;

class SystemTempRoleCtrl extends ApiCtrl
{

    /**
     * SystemTempCtrl constructor.
     */
    public function __construct()
    {

    }

    public function getSystemTemplateRole($tempId)
    {
        $roleList = SystemTaskRole::where('temp_id', $tempId)
            ->get();
        return ['data'=>$roleList];
    }
}
