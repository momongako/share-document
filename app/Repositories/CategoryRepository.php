<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CategoryRepository extends BaseRepository
{
    protected $model;

    public function model()
    {
        return Category::class;
    }

    public function getCategoryBySlug($slugCategory)
    {
        return $this->model->where('slug', $slugCategory)->first();
    }

    public function getListCategory()
    {
        return $this->model
            ->where('user-company', isset(Auth::user()->company) ? Auth::user()->company : NULL)
            ->orderBy('id', 'DESC')
            ->get();
    }
}
