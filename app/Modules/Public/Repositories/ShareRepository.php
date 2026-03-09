<?php

namespace App\Modules\Public\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;


// 상속
class ShareRepository extends BaseRepository{
    protected string $table = 'shares';

    public function __construct(Database $db){
        parent:: __construct($db);
    }


    // shareId로 공유 정보 조회
    public function findById($id): ?array{
        $sql = "SELECT *
                FROM {$this -> table}
                WHERE id = ?
                LIMIT 1";
        return $this -> db -> queryOne($sql, [$id]);
    }


    // share_code로 공유 정보 조회
    public function findByShareCode(string $shareCode): ?array{
        $sql = "SELECT *
                FROM {$this -> table}
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


    // 공유 설정 수정
    public function updateShare(int $shareId, array $data): bool{
        $sql = "UPDATE {$this -> table}
                SET is_public = ?
                WHERE id = ?";

        return $this -> db -> execute($sql, [$data['is_public'], $shareId]);
    }


    // 미리보기 카드
    // token으로 공유 정보 조회
    public function findByToken(string $token): ?array{
        return $this -> findOneBy(['token' => $token]);
    }


    // 공유 링크 생성
    public function createShare(array $data): string{
        return $this -> create([
            'omamori_id' => $data['omamori_id'],
            'share_code' => $data['share_code'],
            'option' => $data['option'],
            'expires_at' => $data['expires_at'],
        ]);
    }
}