<?php

namespace App\Modules\System\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;


// 상속
class RenderRepository extends BaseRepository{
    // table명
    protected string $table = 'renders';
    protected string $primaryKey = 'id';

    public function __construct(Database $db){
        parent::__construct($db);
    }

    /**
     * render_code で1件取得
     */
    public function findByRenderCode(string $renderCode): ?array{
        return $this -> findOneBy(['render_code' => $renderCode]);
    }


    // Render 이력
    public function findMyRenders(int $userId, int $page = 1, int $perPage = 15): array{
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT *
                FROM {$this -> table}
                WHERE user_id = ?
                    AND deleted_at IS NULL
                ORDER BY created_at DESC, id DESC
                LIMIT ? OFFSET ?";

        $items = $this -> db -> query($sql, [$userId, $perPage, $offset]);

        $countSql = "SELECT COUNT(*) AS count
                     FROM {$this->table}
                     WHERE user_id = ?
                        AND deleted_at IS NULL";

        $totalRow = $this -> db -> queryOne($countSql, [$userId]);
        $total = (int)($totalRow['count'] ?? 0);
        return [
            'data' => $items,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => (int)ceil($total / $perPage)
        ];
    }


    // render_code, user_id를 사용해서 1건 취득
    public function findByRenderCodeAndUserId(string $renderCode, int $userId): ?array{
        return $this -> findOneBy([
            'render_code' => $renderCode,
            'user_id' => $userId
        ]);
    }


    // render_code으로 삭제 태상 취득
    public function deleteByRenderCode(string $renderCode): bool{
        $sql = "UPDATE {$this -> table}
                SET deleted_at = ?
                WHERE render_code = ?
                    AND deleted_at IS NULL";

        return $this -> db -> execute($sql, [date('Y-m-d H:i:s'), $renderCode]);
    }
}