<?php

namespace App\Modules\Custom\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;


// 상속
class StampRepository extends BaseRepository{
    protected string $table = 'omamori_elements';

    public function __construct()
    {
        parent::__construct(new Database());
    }

    public function getList(array $filters): array{
        $page = (int)($filters['page'] ?? 1);
        $size = (int)($filters['size'] ?? 24);

        if ($page <= 0) {
            $page = 1;
        }

        if ($size <= 0) {
            $size = 24;
        }

        if ($size > 100) {
            $size = 100;
        }

        $offset = ($page - 1) * $size;

        $sql = "SELECT *
                FROM {$this -> table}
                WHERE type = 'stamp'
                    AND deleted_at IS NULL
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?";

        $items = $this -> db -> query($sql, [$size, $offset]);

        $countSql = "SELECT COUNT(*) as count
                     FROM {$this -> table}
                     WHERE type = 'stamp'
                        AND deleted_at IS NULL";

        $countResult = $this -> db -> queryOne($countSql);
        $total = (int)($countResult['count'] ?? 0);

        return ['items' => $items,
                'pagination' => [
                    'page' => $page,
                    'size' => $size,
                    'total' => $total,
                    'total_pages' => (int) ceil($total / $size),
                ],];
    }
}