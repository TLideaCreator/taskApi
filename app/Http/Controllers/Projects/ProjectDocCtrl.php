<?php


namespace App\Http\Controllers\Projects;


use App\Format\ProjectDocFormat;
use App\Http\Controllers\ApiCtrl;
use App\Models\ProjectDoc;

class ProjectDocCtrl extends ApiCtrl
{


    /**
     * ProjectDocCtrl constructor.
     */
    public function __construct()
    {
        $this->formatTransfer = new ProjectDocFormat();
    }

    public function getProjectDocsCatalog($projectId)
    {
        $projectDocs = ProjectDoc::where('project_id', $projectId)
            ->orderBy('is_home','desc')
            ->orderBy('created_at', 'asc')
            ->get();
        return $this->toJsonArray($projectDocs);
    }
}
