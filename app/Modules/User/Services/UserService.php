<?php

namespace App\Modules\User\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\User\Repositories\UserRepository;

// 상속
class UserService extends BaseService{
    protected UserRepository $userRepository;

    public function __construct(){
        $db = new Database();
        $this -> userRepository = new UserRepository($db);
    }

    // 회원가입
    public function createUser(array $data): string{
        // BaseService의 validateRequired() 메소드 사용
        $this -> validateRequired($data, ['email', 'password', 'name']);

        // 이메일 중북 확인
        if($this -> userRepository -> emailExists($data['email'])){
            throw new \InvalidArgumentException('Email already exists');
        }

        // password -> hash화
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        // 현재 시간 기록
        $data['created_at'] = $this -> now();
        $data['updated_at'] = $this -> now();

        return $this -> userRepository -> create($data);
    }

    // 업데이트
    public function updateUser(int $id, array $data): bool{
        // created_at 이외 사용 
        $filtered = $this -> except($data, ['created_at', 'id']);
        
        // password 오면 hash화
        if (isset($filtered['password']) && $filtered['password'] !== ''){
            $filtered['password'] = password_hash($filtered['password'], PASSWORD_BCRYPT);
        }else{
            unset($filtered['password']);
        }

        // 현재 시간 기록
        $filtered['updated_at'] = $this -> now();
        return $this -> userRepository -> update($id, $filtered);
    }

    public function getUserById(int $id): ?array{
        return $this -> userRepository -> findById($id);
    }

    // 삭재
    public function deleteUser(int $id): bool{
        return $this -> userRepository -> delete($id);
    }

    // me 대응
    public function updateUserMe(int $id, array $data): bool
    {
        // 변경 가는 필드만 허락
        $filtered = $this -> only($data, ['name', 'email', 'password']);

        if (empty($filtered)) {
            throw new \InvalidArgumentException('No fields to update');
        }

        // 필요하면 규칙 검증（선택）
        $rules = [];
        if (isset($filtered['name'])) $rules['name'] = 'required|min:3';
        if (isset($filtered['email'])) $rules['email'] = 'required|email';
        if (isset($filtered['password'])) $rules['password'] = 'required|min:4';

        // BaseService validate는 required/min/email만 보면 됨
        $this -> validate($filtered, $rules);

        // password 오면 hash
        if (isset($filtered['password'])) {
            $filtered['password'] = password_hash($filtered['password'], PASSWORD_BCRYPT);
        }

        $filtered['updated_at'] = $this -> now();
        return $this -> userRepository -> update($id, $filtered);
    }

    // 
    public function getUserByEmail(string $email): ?array{
        return $this -> userRepository -> findOneBy(['email' => $email]);
    }
}