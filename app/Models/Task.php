<?php


namespace App\Models;

class Task extends TM
{
    protected $fillable=[
        'project_id',
        'sprint_id',
        'title',
        'desc',
        'type',
        'points',
        'exe_id',
        'report_id',
        'priority',
        'status'
    ];

    protected $hidden=[
        'updated_at',
        'created_at'
    ];

    public static function checkType($projectId,$type)
    {
        $count = ProjectTaskType::where('project_id', $projectId)->where('id', $type)->count();
        return $count > 0;
    }

    public static function checkPriority($projectId, $priority)
    {
        $count = ProjectTaskPriority::where('project_id', $projectId)->where('id', $priority)->count();
        return $count > 0;
    }

    public static function checkStatus($projectId, $status)
    {
        $count = ProjectTaskStatus::where('project_id', $projectId)->where('id', $status)->count();
        return $count > 0;
    }
}