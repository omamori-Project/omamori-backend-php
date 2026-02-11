<?php

namespace App\Modules\User\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;

// 상속
class UserRepository extends BaseRepository{
    // 데이플 작성
    protected string $table = 'users';

    // DB를 UserRepository -> BaseRepository 보내기
    public function __construct(Database $db){
        parent:: __construct($db);
    }

    // 이메일 중북 확인
    public function emailExists(string $email): bool{
        return $this -> exists(['email' => $email]);
    }
}