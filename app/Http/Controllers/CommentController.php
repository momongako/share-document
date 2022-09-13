<?php

namespace App\Http\Controllers;

use App\Enums\StatusCode;
use App\Http\Request\CommentRequest;
use App\Services\CommentService;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    protected $commentService;
    protected $postService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CommentService $commentService, PostService $postService)
    {
        $this->commentService = $commentService;
        $this->postService = $postService;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'comment' => 'required',
            ],
            [
                'comment.required' => 'The comment field is required',
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors(), StatusCode::BAD_REQUEST);
        }

        try {
            $postId = $request->post_id;
            $content = $request->comment;
            $userId = Auth::user()->id;
            $userName = Auth::user()->name;
            $attributes = [
                'post_id' => $postId,
                'user_id' => $userId,
                'content' => $content,
                'user_name' => $userName
            ];
            $comment = $this->commentService->create($attributes);
            $userComment = $this->commentService->getUserComment($comment->id);
            $post = $this->postService->getPostById($postId);

            return response()->json(['result' => true, 'comment' => $userComment, 'infoPost' => $post], StatusCode::OK);
        } catch (\Exception $e) {
            Log::info($e);

            return response()->json([
                'message' => StatusCode::INTERNAL_ERR,
            ], StatusCode::INTERNAL_ERR);
        }
    }

    public function edit(CommentRequest $request)
    {
        try {
            $contentComment = $request->contentComment;
            $attributes['content'] = $contentComment;
            $commentId = $request->commentId ?? '';
            $comment = $this->commentService->update($commentId, $attributes);

            return response()->json(['result' => true], StatusCode::OK);
        } catch (\Exception $e) {
            Log::info($e);
            return response()->json([
                'message' => StatusCode::INTERNAL_ERR,
            ], StatusCode::INTERNAL_ERR);
        }
    }

    public function delete(Request $request)
    {
        try {
            $commentId = $request->commentId ?? '';
            $comment = $this->commentService->destroy($commentId);

            return response()->json(['result' => true], StatusCode::OK);
        } catch (\Exception $e) {
            Log::info($e);
            return response()->json([
                'message' => StatusCode::INTERNAL_ERR,
            ], StatusCode::INTERNAL_ERR);
        }
    }
}
