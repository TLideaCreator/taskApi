<?php


namespace App\Models;


class SystemTaskRole extends TM
{
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