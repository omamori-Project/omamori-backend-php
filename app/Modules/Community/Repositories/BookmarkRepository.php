<?php

namespace App\Modules\Community\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;


// 상속
class BookmarkRepository extends BaseRepository{
    protected string $table = 'post_bookmarks';

    public function __construct(Database $db)
    {
        parent::__construct($db);
    }


    // 북마크 추가
    public function createBookmark(int $postId, int $userId): string{
        return $this -> create([
            'post_id' => $postId,
            'user_id' => $userId
        ]);
    }

    // 이미 북마크 했는지 확인
    public function existsBookmark(int $postId, int $userId): bool{
        $sql = "SELECT 1 
                FROM {$this->table}
                WHERE post_id = ?
                    AND user_id = ?
                LIMIT 1";
        $result = $this -> db -> queryOne($sql, [$postId, $userId]);
        return (bool)$result;
    }


    // 북마크 취소
    public function deleteBookmark(int $postId, int $userId): bool{
        $sql = "DELETE FROM {$this -> table}
                WHERE post_id = ?
                    AND user_id = ?";

        $result = $this -> db -> execute($sql, [$postId, $userId]);
        return $result > 0;
    }
}