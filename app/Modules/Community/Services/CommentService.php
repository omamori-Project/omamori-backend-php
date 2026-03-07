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

        $post = $this->postRepository->findById($postId);
        if(!$post){
            throw new \RuntimeException('Post not found');
        }

        // 기본값 보정
        $page = isset($query['page']) ? (int)$query['page'] : 1;
        $size = isset($query['size']) ? (int)$query['size'] : 10;

        if($page < 1) $page = 1;
        if($size < 1) $size = 10;
        if($size > 50) $size = 50;
        
        return $this -> commentRepository -> paginateByPostId($postId, $page, $size);
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

        // 생성 후 재조회해서 반환
        $created = $this -> commentRepository -> createComment($postId, $userId, $content);
        return  $created;
    }


    // 댓글 목록
    public function listMyComment(string $token, array $query): array{
        // 토큰 검증
        $auth = new AuthService();
        $userId = $auth->verifyAndGetUserId($token);

        $page = isset($query['page']) ? (int)$query['page'] : 1;
        $size = isset($query['size']) ? (int)$query['size'] : 10;
        $sort = isset($query['sort']) ? trim((string)$query['sort']) : 'latest';
        $type = isset($query['type']) ? trim((string)$query['type']) : 'comment';
        $postId = isset($query['postId']) && $query['postId'] !== '' ? (int)$query['postId'] : null;

        if($page < 1) $page = 1;
        if($size < 1) $size = 10;
        if($size > 50) $size = 50;

        if(!in_array($sort, ['latest', 'oldest'], true)){
            throw new \InvalidArgumentException('sort must be latest or oldest');
        }

        if(!in_array($type, ['comment', 'reply'], true)){
            throw new \InvalidArgumentException('type must be comment or reply');
        }

        if($postId !== null && $postId < 1){
            throw new \InvalidArgumentException('postId must be positive integer');
        }

        return $this -> commentRepository -> paginateMyComment($userId, $page, $size, $sort, $type, $postId);
    }


    // 댓글 수정
    public function updateComment(int $commentId, array $input): array
    {
        // commentId 검증
        if ($commentId <= 0) {
            throw new \InvalidArgumentException('CommentId must be positive integer');
        }

        // content 필수 체크
        $this -> validateRequired($input, ['content']);

        $content = trim((string)$input['content']);

        if ($content === '') {
            throw new \InvalidArgumentException('Content is required');
        }

        // 댓글 존재 확인
        $comment = $this -> commentRepository -> findById($commentId);
        if (!$comment) {
            throw new \RuntimeException('Comment not found');
        }

        // 댓글 수정
        $updatedComment = $this -> commentRepository -> updateComment($commentId, $content);
        return $updatedComment;
    }
}