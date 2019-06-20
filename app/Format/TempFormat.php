<?php


namespace App\Format;


use App\Models\SystemTaskTemp;
use League\Fractal\TransformerAbstract;

class TempFormat extends TransformerAbstract
{
    public function transform(SystemTaskTemp $temp)
    {
        return [
            'id'=>$temp->id,
            'name'=>$temp->name,
            'desc'=>$temp->desc,
            'img'=>$temp->img
        ];
    }
}