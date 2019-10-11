<?php


namespace App\Format;


use App\Models\ProjectDoc;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class ProjectDocFormat extends TransformerAbstract
{

    protected $availableIncludes = ['creator', 'update'];

    public function transform(ProjectDoc $doc)
    {
        return [
            'id' => $doc->id,
            'project_id' => $doc->project_id,
            'is_home' => $doc->is_home,
            'file_name' => $doc->file_name,
            'doc_name' => $doc->doc_name,
            'version' => $doc->version,
            'created_at'=> $doc->created_at,
            'updated_at'=> $doc->updated_at
        ];
    }

    public function includeCreator(ProjectDoc $doc)
    {
        $user = User::where('id', $doc->creator_id)->first();
        if (empty($user)) {
            return null;
        }
        return $this->item($user, function (User $user) {
            return $this->userTransform($user);
        });
    }

    public function includeReporter(ProjectDoc $doc)
    {
        $user = User::where('id', $doc->update_id)->first();
        if (empty($user)) {
            return null;
        }
        return $this->item($user, function (User $user) {
            return $this->userTransform($user);
        });
    }

    private function userTransform(User $user)
    {

        $item = [
            'id' => $user->id,
            'name' => $user->nickname,
            'avatar' => $user->avatar,
        ];
        return $item;

    }
}
