<?php


namespace App\Http\Controllers\Projects;


use App\Format\ProjectDocFormat;
use App\Http\Controllers\ApiCtrl;
use App\Models\ProjectDoc;
use Illuminate\Support\Facades\Input;

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
    public function createProjectDocs($projectId)
    {
        $projectDocs = ProjectDoc::where('project_id', $projectId)
            ->orderBy('is_home','desc')
            ->orderBy('created_at', 'asc')
            ->get();
        return $this->toJsonArray($projectDocs);
    }
    public function getProjectDocContent($projectId, $fileId)
    {

    }

    public function updateProjectDocContent($projectId, $fileId)
    {
        $content = Input::get('content', null);
        $fileName= Input::get('file_name', null);
        $doc = ProjectDoc::where('id', $fileId)
            ->where('project_id', $projectId)
            ->first();
        if(empty($content)){
            $this->notFound404('content');
        }

        if(empty($doc)){
            $this->notFound404('doc not exist');
        }
        $docName = explode($fileName, 'name')[0];

        $doc->doc_name = $docName;
        $doc->file_name = $fileName;

        $count = ProjectDoc::where('project_id', $projectId)
            ->where('doc_name', $docName)
            ->where('file_name', $fileName)
            ->where('id', '!=', $fileId)->count();
        if($count >0){
            $this->dataAlreadyExist('file name already exist');
        }

        $projectDocs = ProjectDoc::where('project_id', $projectId)
            ->orderBy('is_home','desc')
            ->orderBy('created_at', 'asc')
            ->get();
        return $this->toJsonItem($projectDocs)->setMeta($projectDocs);
    }

    public function deleteProjectDocContent($projectId,$fileId)
    {
        ProjectDoc::where('id', $fileId)->where('project_id', $projectId)->delete();
        $projectDocs = ProjectDoc::where('project_id', $projectId)
            ->orderBy('is_home','desc')
            ->orderBy('created_at', 'asc')
            ->get();
        return $this->toJsonArray($projectDocs);
    }
}
