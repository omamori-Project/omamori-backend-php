<?php

namespace App\Modules\Custom\Controllers;

// import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\Custom\Services\FortuneColorService;


// 상속
class FortuneColorController extends BaseController{
    protected FortuneColorService $fortuneColorService;

    public function __construct()
    {
        $this -> fortuneColorService = new FortuneColorService();
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
}