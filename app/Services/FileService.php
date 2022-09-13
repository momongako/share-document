<?php

namespace App\Services;

use App\Repositories\FileRepository;
use App\Services\Traits\WildCardSQLTrait;

/**
 * Class UserService
 *
 * @package App\Services
 */
class FileService extends BaseService
{
    use WildCardSQLTrait;

    /**
     * @var FileRepository
     */
    protected $mainRepository;

    /**
     * OptionService constructor.
     *
     * @param FileRepository $fileRepository
     */
    public function __construct(FileRepository $fileRepository)
    {
        $this->mainRepository = $fileRepository;
    }

    public function deleteFileAttach($postId)
    {
        return $this->mainRepository->deleteFileAttach($postId);
    }

    public function deleteFileAttachById($listFileId)
    {
        return $this->mainRepository->deleteFileAttachById($listFileId);
    }

    public function getFileAttachById($listFileId)
    {
        return $this->mainRepository->getFileAttachById($listFileId);
    }
}

