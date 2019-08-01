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
