<?php


namespace App\Models;


class ProjectTaskStatus extends TM
{
    public $timestamps = false;

    protected $table = 'project_task_status';

    const TYPE_START = 0;
    const TYPE_PROGRESS = 1;
    const TYPE_FINISH = 2;

    protected $fillable = [
        'project_id',
        'name',
        'color',
        'indexes',
        'type'
    ];
}
