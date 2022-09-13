<?php

namespace App\Http\Controllers;

use App\Enums\StatusCode;
use App\Http\Request\CategoryRequest;
use App\Models\Post;
use App\Services\CategoryService;
use App\Services\CommentService;
use App\Services\FileService;
use App\Services\PostService;
use App\Services\Traits\ImageManagerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    use ImageManagerTrait;

    protected $postService;
    protected $fileService;
    protected $categoryService;
    protected $commentService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PostService $postService, FileService $fileService, CategoryService $categoryService, CommentService $commentService)
    {
        $this->postService = $postService;
        $this->fileService = $fileService;
        $this->categoryService = $categoryService;
        $this->commentService = $commentService;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create(Request $request)
    {
        //$this->authorize('create', Post::class);
        $listFile = [];

        $this->validate($request, [
            'title'   => 'required|min:5|max:255',
            'content'   => 'required',
            'category_id'   => 'required',
        ]);

        $attributesPost = $this->postService->getAttributePost($request);
        $post = $this->postService->create($attributesPost);

        if ($request->file('filesAttach')) {
            $listFileUpload = $request->file('filesAttach');
            $listFile = $this->postService->uploadAttachmentPost($listFileUpload, $post->id);
            $this->fileService->inserts($listFile);
        }

        return response()->json(['result' => true], StatusCode::OK);
    }

    public function getListPost()
    {
        $listPost =  $this->postService->getListPost();
        return response([
            'listpost' => $listPost
        ], StatusCode::OK);

    }
    public function getListPostByCategoryId(Request $request)
    {
        $listPost =  $this->postService->getListPostByCategoryId($request->id);
        return response([
            'listpost' => $listPost
        ], StatusCode::OK);

    }

    public function edit(Request $request)
    {
        $this->authorize('update', $this->postService->find($request->id));
        $listFile = [];
        $category = $this->categoryService->find($request->categoryId);
        $post = $this->postService->find($request->id);
        $attributesPost = $this->postService->getAttributePost($request);

        if ($category->slug != LIFE_LDCV_MENU) {
            $totalPostPinned = $this->postService->getAllPostPinned()->count();

            if ($totalPostPinned >= LIMIT_PINNED && $request->is_pin == Post::PIN && $post->is_pin != Post::PIN) {
                return response()->json(['errorPin' => true], StatusCode::BAD_REQUEST);
            }
        }  else {
            $attributesPost['type'] = $request->type;
            $attributesPost['is_pin'] = Post::NOT_PIN;
        }

        if ($request->file('image')) {

            if ($post->image) {
                $this->deleteFile($post->image);
            }

            $file = $request->file('image');
            $attributesPost['image'] = $this->postService->uploadImagePost($file);
        }elseif ($request->removeImageUpload == REMOVE_IMAGE) {
            if ($post->image) {
                $this->deleteFile($post->image);
                $attributesPost['image'] = '';
            }
        }

        $post = $this->postService->update($request->id, $attributesPost);

        if ($request->file('filesAttach')) {

            if (!($post->file->isEmpty()) && $request->removeFile == REMOVE_IMAGE) {
                $this->fileService->deleteFileAttach($post->id);
                $this->deleteFolder(PATH_UPLOAD_FILE . Auth::user()->id . "/attachment/" . $post->id);
            }

            $listFileUpload = $request->file('filesAttach');
            $listFile = $this->postService->uploadAttachmentPost($listFileUpload, $post->id);
            $this->fileService->inserts($listFile);
        } elseif ($request->removeFile == REMOVE_IMAGE && !empty($request->listFileRemove)) {
            $arrayFileIdRemove = array_map('intval', explode(',', $request->listFileRemove));
            $listFileRemove = $this->fileService->getFileAttachById($arrayFileIdRemove);
            foreach ($listFileRemove as $file) {
                $this->deleteFile($file->path);
            }

            $this->fileService->deleteFileAttachById($arrayFileIdRemove);
        }

        return response()->json(['result' => true], StatusCode::OK);
    }

    public function getListPostEvent()
    {
        $data = $this->postService->getListPostEvent();

        return response()->json($data);
    }

    public function delete(Request $request)
    {
        $postId = $request->postId;
        $this->authorize('delete', $this->postService->find($postId));
        DB::beginTransaction();
        try {
            $this->postService->destroy($postId);
            $this->fileService->deleteFileAttach($postId);
            $this->commentService->deleteCommentWithPost($postId);
            DB::commit();

            return response()->json(['result' => true], StatusCode::OK);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            DB::rollBack();

            return  response()->json(['error' => true], StatusCode::INTERNAL_ERR);
        }
    }

    public function incrementViewPost(Request $request)
    {
        $postId = $request->postId ?? '';
        $this->postService->incrementViewPost($postId);
        $detailPostCategory = $this->postService->getPostById($postId);

        if (!$detailPostCategory) {
            return response()->json(['error' => true], StatusCode::NOT_FOUND);
        }

        return response()->json(['result' => true, 'detailPostCategory' => $detailPostCategory], StatusCode::OK);
    }

    public function downloadFile(Request $request)
    {
        return $this->downloadFileAttach($request->file);
    }
}
