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

    public function __construct(){
        $this -> userService = new UserService();
    }

    public function store(Request $request): Response {
        try{
            // varidate(): 入力検証
            $data = $this -> validate($request, [
                'email' => 'required|email',
                'name' => 'required|min:3',
                'password' => 'required|min:4'
            ]);

            $userId = $this -> userService -> createUser($data);

            return $this -> success(['id' => $userId], 'Created', 201);
        
        }catch(\Exception $e){
            return ErrorHandler::handle($e);
        }
    }

    public function index(Request $request): Response{
        try {
            return $this->success([], 'Users retrieved successfully');
        } catch (\Exception $e) {
            return ErrorHandler:: handle($e);
        }
    }

}