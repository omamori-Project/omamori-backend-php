<?php

namespace App\Modules\Omamori\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;

// 상속
class OmamoriElementRepository extends BaseRepository{
    // 리포지토리명
    protected string $table = 'omamori_elements';

    public function __construct(Database $db)
    {
        parent::__construct($db);
    }
    
    // 요소 추가
    public function insert(int $omamoriId, string $type, int $layer, array $props, array $transform): array{
        $sql = "INSERT INTO {$this -> table}
                    (omamori_Id, type, layer, props, transform, created_at, updated_at)
                VALUES
                    (?, ?, ?, ?, ?, NOW(), NOW())
                RETURNING id, omamori_id, type, layer, props, transform, created_at, updated_at";

        $propsJson = json_encode($props, JSON_UNESCAPED_UNICODE);
        $transformJson = json_encode($transform, JSON_UNESCAPED_UNICODE);

        $row = $this -> db -> queryOne($sql, [$omamoriId, $type, $layer, $propsJson, $transformJson,]);
    
        if(!$row){
            throw new \RuntimeException('Insert element failed');
        }
        return $row;
    }
}