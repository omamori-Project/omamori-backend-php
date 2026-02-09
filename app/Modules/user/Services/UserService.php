<?php

namespace App\Modules\User\Services;

// Base クラスを import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\User\Repositories\UserRepository;

// 継承
class UserService extends BaseService{
    protected UserRepository $userRepository;

    public function __construct(){
        $db = new Database();
        $this -> userRepository = new UserRepository($db);
    }

    public function createUser(array $data){
        // BaseService の validateRequired() メソッド
        $this->validateRequired($data, ['email', 'password', 'name']);

        // メール 重複確認
        if($this -> userRepository -> emailExists($data['email'])){
            throw new \InvalidArgumentException('Email already exists');
        }

        // password -> hash
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        // 現在時間
        $data['created_at'] = $this -> now();
        $data['updated_at'] = $this -> now();


        return $this -> userRepository -> create($data);
    }

    // 更新
    public function updateUser(int $id, array $data): bool{
        // created_at以外を選出
        $filtered = $this -> except($data, ['created_at']);
        // 現在時間
        $filtered['updated_at'] = $this -> now();
        return $this -> userRepository -> update($id, $filtered);
    }
}