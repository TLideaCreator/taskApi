<?php


namespace App\Models;


class SystemTaskType extends TM
{
    public $timestamps = false;

    protected $fillable = [
        'temp_id',
        'name',
        'icon'
    ];
}