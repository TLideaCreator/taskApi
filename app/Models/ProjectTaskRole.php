<?php


namespace App\Models;


class ProjectTaskRole extends TM
{
    public $timestamps = false;
    protected $fillable=[
      'project_id',
      'name',
      'logo',
      'project_mgr',
      'sprint_mgr',
      'task_mgr'
    ];
}