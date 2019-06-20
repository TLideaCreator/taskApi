<?php


namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Laravel\Lumen\Routing\Controller;
use Dingo\Api\Routing\Helpers;

abstract class ApiCtrl extends Controller
{
    use Helpers;

    public $formatTransfer = null;


    /**
     * @param $items
     * @param array $relArray
     * @param $format
     * @return \Dingo\Api\Http\Response
     */
    protected function toJsonArray($items, $relArray=[], $format=null)
    {
        if(empty($format)){
            $format = $this->formatTransfer;
        }
        return $this->response->collection($items, $format,[], function (Collection $res, Manager $f) use ($relArray){
            $f->parseIncludes($relArray);
        });
    }

    protected function toJsonItem($item, $relArray=[], $format=null){
        if(empty($format)){
            $format = $this->formatTransfer;
        }
        return $this->response->item($item, $format,[], function (Item $res, Manager $f) use ($relArray){
            $f->parseIncludes($relArray);
        });
    }

    protected function errorItem(){

    }

    /**
     * @param $userId
     * @param $projectId
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    protected function authUserForProject($userId, $projectId)
    {
        $check = DB::table('project_users as pu')
            ->leftJoin('roles', 'pu.role_id', '=', 'roles.id')
            ->where('pu.user_id', $userId)
            ->where('pu.project_id', $projectId)
            ->select(
                'roles.create',
                'roles.read',
                'roles.update',
                'roles.delete'
            )->first();
        if(empty($check)){
            return null;
        }
        return $check;
    }

    public function validatePage($page)
    {
        if(!is_numeric($page) || $page < 1){
            $page = 1;
        }
        return $page;
    }

    public function validatePageCount($pageCount)
    {
        if(!is_numeric($pageCount) || $pageCount < 1){
            $pageCount = 10;
        }
        return $pageCount;
    }

    public function notFound404($msg)
    {
        abort(404,$_ENV['APP_DEBUG']=='true'? $msg: null);
    }
    public function noPermission($msg)
    {
        abort(403,$_ENV['APP_DEBUG']=='true'? $msg: null);
    }
}