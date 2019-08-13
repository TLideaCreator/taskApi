<?php


namespace App\Models;


class SystemTaskPriority extends TM
{
    public $timestamps = false;

    const BE_DEFAULT = 1;
    const UN_DEFAULT = 0;

    protected $fillable = [
        'temp_id',
        'name',
        'color',
        'indexes',
        'is_default'
    ];
}
