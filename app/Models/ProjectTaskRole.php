<?php


namespace App\Models;


class ProjectTaskRole extends TM
{
    public $timestamps = false;

    const ENABLE = 1;
    const DISABLE = 0;

    protected $fillable=[
      'project_id',
      'name',
      'logo',
      'project_mgr',
      'sprint_mgr',
      'task_mgr'
    ];
}
