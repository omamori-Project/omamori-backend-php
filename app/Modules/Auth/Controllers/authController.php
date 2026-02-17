<?php

namespace App\Modules\Auth\Controllers;

// import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\Auth\Services\AuthService;
use App\Modules\User\Services\UserService;

// 상속
class AuthController extends BaseController{
    private UserService $userService;
    private AuthService $authService;

    public function __construct(){
        $this -> userService = new UserService();
        $this -> authService = new AuthService();
    }

    // 회원가입
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
            $decoded = json_decode($e -> getMessage(), true);

            if(is_array($decoded)){
                return $this -> error('validation failed', 422, true);
            }
            return $this -> error($e -> getMessage(), 422);
        
        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }

    // 로그인
    public function login(Request $request): Response{
        try{
            $data = $this -> validate($request, [
                'email' => 'required|email',
                'password' => 'required|min:4'
            ]);

            $user = $this -> userService -> getUserByEmail($data['email']);

            if(!$user){
                return $this -> unauthorized('Invalid credentials');
            }

            if(!password_verify($data['password'], $user['password'])){
                return $this -> unauthorized('Invalid credentials');
            }

            $token = $this -> authService -> generateToken((int)$user['id']);
            return $this -> success([
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => (int)($_ENV['TOKEN_TTL'] ?? 3600),
                ], 'OK');

        }catch(\InvalidArgumentException $e){
            $decoded = json_decode($e -> getMessage(), true);
            if(is_array($decoded)){
                return $this -> error('Validation failed', 422, $decoded);
            }
            return $this -> error($e -> getMessage(), 422);

        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }

    // 로그아웃
    public function logout(Request $request): Response{
        try{
            return $this -> success(null, 'Logout');
        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }
}