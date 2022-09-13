<?php

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\BaseRepository;
use App\Services\Traits\WildCardSQLTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PostRepository extends BaseRepository
{
    use WildCardSQLTrait;

    protected $model;

    public function model()
    {
        return Post::class;
    }

    public function getListPostPinned()
    {
        return $this->model
            ->where('is_pin', PIN)
            ->where('status', Post::ACTIVE)
            ->where('type', POST_NORMAL)
            ->with('category.parents', 'user', 'comment.user', 'likePost')
            ->orderBy('created_at', 'DESC')
            ->limit(LIMIT_PINNED)
            ->get();
    }

    public function getListNewDaily()
    {
        return $this->model
            ->where('is_pin', NOT_PIN)
            ->where('status', Post::ACTIVE)
            ->where('type', POST_NORMAL)
            ->with(['category.parents', 'user', 'comment.user'])
            ->orderBy('created_at', 'DESC')
            ->limit(LIMIT_POST_NORMAL)
            ->get();
    }

    public function getListPostEvent()
    {
        return $this->model
            ->where('is_pin', NOT_PIN)
            ->where('status', Post::ACTIVE)
            ->whereIn('type', [POST_BIRTH_DAY, POST_ONBOARD, POST_MARRIAGE])
            ->with(['category.parents', 'user', 'comment.user'])
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function getListPostCategory($categoryId, $search, $filterLastPost, $filterMostView)
    {
        $isAdmin = Auth::user()->is_admin;
        $query = $this->model
            ->where('category_id', $categoryId)
            ->where('status', ACTIVE)
            ->orWhere(function ($subQuery) use ($isAdmin) {
                $subQuery->where('status', DEACTIVE);

                if ($isAdmin != ADMIN) {
                    $subQuery->where('user_id', Auth::user()->id);
                }
            })
            ->with('user', 'comment.user', 'likePost', 'file')
            ->orderBy('is_pin', 'ASC');

        if ($search) {
            $query->where('title', 'like', '%' . $this->escapeLike($search) . '%');
        }

        if ($filterMostView === FILTER_ASC) {
            $query->orderBy('view', FILTER_ASC);
        } elseif($filterMostView === FILTER_DESC) {
            $query->orderBy('view', FILTER_DESC);
        }

        if ($filterLastPost === FILTER_ASC) {
            $query->orderBy('created_at', FILTER_ASC);
        } elseif(!$filterMostView) {
            $query->orderBy('created_at', FILTER_DESC);
        }

        return $query->get();
    }

    public function firstPostCategory($categoryId, $search, $filterLastPost, $filterMostView)
    {
        $query = $this->model
            ->where('category_id', $categoryId)
            ->with('user', 'comment.user', 'likePost', 'file')
            ->orderBy('is_pin', 'ASC');

        if ($search) {
            $query->where('title', 'like', '%' . $this->escapeLike($search) . '%');
        }

        if ($filterMostView === FILTER_ASC) {
            $query->orderBy('view', FILTER_ASC);
        } elseif($filterMostView === FILTER_DESC) {
            $query->orderBy('view', FILTER_DESC);
        }

        if ($filterLastPost === FILTER_ASC) {
            $query->orderBy('created_at', FILTER_ASC);
        } elseif(!$filterMostView) {
            $query->orderBy('created_at', FILTER_DESC);
        }

        return $query->first();
    }

    public function getPostById($postId)
    {
        return $this->model
            ->where('id', $postId)
            ->with('user', 'comment.user', 'likePost', 'file')
            ->first();
    }

    public function getListPost()
    {
        return $this->model
            ->orderBy('id', 'DESC')
            ->get();
    }

    public  function getListPostByCategoryId($categoryId)
    {
        return $this->model
            ->where('category_id', $categoryId)
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function getPostEvenPaginate()
    {
        return $this->model
            ->whereIn('type', [POST_BIRTH_DAY, POST_ONBOARD, POST_MARRIAGE])
            ->with('user', 'comment.user', 'likePost', 'file')
            ->orderBy('created_at', 'DESC')
            ->paginate(LIMIT_PINNED);
    }

    public function incrementViewPost($postId)
    {
        DB::table('posts')->where('id', $postId)->increment('view');
    }
}
