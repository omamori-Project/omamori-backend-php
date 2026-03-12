<?php

namespace App\Modules\Custom\Controllers;

// import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\Custom\Services\FrameService;


// 상속
class FrameController extends BaseController{
    protected FrameService $frameService;

    public function __construct()
    {
        $this -> frameService = new FrameService();
    }

    // 유저용 프레임 목록
    public function index(Request $request): Response{
        try {
            $query = $request -> query();
            $result = $this -> frameService -> getFrames($query);
            return $this->success($result, 'OK', 200);

        } catch (\Exception $e) {
            return ErrorHandler:: handle($e);
        }
    }
}