<?php
// 
namespace App\Modules\User\Controllers;

// Base クラスを import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
// Core クラスを import
use App\Core\Request;
use App\Core\Response;
use App\Modules\User\Services\UserService;

// 継承
class UserController extends BaseController {
    protected UserService $userService;

    pablic function __construct(){
        $this -> userService = new UserService();
    }

    public function store(Request $request): Response {
        try{
            // varidate(): 入力検証
            $data = $this -> validate($request, [
                'email' => 'required|email',
                'name' => 'require|min:3',
                'password' => 'require|min:4'
            ]);

            $userId = $this -> userService -> createUser($data);

            return $this -> success(['id' => $userId], 'Created', 201);
        
        }catch(\Exception $e){
            return ErrorHandler::handle($e)
        }
    }
}