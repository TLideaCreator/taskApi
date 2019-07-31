<?php


namespace App\Http\Controllers\System;


use App\Http\Controllers\ApiCtrl;
use App\Models\Project;
use App\Models\SystemTaskTemp;
use App\Models\User;

class SystemCtrl extends ApiCtrl
{
    public function getSystemTemplate()
    {
        $temps = SystemTaskTemp::all();
        return ['data'=>$temps];
    }

    public function getSystemStatics()
    {
        $userCount= User::count();
        $projectCount= Project::count();
        $tempCount= SystemTaskTemp::count();
        return ['data'=>[
            'users'=>$userCount,
            'projects'=>$projectCount,
            'temps'=>$tempCount,
        ]];
    }
}
