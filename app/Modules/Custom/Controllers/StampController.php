<?php

namespace App\Modules\Custom\Controllers;

// import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\Custom\Services\StampService;


// 상속
class StampController extends BaseController{
    protected StampService $stampService;

    public function __construct(){
        $this -> stampService = new StampService();
    }

    public function index(Request $request): Response{
        try {
            $result = $this -> stampService -> getStampList($request -> query());
            return $this -> success($result, 'OK', 200);

        } catch (\Exception $e) {
            return ErrorHandler:: handle($e);
        }
    }
}