<?php

namespace App\Modules\Custom\Controllers;

// import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\Custom\Services\AdminFrameService;


// 상속
class AdminFrameController extends BaseController{
    protected AdminFrameService $adminFrameService;

    public function __construct(){
        $this -> adminFrameService = new AdminFrameService();
    }


    // 프레임 등록 (관리자)
    public function create(Request $request): Response{
        try {
            $data = $this -> validate($request, [
                'name' => 'required',
                'frameKey' => 'required',
                'previewUrl' => 'required',
                'assetUrl' => 'required',
            ]);

            $result = $this -> adminFrameService -> createFrame($data);
            return $this -> success($result, 'Created', 201);

        } catch (\Exception $e) {
            return ErrorHandler:: handle($e);
        }
    }
}