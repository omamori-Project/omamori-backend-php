<?php

namespace App\Modules\System\Controllers;

// import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\System\Services\FileService;


// 상속
class FileController extends BaseController{
    protected FileService $fileService;

    public function __construct(){
        $this -> fileService = new FileService();
    }

    
    // 파일 업로드 → url 반환
    public function upload(Request $request): Response{
        try {
            $file = $_FILES['file'] ?? null;
            if (!$file) {
                return $this -> error('File is required');
            }
            $result = $this -> fileService -> upload($file);
            return $this->success($result, 'OK', 201);

        } catch (\InvalidArgumentException $e) {
            return $this -> error($e -> getMessage(), 400);

        } catch (\Throwable $e) {
            return ErrorHandler:: handle($e);
        }
    }


    // 파일 삭제
    public function destroy(Request $request): Response{
        try {
            $fileName = $request -> param('fileName');
            if (!$fileName) {
                return $this -> error('fileName is required');
            }

            $this -> fileService -> delete($fileName);
            return $this->success(null, 'Deleted');

        } catch (\InvalidArgumentException $e) {
            return $this -> error($e -> getMessage(), 400);

        } catch (\Throwable $e) {
            return ErrorHandler:: handle($e);
        }
    }
}