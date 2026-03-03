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
        $this -> commentService = new CommentService;
    }

    public function show(Request $request): Response{
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
}