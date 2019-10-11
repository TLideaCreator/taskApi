<?php


namespace App\Models;


class ProjectDoc extends TM
{
    protected $fillable=[
        'project_id',
        'file_name',
        'doc_name',
        'file_path',
        'creator_id',
        'update_id',
        'version',
        'status'
    ];
}
