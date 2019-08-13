<?php


namespace App\Models;


class ProjectTaskPriority extends TM
{
    public $timestamps = false;

    const BE_DEFAULT = 1;
    const UN_DEFAULT = 0;

    protected $fillable = [
        'project_id',
        'name',
        'color',
        'indexes',
        'is_default'
    ];
}
