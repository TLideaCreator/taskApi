<?php


namespace App\Http\Controllers\System;


use App\Http\Controllers\ApiCtrl;
use App\Models\Project;
use App\Models\SystemTaskTemp;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SystemCtrl extends ApiCtrl
{
    public function getSystemTemplate()
    {
        $temps = SystemTaskTemp::get();
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