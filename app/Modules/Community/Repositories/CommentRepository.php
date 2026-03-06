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
    public function paginateComment(int $postId, int $page, int $size): array{
        return $this -> paginate($page, $size, ['post_id' => $postId]);
    }
}