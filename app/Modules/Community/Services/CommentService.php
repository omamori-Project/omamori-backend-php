<?php

namespace App\Modules\Community\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\Community\Repositories\CommentRepository;
use App\Modules\Auth\Services\AuthService;
use App\Modules\Community\Repositories\PostRepository;

class CommentService extends BaseService{
    protected Database $db;
    protected CommentRepository $commentRepository;
    protected PostRepository $postRepository;

    public function __construct()
    {
        $this -> db = new Database();
        $this -> commentRepository = new CommentRepository($this -> db);
        $this -> postRepository = new PostRepository($this -> db);
    }


    // 댓글 조회
    public function showByPost(int $postId, array $query): array{
        if($postId < 1){
            throw new \InvalidArgumentException('postId must be positive integer');
        }

        // 기본값 보정
        $page = isset($query['page']) ? (int)$query['page'] : 1;
        $size = isset($query['size']) ? (int)$query['size'] : 10;

        if($page < 1) $page = 1;
        if($size < 1) $size = 10;
        if($size > 50) $size = 50;
        
        return $this -> commentRepository -> pagenateByPostId($postId, $page, $size);
    }


    // 댓글 작성
    public function createComment(string $token, int $postId, array $input): array{
        $auth = new AuthService();
        $userId = $auth -> verifyAndGetUserId($token);
        if ($postId < 1) {
            throw new \InvalidArgumentException('postId must be positive integer');
        }

        $this -> validateRequired($input, ['content']);

        $content = trim((string)$input['content']);
        if ($content === '') {
            throw new \InvalidArgumentException('content is required');
        }

        // 게시글 존재 확인 (없으면 404가 맞음)
        $post = $this -> postRepository -> findById($postId);
        if (!$post) {
            throw new \RuntimeException('Post not found');
        }

        $commentId = $this -> commentRepository -> createComment($postId, $userId, $content);

        // 생성 후 재조회해서 반환
        $created = $this -> commentRepository -> findById($commentId);
        return $created ?? ['id' => $commentId];
    }
}