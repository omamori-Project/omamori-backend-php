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
    public function findPostsForFeed(int $page, int $size, string $sort): array{
        $sort = in_array($sort, ['latest', 'popular']);

        $orderBy = ($sort === 'popular') ? 'like_count DESC, created_at DESC' : 'created_at DESC';
        $offset = ($page - 1) * $size;

        $sql = "SELECT id, user_id, omamori_id, title, content, like_count, comment_count, bookmark_count, created_at, updated_at
                FROM {$this -> table}
                WHERE deleted_at IS NULL
                ORDER BY {$orderBy}
                LIMIT ? OFFSET ?";
        return $this -> db -> query($sql, [$size, $offset]);
    }

    public function countPostsForFeed(): int{
        $sql = "SELECT COUNT(*) AS cnt
                FROM {$this -> table}
                WHERE deleted_at IS NULL";

        $row = $this -> db -> queryOne($sql);
        return (int)($row['cnt'] ?? 0);
    }

    // 상세 조회
    public function findPublishedPostById(int $postId): ?array{
        $sql = "SELECT id, user_id, omamori_id, title, content, like_count, comment_count, bookmark_count, created_at, updated_at
                FROM {$this -> table}
                WHERE id = ?
                    AND deleted_at IS NULL
                LIMIT 1";

        $result = $this -> db -> queryOne($sql, [$postId]);
        return $result ? $result : null;
    }


    // 좋아요 표시
    public function existsLike(int $postId, int $userId): bool{
        $sql = "SELECT 1
                FROM post_likes
                WHERE post_id = ?
                    AND user_id = ?
                LIMIT 1";

        $result = $this -> db -> queryOne($sql, [$postId, $userId]);
        return $result ? true : false;
    }


    // Bookmark 표시
    public function existsBookmark(int $postId, int $userId): bool{
        $sql = "SELECT 1
                FROM post_bookmarks
                WHERE post_id = ?
                    AND user_id = ?
                LIMIT 1";
        
        $result = $this -> db -> queryOne($sql, [$postId, $userId]);
        return $result ? true : false;
    }


    // 특정 유저 게시글 목록 조회
    public function paginateByUserId(int $userId, int $page, int $size, array $orderBy = ['created_at' => 'DESC']): array{
        $orderParts = [];
        foreach($orderBy as $column => $direction){
            $orderParts[] = "{$column} {$direction}";
        }
        $orderBySql = !empty($orderParts) ? implode(', ', $orderParts) : 'created_at DESC';

        $offset = ($page - 1) * $size;

        $sql = "SELECT id, user_id, omamori_id, title, content, like_count, comment_count, bookmark_count, created_at, updated_at
                FROM {$this -> table}
                 WHERE user_id = ?
                   AND deleted_at IS NULL
                 ORDER BY {$orderBySql}
                 LIMIT ? OFFSET ?";

        $items = $this -> db -> query($sql, [$userId, $size, $offset]);

        $sqlTotal = "SELECT COUNT(*) AS cnt
                  FROM {$this->table}
                  WHERE user_id = ?
                    AND deleted_at IS NULL";

        $row = $this->db->queryOne($sqlTotal, [$userId]);
        $total = (int)($row['cnt'] ?? 0);

        return [
            'items' => is_array($items) ? $items : [],
            'total' => $total,
        ];
    }


    // 게시글 수정
    public function updatePost(int $postId, array $data): int{
        // 허용 필드만 남기기
        $allowed = ['title', 'content', 'updated_at'];
        $filtered = array_intersect_key($allowed);
        if(empty($filtered)){
            throw new \InvalidArgumentException('No fields to update');
        }

        $setParts = [];
        $params = [];
        foreach($filtered as $key => $value){
            $setParts[] = "{$key} = ?";
            $params[] = $value;
        }

        $setSql = implode(', ', $setParts);
        $sql = "UPDATE {$this -> table}
                SET {$setSql}
                    AND deleted_at IS NULL";
        
        $params[] = $postId;
        return $this -> db -> execute($sql, $params);
    }
}