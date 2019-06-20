<?php


namespace App\Models;


use League\Fractal\Pagination\CursorInterface;

class Project extends TM implements CursorInterface
{
    const STATUS_FINISH = 1;
    const STATUS_ACTIVE = 0;
    const STATUS_CANCEL = -1;
    protected $fillable=[
        'name',
        'icon',
        'desc',
        'status',
        'creator_id',
        'cur_sprint_id'
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function getCurrent()
    {
        // TODO: Implement getCurrent() method.
    }

    public function getPrev()
    {
        // TODO: Implement getPrev() method.
    }

    public function getNext()
    {
        // TODO: Implement getNext() method.
    }

    public function getCount()
    {
        // TODO: Implement getCount() method.
    }


}