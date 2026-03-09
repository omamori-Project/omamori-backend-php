<?php

namespace App\Modules\Public\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;


// 상속
class ShareRepository extends BaseRepository{
    protected string $table = 'shares';

    public function __construct(Database $db)
    {
        parent::__construct($db);
    }

    // share_code로 공유 정보 조회
    public function findByShareCode(string $shareCode): ?array{
        $sql = "SELECT *
                FROM shares
                WHERE share_code = ?
                LIMIT 1";
        return $this -> db -> queryOne($sql, [$shareCode]);
    }

    
    // 공유된 오마모리 조회
    public function findOmamoriById(int $omamoriId): ?array{
        $sql = "SELECT *
                FROM omamoris
                WHERE id = ?
                    AND deleted_at IS NULL
                LIMIT 1";
        return $this -> db -> queryOne($sql, [$omamoriId]);
    }
}