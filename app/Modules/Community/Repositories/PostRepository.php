<?php

namespace App\Modules\Community\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;

// 상속
class PostRepository extends BaseRepository{
    // table명
    protected string $table = 'posts';

    public function __construct(Database $db)
    {
        return parent::__construct($db);
    }

    // 게시글 작성
    public function createPost(array $data): int{
        $sql = "INSERT INTO {$this -> table}
                    (user_id, omamori_id, title, content)
                VALUES
                    (?, ?, ?, ?)
                RETURNING id";

        $result = $this -> db -> queryOne($sql, [
            $data['user_id'],
            $data['omamori_id'],
            $data['title'],
            $data['content']
            ]);
        return (int)$result['id'];
    }


    // 전체 게시글 목록 조회 (공개 피드)
    public function findPublishedPostById(int $postId): ?array{
        $sql = "SELECT id, user_id, omamori_id, title, content, created_at, updated_at
                FROM {$this -> table}
                WHERE id = ?
                    AND status = 'published'
                    AND deleted_at IS NULL
                LIMIT 1";

        $result = $this -> db -> queryOne($sql, [$postId]);
        return $result ? $result : null;
    }
}