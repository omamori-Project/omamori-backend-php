<?php

namespace App\Modules\Custom\Controllers;

// import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\Custom\Services\AdminFrameService;


// 상속
class AdminFrameController extends BaseController{
    protected AdminFrameService $adminFrameService;

    public function __construct(){
        $this -> adminFrameService = new AdminFrameService();
    }


    // 프레임 등록 (관리자)
    public function create(Request $request): Response{
        try {
            $data = $this -> validate($request, [
                'name' => 'required',
                'frameKey' => 'required',
                'previewUrl' => 'required',
                'assetUrl' => 'required',
            ]);

            $result = $this -> adminFrameService -> createFrame($data);
            return $this -> success($result, 'Created', 201);

        } catch (\Exception $e) {
            return ErrorHandler:: handle($e);
        }
    }


    // 프레임 수정 (관리자)
    public function update(Request $request): Response{
        try{
            // Id 검증
            $frameId = (int)$request -> param('frameId', 0);
            if($frameId <= 0){
                return $this -> error('Invalid frameId');
            }

            $data = $request -> all();
            $result = $this -> adminFrameService -> updateFrame($frameId, $data);
            return $this -> success($result, 'Updated', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }


    // 프레임 삭제 (관리자)
    public function destroy(Request $request): Response{
        try{
            // Id 검증
            $frameId = (int)$request -> param('frameId', 0);
            if($frameId <= 0){
                return $this -> error('Invalid frameId');
            }

            $this -> adminFrameService -> destroyFrame($frameId);
            return $this -> success(null, 'Deleted', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }


    // 프레임 목록 (관리자)
    public function index(Request $request): Response{
        try{
            $query = $request -> query();
            $result = $this -> adminFrameService -> indexFrames($query);

            return $this -> success($result, 'OK', 200);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }
}