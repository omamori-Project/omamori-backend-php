<?php
namespace App\Modules\Auth\Repositories;

// import
use App\Core\Database;

class AuthRepository{
    private Database $db;

    public function __construct(Database $db)
    {
        $this -> db = $db;
    }

    public function findUserByEmail(string $email): ?array{
        $sql = "SELECT * FROM users WHERE email = ? AND delete_at IS NULL LIMIT1";
        return $this -> db -> queryOne($sql, [$email]);
    }
}