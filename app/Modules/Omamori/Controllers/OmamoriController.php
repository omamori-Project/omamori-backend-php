<?php

namespace App\Modules\Omamori\Controllers;

// import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\Omamori\Services\OmamoriServise;

// 상속
class OmamoriController extends BaseController{
    protected OmamoriServise $omamoriService;

    public function __construct(){
        $this -> omamoriService = new OmamoriServise();
    }

    public function store(Request $request): Response{
        try{
            $token = $request -> bearerToken();
            if(!$token){
                return $this -> unauthorized('Token required');
            }
            
            // Json body
            $input = $request -> input();
            $result = $this -> omamoriService -> createOmamori($token, $input);

            // 201 Created
            return $this -> success($result, 'Created', 201);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }
}