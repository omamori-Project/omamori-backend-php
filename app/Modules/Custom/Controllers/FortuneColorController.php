<?php

namespace App\Modules\Custom\Controllers;

// import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\Custom\Services\FortuneColorService;
use App\Modules\User\Services\UserService;


// 상속
class FortuneColorController extends BaseController{
    protected FortuneColorService $fortuneColorService;
    protected UserService $userService;

    public function __construct()
    {
        $this -> fortuneColorService = new FortuneColorService();
        $this -> userService = new UserService();
    }

    // 생년월일 기반 1회성 결과
    public function today(Request $request): Response{
        try{
            $input = $request -> all();
            $result = $this -> fortuneColorService -> getTodayResult($input);
            return $this -> success($result, 'OK', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }

    // 행운컬러 목록
    public function index(Request $request): Response{
        try{
            $page = (int)$request -> query('page', 1);
            $size = (int)$request -> query('size', 10);
            $sort = $request -> query('sort', 'latest');
            $category = $request -> query('category');
            $active = filter_var($request -> query('active', true), FILTER_VALIDATE_BOOLEAN);

            $result = $this -> fortuneColorService -> getList($page, $size, $sort, $category, $active);
            return $this -> success($result, 'OK', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }


    // 행운컬러 단건 조회
    public function show(Request $request): Response{
        try{
            $fortuneColorId = (int)$request -> param('fortuneColorId', 0);

            $result = $this -> fortuneColorService -> getById($fortuneColorId);
            return $this->success($result, 'OK', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }


    // 내 테마 적용/변경
    public function updateTheme(Request $request): Response{
        try{
            // 토큰 겁증
            $token = $request -> bearerToken();
            if(!$token){
                return $this -> unauthorized('Token required');
            }

            $result = $this -> userService -> updateTheme($token, $request -> all());
            return $this -> success($result, 'OK', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }
}