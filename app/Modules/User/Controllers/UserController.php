<?php
// 
namespace App\Modules\User\Controllers;

// import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\User\Services\UserService;

// 상속
class UserController extends BaseController {
    protected UserService $userService;

    public function __construct(){
        $this -> userService = new UserService();
    }

    // varidate(): 입력검증
    public function store(Request $request): Response {
        try{            
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

    public function show(Request $request): Response{
        try {
            $id = $request -> param('id');
            $user = $this -> userService -> getUserById($id);

            if (!$user) {
                // notFound() 사용
                return $this->notFound('User not found');
            }

            // success() 사용
            return $this -> success($user);

        } catch (\Exception $e) {
            return ErrorHandler:: handle($e);
        }
    }

}