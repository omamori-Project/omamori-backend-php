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


    // 프레임 적용
    public function apply(Request $request): Response{
        try {
            $omamoriId = (int) $request -> param('omamoriId', 0);
            if ($omamoriId <= 0) {
                return $this -> error('Invalid omamoriId');
            }

            $data = $request -> all();
            $result = $this -> frameService -> applyFrame($omamoriId, $data);
            return $this -> success($result, 'OK', 200);

        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }


    // 프레임 적용 해제
    public function destroy(Request $request): Response{
        try {
            // Id 검증
            $omamoriId = (int) $request -> param('omamoriId', 0);
            if ($omamoriId <= 0) {
                return $this -> error('Invalid omamoriId');
            }
            $result = $this -> frameService -> removeFrame($omamoriId);
            return $this -> success($result, 'OK', 200);

        } catch (\Exception $e) {
            return ErrorHandler:: handle($e);
        }
    }

}