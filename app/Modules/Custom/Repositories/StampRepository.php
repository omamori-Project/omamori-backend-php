<?php

namespace App\Modules\Custom\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;


// 상속
class StampRepository extends BaseRepository{
    protected string $table = 'stamps';

    public function __construct()
    {
        parent::__construct(new Database());
    }

    public function getList(array $filters): array{
        $page = (int)($filters['page'] ?? 1);
        $size = (int)($filters['size'] ?? 24);
        $offset = ($page - 1) * $size;

        $conditions = ['deleted_at IS NULL'];
        $params = [];

        if (!empty($filters['q'])) {
            $conditions[] = 'name LIKE ?';
            $params[] = '%' . $filters['q'] . '%';
        }

        if (!empty($filters['category'])) {
            $conditions[] = 'category = ?';
            $params[] = $filters['category'];
        }

        if (!empty($filters['asset_key'])) {
            $conditions[] = 'asset_key = ?';
            $params[] = $filters['asset_key'];
        }

        $where = 'WHERE ' . implode(' AND ', $conditions);

        $orderBy = 'ORDER BY name ASC';
        if (($filters['sort'] ?? 'name') === 'latest') {
            $orderBy = 'ORDER BY created_at DESC';
        }

        $listSql = "SELECT *
                    FROM {$this -> table}
                         {$where}
                         {$orderBy}
                    LIMIT ? OFFSET ?";

        $listParams = [...$params, $size, $offset];
        $items = $this -> db -> query($listSql, $listParams);

        $countSql = "SELECT COUNT(*) as count
                     FROM {$this -> table}
                     {$where}";

        $totalResult = $this -> db -> queryOne($countSql, $params);
        $total = (int)($totalResult['count'] ?? 0);

        return ['items' => $items,
                'pagination' => [
                    'page' => $page,
                    'size' => $size,
                    'total' => $total,
                    'total_pages' => (int) ceil($total / $size),
                ]];
    }
}