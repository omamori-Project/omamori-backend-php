<?php

namespace App\Modules\Custom\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;


// 상속
class FortuneColorRepository extends BaseRepository{
    // table명
    protected string $table = 'fortune_colors';

    public function __construct(Database $db)
    {
        parent::__construct($db);
    }

    // 유호 색 찾기
    public function findActiveColors(): array{
        $sql = "SELECT *
                FROM {$this -> table}
                WHERE is_active = true
                AND deleted_at IS NULL
                ORDER BY id ASC";

        return $this -> db -> query($sql);
    }


    // 행운컬러 목록
    public function findList(int $size, int $offset, ?string $sort = 'latest', ?string $category = null, bool $active = true): array{
        $sql = "SELECT id, code, name, hex, category, short_meaning, meaning, tips, is_active, created_at, updated_at
                FROM {$this -> table}
                WHERE deleted_at IS NULL";

        $params = [];
        if($active){
            $sql .= " AND is_active = true";
        }
        if($category !== null){
            $sql .= " AND category = ?";
            $params[] = $category;
        }

        $orderBy = "id DESC";
        if($sort === 'name'){
            $orderBy = "name ASC";
        }

        $sql .= " ORDER BY {$orderBy} LIMIT ? OFFSET ?";
        $params[] = $size;
        $params[] = $offset;
        return $this -> db -> query($sql, $params);
    }

    // 행운컬러 개수
    public function countList(?string $category = null, bool $active = true): int{
        $sql = "SELECT COUNT(*) as total
                FROM {$this -> table}
                WHERE deleted_at IS NULL";

        $params = [];
        if($active){
            $sql .= " AND is_active = true";
        }
        if($category !== null){
            $sql .= " AND category = ?";
            $params[] = $category;
        }

        $row = $this -> db ->queryOne($sql, $params);
        return (int)($row['total'] ?? 0);
    }
}