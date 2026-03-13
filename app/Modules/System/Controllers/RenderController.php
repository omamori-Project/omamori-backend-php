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
}