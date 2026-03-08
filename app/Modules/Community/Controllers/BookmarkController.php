<?php

namespace App\Modules\Community\Controllers;

// import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\Community\Services\BookmarkService;


// 상속
class BookmarkController extends BaseController{
    protected BookmarkService $bookmarkService;

    public function __construct()
    {
        $this -> bookmarkService = new BookmarkService();
    }

    // 북마크 추가
    public function create(Request $request): Response{
        try{
            // 토큰 확인
            $token = $request -> bearerToken();
            if(!$token){
                return $this -> unauthorized('Token required');
            }

            // post 파라미터 확인
            $postId = (int)$request -> param('post', 0);
            if($postId <= 0){
                return $this -> error('Invalid postId');
            }

            $result = $this -> bookmarkService -> createBookmark($token, $postId);
            return $this -> success($result, 'Created', 201);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }


    // 북마크 취소
    public function destroy(Request $request): Response{
        try{
            // 토큰 확인
            $token = $request -> bearerToken();
            if(!$token){
                return $this -> unauthorized('Token required');
            }

            // postId 확인
            $postId = (int)$request -> param('post', 0);
            if($postId <= 0){
                return $this -> error('Invalid postId');
            }

            $this -> bookmarkService -> destroyBookmark($token, $postId);
            return $this -> success(null, 'Bookmark removed', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }


    // 내 북마크 목록 조회
    public function index(Request $request): Response{
        try{
            // 토큰 확인
            $token = $request -> bearerToken();
            if(!$token){
                return $this -> unauthorized('Token required');
            }

            $query = $request -> query();
            $result = $this -> bookmarkService -> getMyBookmarks($token, $query);
            return $this -> success($result, 'OK', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }
}