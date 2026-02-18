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
        $sql = "SELECT COUNT(*) as count FROM {$this -> table}
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

    // 오마모리 복제
    public function duplicateById(int $userId, int $omamoriId): int{
        $sql = "INSERT INTO {$this -> table}(
                    user_id,
                    title,
                    meaning,
                    status,
                    created_at,
                    updated_at,
                    deleted_at
                )
                SELECT
                    user_id,
                    ('복제' || title) as title,
                    meaning,
                    'draft' as status,
                    NOW() as created_at,
                    NOW() as updated_at,
                    NULL as deleted_at
                FROM {$this -> table}
                WHERE id = ?
                    AND user_id = ?
                    AND deleted_at IS NULL
                RETURNING id";
        
        $result = $this -> db -> queryOne($sql, [$omamoriId, $userId]);

        if (!$result || !isset($result['id'])){
            throw new \RuntimeException('Omamori not found or not allowed');
        }
        return (int)$result['id'];

    }

    // 
    public function findOwnById(int $userId, int $omamoriId): ?array{
        $sql = "SELECT id, user_id, title, meaning, status, created_at, updated_at, deleted_at
                FROM {$this -> table}
                WHERE id = ?
                    AND user_id = ?
                    AND deleted_at IS NULL";

        $result = $this -> db -> queryOne($sql, [$omamoriId, $userId]);
        return $result ?: null;
    }
}