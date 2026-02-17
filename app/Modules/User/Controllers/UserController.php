<?php
// 
namespace App\Modules\User\Controllers;

// import
use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Modules\User\Services\UserService;
use App\Modules\Auth\Services\AuthService;

// 상속
class UserController extends BaseController {
    protected UserService $userService;

    public function __construct(){
        $this -> userService = new UserService();
    }

    // index(): 목록
    public function index(Request $request): Response{
        try {
            return $this->success([], 'Users retrieved successfully');
        } catch (\Exception $e) {
            return ErrorHandler:: handle($e);
        }
    }

    // 사용자 상세
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

    // 사용자 생성
    public function store(Request $request): Response {
        try{            
            // validate(): 입력 검증
            $data = $this -> validate($request, [
                'email' => 'required|email',
                'name' => 'required|min:3',
                'password' => 'required|min:4'
            ]);

            $userId = $this -> userService -> createUser($data);
            // success() 사용
            return $this -> success(['id' => $userId], 'Created', 201);
        
        }catch(\InvalidArgumentException $e){
            $msg = $e -> getMessage();
            $decoded = json_decode($msg, true);

            // validate() json 문자열
            if(is_array($decoded)){
                return $this -> error('Validation failed', 422, $decoded);
            }
            // 그냥 글자로
            return $this -> error($msg, 422);
        }
    }

    // 사용자 수정
    public function update(Request $request): Response{
        try{
            $id = (int) $request -> param('id');
            $user = $this -> userService -> getUserById($id);
            //  사용자 확인
            if(!$user){
                return $this -> notFound('User not found');
            }

            // 정보 받기
            $data = $this -> validate($request, [
                'name' => 'required|min:3'
            ]);

            $ok = $this -> userService -> updateUser($id, $data);

            // success() 사용
            return $this -> success(['id' => $id], 'Updated');

        }catch(\InvalidArgumentException $e){
            return $this -> error('Validation failed', 422, json_decode($e -> getMessage(), true));
        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }

    // 사용자 삭제
    public function destroy(Request $request): Response{
        try{
            $id = (int) $request -> param('id');
            $user = $this -> userService -> getUserById($id);
            // 사용자 확인
            if(!$user){
                // notfound() 사용
                return $this -> notFound('User not found');
            }

            $ok = $this -> userService -> deleteUser($id);
            if (!$ok){
                return $this -> error('Dalete failed', 500);
            }
            // success 사용
            return $this -> success(['id' => $id], 'Deleted');
        }catch(\Exception $e){
            return ErrorHandler:: handle($e);
        }
    }

    // 내 정보 수정: PATCH /api/me
    public function updateMe(Request $request): Response
    {
        try {
            $token = $request -> bearerToken();
            if (!$token) {
                return $this -> unauthorized('Token required');
            }

            $auth = new AuthService();
            $userId = $auth -> verifyAndGetUserId($token);
            // Json body
            $input = $request -> input();
            $data = [];

            if(array_key_exists('name', $input)){
                $data['name'] = $input['name'];
            }
            if(array_key_exists('email', $input)){
                $data['email'] = $input['email'];
            }
            if(array_key_exists('password', $input)){
                $data['password'] = $input['password'];
            }

            // 입력 확인
            if(empty($data)){
                return $this -> error('No data to update', 400);
            }

            // 성공 내용 출력
            $ok = $this -> userService -> updateUserMe($userId, $data);
            return $this -> success(['id' => $userId], 'Updated');


        } catch (\Exception $e) {
            return ErrorHandler:: handle($e);
        }
    }

    // 회원 탈퇴: DELETE /api/me
    public function deleteMe(Request $request): Response
    {
        try {
            $token = $request -> bearerToken();

            if (!$token) {
                return $this -> unauthorized('Token required');
            }

            $auth = new AuthService();
            $userId = $auth -> verifyAndGetUserId($token);
            $ok = $this -> userService -> deleteUser($userId);

            if (!$ok) {
                return $this -> error('Delete failed', 500);
            }
            return $this -> success(['id' => $userId], 'Deleted');

        } catch (\Exception $e) {
            return ErrorHandler:: handle($e);
        }
    }

}