<?php

namespace App\Modules\Community\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;


// 상속
class CommentRepository extends BaseRepository{
    protected string $table = 'comments';

    public function __construct(Database $db)
    {
        parent::__construct($db);
    }


    // 게시물 댓글 조회
    public function paginateByPostId(int $postId, int $page, int $size): array{
        return $this -> paginate($page, $size, ['post_id' => $postId]);
    }


    // 댓글 작성
    public function createComment(int $postId, int $userId, string $content): array{
        $sql = "INSERT INTO {$this -> table}
                (post_id, user_id, parent_id, content)
            VALUES
                (?, ?, ?, ?)
            RETURNING id, post_id, user_id, parent_id, content, created_at, updated_at, deleted_at";

        return $this -> db -> queryOne($sql, [$postId, $userId, null, $content]);
    }


    // 댓글 목록
    public function paginateMyComment(int $userId, int $page, int $size, string $sort, string $type, ?int $postId = null): array{
        $offset = ($page - 1) * $size;

        $conditions = ['user_id = ?', 'deleted_at IS NULL'];
        $params = [$userId];

        if($postId !== null){
            $conditions[] = 'post_id = ?';
            $params[] = $postId;
        }

        if($type === 'comment'){
            $conditions[] = 'parent_id IS NULL';
        }elseif($type === 'reply'){
            $conditions[] = 'parent_id IS NOT NULL';
        }

        $orderBy = $sort === 'oldest' ? 'created_at ASC' : 'created_at DESC';
        $where = implode(' AND ', $conditions);

        $items = $this -> db -> query(
            "SELECT * FROM {$this -> table}
             WHERE {$where}
             ORDER BY {$orderBy} 
             LIMIT ? OFFSET ?",
            [...$params, $size, $offset]
        );

        $totalRow = $this -> db -> queryOne(
            "SELECT COUNT(*) AS count 
             FROM {$this -> table}
             WHERE {$where}",
            $params
        );

        $total = (int)$totalRow['count'];

        return [
            'data' => $items,
            'current_page' => $page,
            'per_page' => $size,
            'total' => $total,
            'last_page' => (int)ceil($total / $size)
        ];
    }


    // 댓글 수정
    public function updateComment(int $commentId, string $content): array{
        $sql = "UPDATE {$this -> table}
                SET content = ?,
                    updated_at = NOW()
                WHERE id = ?
                RETURNING *";
        
        return $this -> db -> queryOne($sql, [$content, $commentId]);
    }


    // 댓글 삭제
    public function deleteComment(int $commentId): array{
        $sql = "UPDATE {$this -> table}
                SET deleted_at = NOW()
                WHERE id = ?
                AND deleted_at IS NULL";

        $result = $this -> db -> queryOne($sql, [$commentId]);
        if(!$result){
            throw new \RuntimeException('Comment not found or already deleted');
        }
        return $result;
    }
}