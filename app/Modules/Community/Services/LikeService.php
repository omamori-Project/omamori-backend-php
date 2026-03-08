<?php

namespace App\Modules\Community\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\Auth\Services\AuthService;
use App\Modules\Community\Repositories\LikeRepository;
use App\Modules\Community\Repositories\PostRepository;
use RuntimeException;

// 상속
class LikeService extends BaseService{
    protected Database $db;
    protected LikeRepository $likesRepository;
    protected PostRepository $postRepository;

    public function __construct(){
        $this -> db = new Database();
        $this -> likesRepository = new LikeRepository($this -> db);
        $this -> postRepository = new PostRepository($this -> db);
    }


    // 좋아요 추가
    public function createLike(string $token, int $postId): array{
        // 토큰 검증
        $auth = new AuthService();
        $userId = $auth -> verifyAndGetUserId($token);

        // postId 검증
        if ($postId <= 0) {
            throw new \InvalidArgumentException('Post id must be positive integer');
        }

        // 게시글 존재 확인
        $post = $this -> postRepository -> findById($postId);
        if (!$post) {
            throw new RuntimeException('Post not found');
        }

        // 중복 좋아요 확인
        if ($this -> likesRepository -> existsLike($userId, $postId)) {
            throw new RuntimeException('Already liked');
        }

        // 좋아요 추가
        $likeId = $this -> likesRepository -> createLike($userId, $postId);
        return [
            'id' => $likeId,
            'post_id' => $postId,
            'user_id' => $userId
        ];
    }


    // 좋아요 취소
    public function destroyLike(string $token, int $postId): array{
        // 토큰 검증
        $auth = new AuthService();
        $userId = $auth -> verifyAndGetUserId($token);

        // postId 검증
        if ($postId <= 0) {
            throw new \InvalidArgumentException('Post id must be positive integer');
        }

        // 게시글 존재 확인
        $post = $this -> postRepository -> findById($postId);
        if (!$post) {
            throw new RuntimeException('Post not found');
        }

        // 좋아요 존재 여부 확인
        if (!$this -> likesRepository -> existsLike($userId, $postId)) {
            throw new RuntimeException('Like not found');
        }

        // 좋아요 삭제
        $this -> likesRepository -> deleteLike($userId, $postId);

        return [
            'post_id' => $postId,
            'user_id' => $userId
        ];
    }
}