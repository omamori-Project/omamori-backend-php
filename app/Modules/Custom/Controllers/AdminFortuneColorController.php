<?php

namespace App\Modules\Custom\Controllers;

// import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\Custom\Services\AdminFortuneColorService;


// 속상
class AdminFortuneColorController extends BaseController{
    protected AdminFortuneColorService $adminFortuneColorService;

    public function __construct(){
        $this -> adminFortuneColorService = new AdminFortuneColorService();
    }


    // 목록(관리용)
    public function index(Request $request): Response{
        try{
            $query = $request -> query();
            $result = $this -> adminFortuneColorService -> index($query);
            return $this -> success($result, 'OK', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }


    // 생성(관리용)
    public function create(Request $request): Response{
        try{
            $result = $this -> adminFortuneColorService -> store();
            return $this -> success($result, 'Created', 201);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }
}