<?php

namespace App\Modules\Element\Controllers;

use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\Element\Services\ElementService;

class ElementController extends BaseController{
    protected ElementService $elementService;

    public function __construct()
    {
        $this -> elementService = new ElementService();
    }

    // 오마모리 요소 추가
    public function store(Request $request): Response{
        try{
            // 토큰 검증
            $token = $request -> bearerToken();
            if(!$token){
                return $this -> unauthorized('Token required');
            }

            // omamoriId 취득
            $omamoriId = (int)($request -> param('omamoriId') ?? 0);
            // ID가 0이하이라면 오류
            if($omamoriId <= 0){
                return $this -> error('Invalid omamoriId');
            }

            $input = $request -> input();

            $result = $this -> elementService -> createElement($token, $omamoriId, $input);
            return $this -> success($result, 'Created', 201);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }


    // 오마모리 요소 재정렬 (background 제외)
    public function reorder(Request $request): Response{
        try{
            // 토큰 검증
            $token = $request -> bearerToken();
            if(!$token){
                return $this -> unauthorized('Token required');
            }

            // route param
            $omamoriId = (int)$request -> param('omamoriId');
            if(!$omamoriId){
                return $this -> error('Invalid omamoriId');
            }

            // body
            $input = $request -> input();
            $result = $this -> elementService -> reorderElements($token, $omamoriId, $input);
            return $this -> success($result, 'OK', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }


    // 오마모리 수정
    public function updateElement(Request $request): Response{
        try{
            // 토큰 검증
            $token = $request -> bearerToken();
            if(!$token){
                return $this -> unauthorized('Token required');
            }

            // route param
            $omamoriId = (int)($request -> param('omamoriId') ?? 0);
            if($omamoriId <= 0){
                return $this -> error('Invalid omamoriId');
            }

            $elementId = (int)($request -> param('elementId') ?? 0);
            if(!$elementId <= 0){
                return $this -> error('Invalid elementId');
            }

            // body
            $input = $request -> input();

            $result = $this -> elementService -> updateElement($token, $omamoriId, $elementId, $input);
            return $this -> success($result, 'OK', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }        
    }
}