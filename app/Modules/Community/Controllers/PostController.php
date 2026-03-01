<?php

namespace App\Modules\Community\Controllers;

// import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\Community\Services\PostService;

// 상속
class PostController extends BaseController{
    public PostService $postService;

    public function __construct()
    {
        $this -> postService = new PostService();
    }


    // 게시글 작성
    public function store(Request $request): Response{
        try{
            // 토큰 겁증
            $token = $request -> bearerToken();
            if(!$token){
                return $this -> unauthorized('Token required');
            }

            // Json body
            $input = $request -> input();
            $result = $this -> postService -> createPost($token, $input);

            // 201 Created
            return $this -> success($result, 'Created', 201);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }


    // 전체 게시글 목록 조회 (공개 피드)
    public function index(Request $request): Response{
        try{
            $query = $_GET;
            $result = $this -> postService -> index($query);
            return $this -> success($result, 'OK', 200);
        }catch(\Exception $e){
            return ErrorHandler:: handle($e);

        }
    }


    // 상세 조회
    public function show(Request $request): Response{
        try{
            //  토큰 겁증
            $token = $request -> bearerToken();
            if(!$token){
                return $this -> unauthorized('Token required');
            }

            $postId = (int)$request -> param('postId', 0);
            if(!$postId){
                return $this -> error('Invalid postId');
            }

            $result = $this -> postService -> showPublishedPost($token, $postId);
            return $this -> success($result, 'OK', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }


    // 특정 유저 게시글 목록 조회
    public function indexByUser(Request $request): Response{
        try{
            // 토큰 겁증
            $userId = (int)$request -> param('userId', 0);
            if(!$userId){
                return $this -> error('Invalid userId');
            }

            $query = $_GET;
            $result = $this -> postService -> indexByUser($userId, $query);
            return $this -> success($result, 'OK', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }
}