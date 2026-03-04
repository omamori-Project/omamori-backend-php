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
    public function pagenateByPostId(int $postId, int $page, int $size): array{
        return $this -> paginate($page, $size, ['post_id' => $postId]);
    }


    // 댓글 작성
    public function createComment(int $postId, int $userId, string $content): int{
        $id = $this -> create([
            'post_id' => $postId,
            'user_id' => $userId,
            'content' => $content,
        ]);
        return (int)$id;
    }
}