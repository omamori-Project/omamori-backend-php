<?php

namespace App\Modules\Element\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;

// 상속
class ElementRepository extends BaseRepository{
    // table명
    protected string $table = 'omamori_elements';

    public function __construct(Database $db)
    {
        parent::__construct($db);
    }
    
    // 요소 추가
    public function insert(int $omamoriId, string $type, int $layer, array $props, array $transform): array{
       $sql = "INSERT INTO {$this -> table}
                    (omamori_id, type, layer, props, transform, created_at, updated_at)
                VALUES
                    (?, ?, ?, ?, ?, NOW(), NOW())
                RETURNING id, omamori_id, type, layer, props, transform, created_at, updated_at";

        $propsJson = json_encode($props, JSON_UNESCAPED_UNICODE);
        $transformJson = json_encode($transform, JSON_UNESCAPED_UNICODE);

        $row = $this -> db -> queryOne($sql, [$omamoriId, $type, $layer, $propsJson, $transformJson]);
        if(!$row){
            throw new \RuntimeException('Insert element failed');
        }
        return $row;
    }

    // 오마모리 요소 재정렬 (background 제외)
    public function getElementsById(int $omamoriId, array $elementIds): array{
        if(empty($elementIds)){
            return [];
        }

        $placeholder = implode(',', array_fill(0, count($elementIds), '?'));
        $sql = "SELECT id, omamori_id, type, layer, deleted_at
                FROM {$this -> table}
                WHERE omamori_id = ?
                    AND deleted_at IS NULL
                    AND id IN ({$placeholder})";
        
        $params = array_merge([$omamoriId], array_values($elementIds));
        return $this -> db -> query($sql, $params);
    }

    // 요소 레이어 일괄 업데이트
    public function updateLayersBulk(int $omamoriId, array $idToLayer): array{
        if(empty($idToLayer)){
            return [];
        }

        $caseSql = "CASE id ";
        $params = [];
        foreach($idToLayer as $id => $layer){
            $caseSql .= "WHEN ? THEN (? )::int ";
            $params[] = (int)$id;
            $params[] = (int)$layer;
        }
        $caseSql .= "END";

        $ids = array_keys($idToLayer);
        $idPlaceholders = implode(',', array_fill(0, count($ids), '?'));

        $sql = "UPDATE {$this -> table}
                SET layer = {$caseSql},
                    updated_at = NOW()
                WHERE omamori_id = ?
                    AND deleted_at IS NULL
                    AND id IN ({$idPlaceholders})
                RETURNING id, omamori_id, type, layer, updated_at";
        
        $params[] = (int)$omamoriId;
        foreach($ids as $id){
            $params[] = (int)$id;
        }
        return $this -> db -> query($sql, $params);
    }


    // 
    public function findNonBackgroundIdsByOmamoriId(int $omamoriId): array {
        $sql = "SELECT id
                FROM {$this -> table}
                WHERE omamori_id = ?
                    AND deleted_at IS NULL
                    AND type <> 'background'";

        $rows = $this -> db -> query($sql, [$omamoriId]);
        $ids = [];
        foreach($rows as $row){
            $ids[] = (int)$row['id'];
        }
        return $ids;
    }

    // background layer = 항상 0 으로 고정
    public function setBackgroundLayerZero(int $omamoriId): void{
        $sql = "UPDATE {$this -> table}
                SET layer = 0,
                    updated_at = NOW()
                WHERE omamori_id = ?
                    AND deleted_at IS NULL
                    AND type = 'background'";

        $this -> db -> execute($sql, [$omamoriId]);
    }

    // element 단건 조회
    public function findElementById(int $elementId): ?array{
        $sql = "SELECT id, omamori_id, type, layer, props, transform, deleted_at, created_at, updated_at
                FROM {$this -> table}
                WHERE id = ?";

        $row = $this -> db -> queryOne($sql, [$elementId]);
        return $row ?: null;
    }


    // element props/transform 전체 갱신
    public function updateElementPropsTransform(int $elementId, ?string $propsJson, ?string $transformJson): array{
        $sql = "UPDATE {$this -> table}
                SET props = ?,
                    transform = ?,
                    updated_at = NOW()
                WHERE id = ?
                RETURNING id, omamori_id, type, layer, props, transform, updated_at";

        $row = $this -> db -> queryOne($sql, [$propsJson, $transformJson, $elementId]);
        if(!$row){
            throw new \RuntimeException('Update element failed');
        }
        return $row;
    }


    // 오마모리 요소 한개만 조회
    public function findActiveElementByOmamoriId(int $omamoriId, int $elementId): ?array{
        $sql = "SELECT id, omamori_id, type, layer, props, transform, created_at, updated_at, deleted_at
                FROM {$this -> table}
                WHERE omamori_id = ?
                    AND id = ?
                    AND deleted_at IS NULL
                    AND type <> 'background'";

        $row = $this -> db -> queryOne($sql, [$omamoriId, $elementId]);
        return $row ?: null;
    }


    // element 삭제
    public function softDeleteElement(int $omamoriId, int $elementId): ?array{
        $sql = "UPDATE {$this -> table}
                SET deleted_at = NOW(),
                    updated_at = NOW()
                WHERE omamori_id = ?
                    AND id = ?
                    AND deleted_at IS NULL
                    AND type <> 'background'
                RETURNING id, omamori_id, deleted_at, updated_at";
        
        $row = $this -> db -> queryOne($sql, [$omamoriId, $elementId]);
        // 없으면 null 처리 (404)
        return $row ?: null;
    }


    // 삭제되지 않은 non-background 요소를 layer 순으로 가져오기
    public function findActiveNonBackgroundIdsOrdered(int $omamoriId): array{
        $sql = "SELECT id
                FROM {$this -> table}
                WHERE omamori_id = ?
                    AND deleted_at IS NULL
                    AND type <> 'background'
                ORDER BY layer ASC, id ASC";

        $rows = $this -> db -> query($sql, [$omamoriId]);

        $ids = [];
        foreach($rows as $row){
            $ids[] = (int)$row['id'];
        }
        return $ids;
    }


    // 대상 오마모리 요소 전부를 삭제
    public function softDeleteAllByOmamoriId(int $omamoriId): int{
        $sql = "UPDATE {$this -> table}
                SET deleted_at = NOW(),
                    updated_at = NOW()
                WHERE omamori_id = ?
                    AND deleted_at IS NULL";
        
        $stmt = $this -> db -> getConnection() -> prepare($sql);
        $stmt -> execute([$omamoriId]);
        return $stmt -> rowCount();
    }

}