<?php


namespace App\Http\Controllers\SystemTemp;


use App\Http\Controllers\ApiCtrl;
use App\Models\SystemTaskPriority;
use App\Models\SystemTaskTemp;
use Illuminate\Support\Facades\Input;

class SystemTempPriorityCtrl extends ApiCtrl
{

    /**
     * SystemTempCtrl constructor.
     */
    public function __construct()
    {

    }

    public function getSystemTemplatePriority($tempId)
    {
        $priorities = SystemTaskPriority::where('temp_id', $tempId)->get();
        return ['data' => $priorities];
    }

    public function createSystemTemplatePriority($tempId)
    {
        $temp = SystemTaskTemp::where('id', $tempId)
            ->first();
        if (empty($temp)) {
            $this->notFound404('priority');
        }

        $name = Input::get('name', null);
        $color = Input::get('color', null);
        $isDefault = Input::get('is_default', SystemTaskPriority::UN_DEFAULT);

        if (empty($name)) {
            $this->notFound404('name');
        }
        if (empty($color)) {
            $this->notFound404('color');
        }
        if (!is_numeric($isDefault) || (
                $isDefault != SystemTaskPriority::BE_DEFAULT &&
                $isDefault != SystemTaskPriority::UN_DEFAULT
            )) {
            $this->notFound404('is_default');
        }

        $max = SystemTaskPriority::where('temp_id', $tempId)->max('indexes');
        if(is_null($max)){
            $max=1;
        }else {
            $max = $max +1;
        }

        $priority = SystemTaskPriority::create([
            'name' => $name,
            'color' => $color,
            'is_default' => $isDefault,
            'indexes'=>$max,
            'temp_id' => $tempId
        ]);


        if ($isDefault == SystemTaskPriority::BE_DEFAULT) {
            SystemTaskPriority::where('temp_id', $tempId)
                ->where('id', '!=', $priority->id)
                ->update(['is_default' => SystemTaskPriority::UN_DEFAULT]);
        }
        $priorities = SystemTaskPriority::where('temp_id', $tempId)->get();
        return ['data' => $priorities];
    }

    public function updateSystemTemplatePriority($tempId, $priorityId)
    {
        $priority = SystemTaskPriority::where('id', $priorityId)
            ->where('temp_id', $tempId)
            ->first();
        if (empty($priority)) {
            $this->notFound404('priority');
        }
        $name = Input::get('name', null);
        $color = Input::get('color', null);
        $isDefault = Input::get('is_default', null);

        if (!is_null($name)) {
            if (empty($name)) {
                $this->notFound404('name');
            }
            $priority->name = $name;
        }
        if (!is_null($color)) {
            if (empty($color)) {
                $this->notFound404('color');
            }
            $priority->color = $color;
        }
        if (!is_null($isDefault)) {
            if (!is_numeric($isDefault) || (
                    $isDefault != SystemTaskPriority::BE_DEFAULT &&
                    $isDefault != SystemTaskPriority::UN_DEFAULT
                )) {
                $this->notFound404('is_default');
            }
            if (
                SystemTaskPriority::UN_DEFAULT == $isDefault &&
                SystemTaskPriority::BE_DEFAULT == $priority->is_default
            ) {
                $this->onDateError('must has default priority');
            }

            $priority->is_default = $isDefault;
        }

        if ($priority->save()) {
            if ($isDefault === SystemTaskPriority::BE_DEFAULT) {
                SystemTaskPriority::where('temp_id', $tempId)
                    ->where('id', '!=', $priorityId)
                    ->update(['is_default' => SystemTaskPriority::UN_DEFAULT]);
            }
            $priorities = SystemTaskPriority::where('temp_id', $tempId)->get();
            return ['data' => $priorities];
        } else {
            $this->onDBError($priority, 'update system template priority error');
        }
    }

    public function deleteSystemTemplatePriority($tempId, $priorityId)
    {
        $priority = SystemTaskPriority::where('id', $priorityId)->where('temp_id', $tempId)->first();
        if (empty($priority)) {
            $this->notFound404('priority');
        }
        $count = SystemTaskPriority::where('temp_id', $tempId)->count();
        if ($count <= 1) {
            $this->onDateError('must has a priority');
        }

        if ($priority->delete()) {
            if(SystemTaskPriority::BE_DEFAULT == $priority->is_default){
                $tempPriority = SystemTaskPriority::where('temp_id',$tempId)->first();
                $tempPriority->is_default = SystemTaskPriority::BE_DEFAULT;
                $tempPriority->save();
            }
            $priorities = SystemTaskPriority::where('temp_id', $tempId)->get();
            return ['data' => $priorities];
        } else {
            $this->onDBError($priority, 'delete system template priority error');
        }
    }
}
