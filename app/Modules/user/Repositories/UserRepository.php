<?php

namespace App\Modules\User\Repositories;

// Base
use App\Common\Base\BaseRepository;
use App\Core\Database;

// 継承
class UserRepository extends BaseRepository{
    // テーブル名
    protected string $table = 'users';

    // DBを UserRepository -> BaseRepository
    public function __construct(Database $db){
        parent:: __construct($db);
    }

    // メール重複確認
    public function emailExists(string $email): bool{
        return $this -> exists(['email' => $email]);
    }
}