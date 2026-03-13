<?php

namespace App\Modules\System\Controllers;

// import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\System\Services\RenderService;


// 상속
class RenderController extends BaseController{
    protected RenderService $renderService;

    public function __construct(){
        $this -> renderService = new RenderService();
    }

    // 레이어 합성 → 이미지 생성
    public function create(Request $request): Response{
        try {
            $data = $request -> all();
            $result = $this -> renderService -> createRender($data);
            return $this->success($result, 'Created', 201);

        } catch (\InvalidArgumentException $e) {
            return $this -> error($e -> getMessage(), 400);
        } catch (\Exception $e) {
            return ErrorHandler:: handle($e);
        }
    }


    // 렌더 결과 조회
    public function show(Request $request): Response{
        try {
            $renderCode = $request -> param('renderCode');
            if (!$renderCode) {
                return $this -> error('renderCode is required.', 400);
            }

            $result = $this -> renderService -> showRender($renderCode);
            return $this->success($result, 'OK', 200);

        } catch (\InvalidArgumentException $e) {
            return $this -> notFound($e -> getMessage());
        } catch (\Exception $e) {
            return ErrorHandler:: handle($e);
        }
    }


    // 내 렌더 히스토리
    public function myHistory(Request $request): Response{
        try {
            $query = $request -> query();

            $page = isset($query['page']) ? (int)$query['page'] : 1;
            $perPage = isset($query['perPage']) ? (int)$query['perPage'] : 15;

            $result = $this -> renderService -> getMyRenders($page, $perPage);
            return $this -> success($result, 'OK', 200);

        } catch (\Exception $e) {
            return ErrorHandler:: handle($e);
        }
    }
}
