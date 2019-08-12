<?php


namespace App\Http\Controllers\SystemTemp;


use App\Http\Controllers\ApiCtrl;
use App\Models\SystemTaskStatus;
use App\Models\SystemTaskTemp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class SystemTempStatusCtrl extends ApiCtrl
{

    /**
     * SystemTempCtrl constructor.
     */
    public function __construct()
    {

    }

    public function getSystemTemplateStatus($tempId)
    {
        $status = SystemTaskStatus::where('temp_id', $tempId)->get();
        return ['data' => $status];
    }

    public function createSystemTemplateStatus($tempId)
    {
        $name = Input::get('name', null);
        $color = Input::get('color', null);
        if (empty($name) || empty($color)) {
            $this->notFound404('params');
        }
        $tempCount = SystemTaskTemp::where('id', $tempId)->count();
        if ($tempCount < 1) {
            $this->notFound404('status');
        }
        $tempMax = SystemTaskStatus::where('temp_id', $tempId)->max('indexes');
        if (empty($tempMax)) {
            $tempMax = 0;
        }
        SystemTaskStatus::create([
            'temp_id' => $tempId,
            'name' => $name,
            'color' => $color,
            'indexes' => $tempMax
        ]);
        $status = SystemTaskStatus::where('temp_id', $tempId)->get();
        return ['data' => $status];
    }

    public function updateSystemTemplateStatus($tempId, $statusId)
    {
        $status = SystemTaskStatus::where('id', $statusId)
            ->where('temp_id', $tempId)
            ->first();
        if (empty($status)) {
            $this->notFound404('status');
        }
        $name = Input::get('name', null);
        $color = Input::get('color', null);
        if (!is_null($name)) {
            if (empty($name)) {
                $this->notFound404('name');
            } else {
                $status->name = $name;
            }
        }
        if (!is_null($color)) {
            if (empty($color)) {
                $this->notFound404('color');
            } else {
                $status->color = $color;
            }
        }
        if ($status->save()) {
            $status = SystemTaskStatus::where('temp_id', $tempId)->get();
            return ['data' => $status];
        } else {
            $this->onDBError($status, 'update system template status error');
        }
    }

    public function deleteSystemTemplateStatus($tempId, $statusId)
    {
        SystemTaskStatus::where('id', $statusId)
            ->where('temp_id', $tempId)
            ->delete();
        $status = SystemTaskStatus::where('temp_id', $tempId)->get();
        return ['data' => $status];
    }

    public function updateTemplateSequence($tempId)
    {
        $fromIndex = Input::get('from', null);
        $toIndex = Input::get('to', null);
        if (!is_numeric($fromIndex)) {
            $this->notFound404('from');
        }
        if (!is_numeric($toIndex)) {
            $this->notFound404('to');
        }
        if ($fromIndex != $toIndex) {
            $status = SystemTaskStatus::where('temp_id', $tempId)
                ->where('indexes', $fromIndex)
                ->first();
            if (empty($status)) {
                $this->notFound404('status');
            }
            DB::transaction(function () use ($fromIndex, $toIndex, $tempId, $status) {
                if ($fromIndex > $toIndex) {
                    DB::update('
                        update system_task_status set indexes = indexes + 1 
                        where indexes >= ? and indexes < ? and id != ?
                    ',[$toIndex, $fromIndex, $status->id]);
                    DB::update('
                        update system_task_status set indexes = ? where id = ?
                    ',[$toIndex, $status->id]);
                } else {
                    DB::update('
                        update system_task_status set indexes = indexes - 1 
                        where indexes > ? and indexes <= ? and id != ?
                    ',[$fromIndex, $toIndex, $status->id]);
                    DB::update('
                        update system_task_status set indexes = ? where id = ?
                    ',[$toIndex, $status->id]);
                }
            });
        }
        $status = SystemTaskStatus::where('temp_id', $tempId)->get();
        return ['data' => $status];
    }
}
