<?php

namespace App\Module\Auth\Controllers;

// import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\User\Services\UserService;

// 상속
class AuthController extends BaseController{
    private UserService $userService;

    public function __construct(){
        $this -> userService = new UserService();
    }

    public function register(Request $request): Response{
        try{
            $data = $this -> validate($request, [
                'email' => 'required|email',
                'name' => 'required|min:3',
                'password' => 'required|min:4'
            ]);

            $userId = $this -> userService -> createUser($data);
            return $this -> success(['id' => $userId], 'Created', 201);
        }catch(\InvalidArgumentException $e){
            $msg = $e -> getMessage();
            $decoded = json_decode($msg, true);

            if(is_array($decoded)){
                return $this -> error('Validation failed', 422, $decoded);
            }
            return $this -> error($msg, 422);
        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }
}