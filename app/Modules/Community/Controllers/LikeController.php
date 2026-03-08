<?php

namespace App\Modules\Community\Controllers;

// import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\Community\Services\LikeService;

// 상속
class LikeController extends BaseController{
    protected LikeService $likesService;

    public function __construct()
    {
        $this -> likesService = new LikeService();
    }

    // 좋아요 추가
    public function create(Request $request): Response{
        try{
            // post 검증
            $postId = (int)$request -> param('post', 0);
            if($postId <= 0){
                return $this -> error('Invalid post');
            }

            // Authorization 헤더에서 Bearer 토큰 추출
            $authHeader = $request -> header('Authorization');
            $token = str_replace('Bearer ', '', $authHeader);

            $result = $this -> likesService -> createLike($token, $postId);
            return $this -> success($result, 'Like created', 201);

        }catch(\RuntimeException $e){
            return $this -> error($e -> getMessage(), 409);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }
}