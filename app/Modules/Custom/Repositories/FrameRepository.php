<?php

namespace App\Modules\Custom\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;


// 상속
class FrameRepository extends BaseRepository{
    // table명
    protected string $table = 'frames';
    protected string $primaryKey = 'id';

    public function __construct(Database $db){
        parent::__construct($db);
    }


    // 유저용 프레임 목록
    public function getFrames(?int $isActive, int $page = 1, int $size = 10): array{
        $criteria = [];
        if ($isActive !== null) {
            $criteria['is_active'] = $isActive;
        }
        return $this -> paginate($page, $size, $criteria);
    }


    // 프레임 적용
    public function findByFrameKey(string $frameKey): ?array{
        return $this -> findOneBy(['frame_key' => $frameKey]);
    }


    // 프레임 적용 해제
    public function removeFrame(int $omamoriId): bool{
        return $this -> update($omamoriId, [
            'applied_frame_id' => null,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }


    // 프레임 목록 (관리자)
    public function getAdminFrames(int $page = 1, int $size = 10, ?int $isActive = null, string $keyword = ''): array{
        $offset = ($page - 1) * $size;

        $conditions = ['deleted_at IS NULL'];
        $params = [];

        if ($isActive !== null) {
            $conditions[] = 'is_active = ?';
            $params[] = $isActive;
        }

        if ($keyword !== '') {
            $conditions[] = '(name ILIKE ? OR frame_key ILIKE ?)';
            $params[] = "%{$keyword}%";
            $params[] = "%{$keyword}%";
        }

        $where = implode(' AND ', $conditions);

        $items = $this -> db -> query(
            "SELECT * FROM {$this -> table}
            WHERE {$where}
            ORDER BY id DESC
            LIMIT ? OFFSET ?",
            array_merge($params, [$size, $offset])
        );

        $countResult = $this -> db -> queryOne(
            "SELECT COUNT(*) as count
            FROM {$this -> table}
            WHERE {$where}",
            $params
        );

        $total = (int)$countResult['count'];

        return [
            'data' => $items,
            'current_page' => $page,
            'per_page' => $size,
            'total' => $total,
            'last_page' => (int)ceil($total / $size),
        ];
    }
}