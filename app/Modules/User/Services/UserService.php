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
        $filtered = $this -> except($data, ['created_at']);
        // 현재 시간 기록
        $filtered['updated_at'] = $this -> now();
        return $this -> userRepository -> update($id, $filtered);
    }

    public function getUserById(int $id): ?array{
        return $this -> userRepository -> findById($id);
    }
}