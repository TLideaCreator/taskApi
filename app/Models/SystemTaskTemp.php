<?php


namespace App\Models;


class SystemTaskTemp extends TM
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'desc',
        'img',
    ];
}