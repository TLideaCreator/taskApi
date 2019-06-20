<?php


namespace App\Models;


class ProjectTaskType extends TM
{
    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'name',
        'icon',
    ];
}