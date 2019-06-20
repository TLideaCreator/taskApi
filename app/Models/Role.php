<?php


namespace App\Models;


class Role extends TM
{
    public $timestamps = false;
    protected $fillable = [
        'name',
        'type',
        'icon',
        'create',
        'read',
        'update',
        'delete',
    ];
}