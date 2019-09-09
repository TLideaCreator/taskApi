<?php


namespace App\Models;


class TaskComment extends TM
{
    protected $fillable = [
        'creator_id',
        'task_id',
        'project_id',
        'content',
    ];
}
