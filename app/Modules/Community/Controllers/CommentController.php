<?php

namespace App\Modules\Community\Controllers;

// import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\Community\Services\CommentService;

// 상속
class CommentController extends BaseController{
    protected CommentService $commentService;

    public function __construct()
    {
        $this -> commentService = new CommentService();
    }

    public function index(Request $request): Response{
        try{
            // postId 검증
            $postId = (int)$request -> param('postId', 0);
            if($postId <= 0){
                return $this -> error('Invalid postId');
            }

            $query = $request -> query();
            $result = $this -> commentService -> showByPost($postId, $query);
            return $this -> success($result, 'OK', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }


    // 댓글 작성 (이번 단계: parent_id는 null)
    public function create(Request $request): Response{
        try{
            // 토큰 검증
            $token = $request -> bearerToken();
            if(!$token){
                return $this -> unauthorized('Token required');
            }

            // 댓글 존재 확인
            $postId = (int)$request -> param('postId', 0);
            if($postId <= 0){
                return $this -> error('Invalid postId');
            }

            $input = $request -> input();
            $result = $this -> commentService -> createComment($token, $postId, $input);
            return $this -> success($result, 'Created', 201);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }
}