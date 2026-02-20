<?php

namespace App\Modules\Omamori\Controllers;

// import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\Omamori\Services\OmamoriService;

// 상속
class OmamoriController extends BaseController{
    protected OmamoriService $omamoriService;

    public function __construct(){
        $this -> omamoriService = new OmamoriService();
    }

    public function store(Request $request): Response{
        try{
            $token = $request -> bearerToken();
            if(!$token){
                return $this -> unauthorized('Token required');
            }
            
            // Json body
            $input = $request -> input();
            $result = $this -> omamoriService -> createOmamori($token, $input);

            // 201 Created
            return $this -> success($result, 'Created', 201);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }

    // 오마모리 복제
    public function duplicate(Request $request): Response{
        try{
            $token = $request -> bearerToken();
            if (!$token){
                return $this -> unauthorized('Token required');
            }

            $omamoriId = (int)$request -> param('omamoriId', 0);
            if ($omamoriId <= 0){
                return $this -> error('Invalid omamoriId');
            }

            $result = $this -> omamoriService -> duplicateOmamori($token, $omamoriId);
            return $this -> success($result, 'Duplicated', 201);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }

    // 오마모리 조회(편집/확인)
    public function show(Request $request): Response{
        try{
            $token = $request -> bearerToken();
            if(!$token){
                return $this -> unauthorized('Token required');
            }

            $omamoriId = (int)$request -> param('omamoriId', 0);
            if($omamoriId <= 0){
                return $this -> error('Invalid omamoriId');
            }

            $result = $this -> omamoriService -> getOwnOmamoriById($token, $omamoriId);
            return $this -> success($result, 'OK', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }

    // 오마모리 내 목록
    public function index(Request $request): Response{
        try{
            $token = $request -> bearerToken();
            if(!$token){
                return $this -> unauthorized('Token required');
            }

            $page = (int)$request -> query('page', 1);
            $size = (int)$request -> query('size', 10);
            // null or string
            $status = $request -> query('status');
            $sort = $request -> query('sort', 'latest');

            $result = $this -> omamoriService -> getList($token, $page, $size, $status, $sort);
            return $this -> success($result, 'OK', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }

    // 오마모리 공개
    public function publish(Request $request): Response{
        try{
            $token = $request -> bearerToken();
            if (!$token){
                return $this -> unauthorized('Token required');
            }

            $omamoriId = (int)($request -> param('omamoriId') ?? 0);
            if($omamoriId <= 0){
                return $this -> error('Invallid omamoriId');
            }
            return $this -> success(['omamoriId' => $omamoriId], 'Publish endpoint ready', 200);
            
        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }
}