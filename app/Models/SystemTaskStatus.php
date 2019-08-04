<?php


namespace App\Models;


class SystemTaskStatus extends TM
{
    public $timestamps = false;

    protected $table = 'system_task_status';

    const TYPE_START = 0;
    const TYPE_PROGRESS = 1;
    const TYPE_FINISH = 2;

    protected $fillable = [
        'temp_id',
        'name',
        'color',
        'indexes',
        'type'
    ];
}
