<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\PostRepository;
use App\Services\Traits\ImageManagerTrait;
use App\Services\Traits\WildCardSQLTrait;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserService
 *
 * @package App\Services
 */
class PostService extends BaseService
{
    use WildCardSQLTrait;
    use ImageManagerTrait;

    /**
     * @var PostRepository
     */
    protected $mainRepository;

    /**
     * OptionService constructor.
     *
     * @param PostRepository $postRepository
     */
    public function __construct(PostRepository $postRepository)
    {
        $this->mainRepository = $postRepository;
    }

    public function getAllPostPinned()
    {
        return $this->mainRepository->getListPostPinned();
    }

    public function getPostById($postId)
    {
        return $this->mainRepository->getPostById($postId);
    }

    public function getListPostEvent()
    {
        return $this->mainRepository->getPostEvenPaginate();
    }

     public function getListPost()
     {
        return $this->mainRepository->getListPost();
     }

     public function getListPostByCategoryId($categoryId){
         return $this->mainRepository->getListPostByCategoryId($categoryId);
     }

    public function getAttributePost($request)
    {
        $attributesPost = [
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id,
            'user_id' => isset(Auth::user()->id) ? Auth::user()->id : '',
            'user_name' => isset(Auth::user()->name) ? Auth::user()->name : '',
            'user_company' => isset(Auth::user()->company) ? Auth::user()->company : ''
        ];

        return $attributesPost;
    }

    public function uploadImagePost($file)
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = $this->uploadFileName(empty($extension) ? 'png' : $extension);
        $folderName = PATH_UPLOAD_FILE . Auth::user()->id;
        $this->uploadFile($folderName, $file, $fileName);

        return "posts" . "/" . Auth::user()->id . "/" . $fileName;
    }

    public function uploadAttachmentPost($listFileUpload, $postId)
    {
        $listFile = [];
        $folderName = PATH_UPLOAD_FILE . Auth::user()->id . "/attachment/" . $postId;
        foreach ($listFileUpload as $file) {
            $fileName = $file->getClientOriginalName();
            $this->uploadFile($folderName, $file, $fileName);
            $path = "posts/" . Auth::user()->id . "/attachment/" . $postId . "/" . $fileName;
            $listFile[] = [
                'name' => $fileName,
                'path' => $path,
                'post_id' => $postId,
            ];
        }

        return $listFile;
    }

    public function incrementViewPost($postId)
    {
        $this->mainRepository->incrementViewPost($postId);
    }
}

