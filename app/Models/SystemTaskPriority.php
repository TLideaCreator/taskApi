<?php


namespace App\Models;


class SystemTaskPriority extends TM
{
    public $timestamps = false;


    protected $fillable = [
        'temp_id',
        'name',
        'color',
        'is_default'
    ];
}