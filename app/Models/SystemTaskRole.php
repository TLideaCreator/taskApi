<?php


namespace App\Models;


class SystemTaskRole extends TM
{
    const ENABLE = 1;
    const DISABLE = 0;

    public $timestamps = false;
    protected $fillable=[
      'temp_id',
      'name',
      'logo',
      'project_mgr',
      'sprint_mgr',
      'task_mgr',
    ];
}
