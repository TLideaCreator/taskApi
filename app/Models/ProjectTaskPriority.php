<?php


namespace App\Models;


class ProjectTaskPriority extends TM
{
    public $timestamps = false;


    protected $fillable = [
        'project_id',
        'name',
        'color',
        'is_default'
    ];
}