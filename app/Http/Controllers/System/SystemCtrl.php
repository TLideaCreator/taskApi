<?php


namespace App\Http\Controllers\System;


use App\Http\Controllers\ApiCtrl;
use App\Models\SystemTaskTemp;
use Illuminate\Support\Facades\Log;

class SystemCtrl extends ApiCtrl
{
    public function getSystemTemplate()
    {
        $temps = SystemTaskTemp::get();
        return ['data'=>$temps];
    }
}