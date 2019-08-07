<?php


namespace App\Http\Controllers\System;


use App\Format\UserFormat;
use App\Http\Controllers\ApiCtrl;
use App\Models\User;
use Illuminate\Support\Facades\Input;

class SystemUserCtrl extends ApiCtrl
{


    /**
     * SystemUserCtrl constructor.
     */
    public function __construct()
    {
        $this->formatTransfer = new UserFormat();
    }

    public function getSystemUser(){
        $page =  $this->validatePage(Input::get('page',null));
        $pageCount =  $this->validatePageCount(Input::get('per_page',null));
        $searchKey = Input::get('searchKey',null);

        $userQuery = User::where(function ($query) use ($searchKey){
            if(!is_null($searchKey)){
                $query->where('nickname', 'like', "%{$searchKey}%")
                    ->orWhere('phone', 'like', "%{$searchKey}%")
                    ->orWhere('email', 'like', "%{$searchKey}%");
            }
        });
        $total = $userQuery->count();
        $userList = $userQuery->skip(($page-1)*$pageCount)->take($pageCount)->get();
        return $this->toJsonArray($userList)->setMeta([
            'total'=>$total,
            'page'=>$page,
            'page_count'=>$pageCount
        ]);
    }
    public function createSystemUser(){

    }
    public function getSystemUserDetail($userId){

    }
    public function updateSystemUser($userId){

    }
    public function deleteSystemUser($userId){

    }

}
