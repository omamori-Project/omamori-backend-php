<?php

namespace App\Modules\Omamori\Repositories;

use App\Common\Base\BaseRepository;
use App\Core\Database;

class OmamoriRepository extends BaseRepository{
    protected string $table = 'omamoris';

    public function __construct(Database $db)
    {
        parent::__construct($db);
    }

    // 유저별 오마모리 개수 (소프트 삭제 제외)
    public function countByUser(int $userId): int{
        $sql = "SELECT COUNT(*) as count FROM{$this -> table}
                WHERE user_id = ?
                    AND deleted_at IS NULL";
        
        $result = $this -> db -> queryOne($sql, [$userId]);
        return (int)($result['count'] ?? 0);
    }

    // 오마모리 생성
    public function insertDraft(int $userId, string $title, ?string $meaning): int{
        $sql = "INSERT INTO {$this -> table}
                    (user_id, title, meaning, status, created_at, updated_at)
                VALUES
                    (?, ?, ?, 'draft', NOW(), NOW())
                RETURNING id";
        
        $result = $this -> db -> queryOne($sql, [$userId, $title, $meaning]);
        return (int)$result['id'];
    }
}