<?php

namespace App\Modules\Public\Controllers;

// import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\Public\Services\ShareService;


// 상속
class ShareController extends BaseController{
    protected ShareService $shareService;

    public function __construct(){
        $this -> shareService = new ShareService();
    }

    // 외부 공유용 오마모리 조회
    public function show(Request $request): Response{
        try {
            $token = (string) $request -> param('token', '');

            if ($token === '') {
                return $this -> error('Invalid token');
            }

            $result = $this -> shareService -> showByToken($token);
            return $this -> success($result, 'OK', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }


    // 공유 설정 수정
    public function update(Request $request): Response{
        try {
            $shareId = (int)$request -> param('shareId', 0);
            if ($shareId <= 0) {
                return $this -> error('Invalid shareId');
            }

            $result = $this -> shareService -> updateShare($shareId);
            return $this -> success($result, 'Updated', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }


    // 미리보기 카드
    public function preview(Request $request): Response{
        try {
            $token = (string)$request -> param('token', '');
            if ($token === '') {
                return $this -> error('Invalid token');
            }

            $result = $this -> shareService-> preview($token);
            return $this -> success($result, 'OK', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }


    // 공유 링크 생성
    public function create(Request $request): Response{
        try {
            // 오마모리 존재 확인
            $omamoriId = (int)$request -> param('omamoriId', 0);
            if ($omamoriId <= 0) {
                return $this -> error('Invalid omamoriId');
            }

            // body 내용 받기
            $data = $this -> validate($request, [
                'option' => 'required',
                'expires_at' => 'required',
            ]);

            $result = $this -> shareService -> createShare($omamoriId, $data);
            return $this -> success($result, 'Created', 201);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }


    // 내보내기(다운로드 URL 반환)
    public function download(Request $request): Response{
        try{
            // 오마모리 존재 확인
            $omamoriId = (int)$request -> param('omamoriId');

            $data = $this -> validate($request, ['dpi' => 'numeric']);

            $result = $this->shareService->exportOmamori($omamoriId, $request -> all());
            return $this -> success($result, 'OK', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }
}