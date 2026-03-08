<?php

namespace App\Modules\Community\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;


// 상속
class LikeRepository extends BaseRepository{
    protected string $table = 'post_likes';

    public function __construct(Database $db)
    {
        parent::__construct($db);
    }

    // 좋아요 존재 여부 확인
    public function existsLike(int $userId, int $postId): bool{
        $sql = "SELECT id
                FROM {$this -> table}
                WHERE user_id = ?
                  AND post_id = ?
                LIMIT 1";

        $result = $this -> db -> queryOne($sql, [$userId, $postId]);
        return !empty($result);
    }


    // 좋아요 추가
    public function createLike(int $userId, int $postId): int{
        $sql = "INSERT INTO {$this -> table}
                    (user_id, post_id)
                VALUES
                    (?, ?)
                RETURNING id";

        $result = $this -> db -> queryOne($sql, [$userId, $postId]);
        return (int)$result['id'];
    }


    // 좋아요 취소
    public function deleteLike(int $userId, int $postId): bool{
        $sql = "DELETE FROM {$this -> table}
                WHERE user_id = ?
                AND post_id = ?";

        $this -> db -> query($sql, [$userId, $postId]);
        return true;
    }
}