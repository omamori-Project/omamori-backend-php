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

            $data = $request -> all();

            $result = $this -> shareService -> updateShare($shareId, $data);
            return $this -> success($result, 'Updated', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }
}