<?php


namespace App\Models;


class Sprint extends TM
{
    const STATUS_NORMAL = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_FINISH = 2;

    protected $fillable=[
        'name_index',
        'project_id',
        'status',
        'start_time',
        'end_time'
    ];
    protected $hidden=[
        'created_at',
        'updated_at'
    ];
}