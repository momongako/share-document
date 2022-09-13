<?php

namespace App\Repositories;

use App\Models\File;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;

class FileRepository extends BaseRepository
{
    protected $model;

    public function model()
    {
        return File::class;
    }

    public function deleteFileAttach($postId)
    {
        $this->model->where('post_id', $postId)->delete();
    }

    public function deleteFileAttachById($listFileId)
    {
        $this->model->whereIn('id', $listFileId)->delete();
    }

    public function getFileAttachById($listFileId)
    {
       return $this->model->whereIn('id', $listFileId)->get();
    }
}
