<?php

namespace App\Modules\System\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\System\Repositories\FileRepository;


// 상속
class FileService extends BaseService{
    protected FileRepository $fileRepository;

    public function __construct(){
        $db = new Database();
        $this -> fileRepository = new FileRepository($db);
    }


    // 파일 업로드 → url 반환
    public function upload(array $file): array{
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \InvalidArgumentException('File upload failed: ' . $file['error']);
        }

        $uploadDir = __DIR__ . '/../../../../public/uploads/';
        if(!is_dir($uploadDir)){
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . '_' . $file['name'];
        $destination = $uploadDir . $fileName;
        if(!move_uploaded_file($file['tmp_name'], $destination)){
            throw new \RuntimeException('Failed to save file');
        }

        $url = '/uploads/' . $fileName;
        return ['url' => $url];
    }


    // 파일 삭제
    public function delete(string $fileName): void{
        if (!$fileName) {
            throw new \InvalidArgumentException('fileName is required');
        }

        $filePath = __DIR__ . '/../../../../public/uploads/' . $fileName;

        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException('File not found');
        }

        if (!unlink($filePath)) {
            throw new \RuntimeException('Failed to delete file');
        }
    }
}