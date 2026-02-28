<?php

namespace App\Modules\Community\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\Auth\Services\AuthService;
use App\Modules\Community\Repositories\PostRepository;
use RuntimeException;

// 상속
class PostService extends BaseService{
    protected Database $db;
    protected PostRepository $postRepository;

    public function __construct()
    {
        $this -> db = new Database();
        $this -> postRepository = new PostRepository($this -> db);
    }


    // 게시글 작성
    public function createPost(string $token, array $input): array{
        // 토큰 겁증
        $auth = new AuthService();
        $userId = $auth -> verifyAndGetUserId($token);

        // 입력값 검증
        $this -> validateRequired($input, ['title', 'content', 'omamori_id']);

        // 숫자인지만 체크
        if(!is_numeric($input['omamori_id'])){
            throw new \InvalidArgumentException('Omamori_id must be number');
        }

        // data -> repository
        $data =[
            'user_id' => (int)$userId,
            'omamori_id' => isset($input['omamori_id']) ? (int)$input['omamori_id'] : null,
            'title' => (string)$input['title'],
            'content' => (string)$input['content'],
        ];

        // postId 보내기
        $postId = $this -> postRepository -> createPost($data);

        // postId 받기
        return ['id' => $postId];

    }


    // 전체 게시글 목록 조회 (공개 피드)
    public function index(array $query): array{
        $page = isset($query['page']) && is_numeric($query['page']) ? (int)$query['page'] : 1;
        $size = isset($query['size']) && is_numeric($query['size']) ? (int)$query['size'] : 10;
        $sort = isset($query['sort']) ? (string)$query['sort'] : 'latest';

        if ($page < 1) $page = 1;
        if ($size < 1) $size = 1;
        if ($size > 50) $size = 50;

        $items = $this -> postRepository -> findPostsForFeed($page, $size, $sort);
        $total = $this -> postRepository -> countPostsForFeed();

        return [
            'items' => $items,
            'meta' => [
                'page' => $page,
                'size' => $size,
                'total' => $total,
                'sort' => $sort,
            ],
        ];
    }

    public function showPublishedPost(?string $token, int $postId): array{
        // postId 검증
        if($postId < 1){
            throw new \InvalidArgumentException('posts must be positive integer');
        }

        // 게시글 조회
        $post = $this -> postRepository -> findPublishedPostById($postId);
        if(!$post){
            throw new RuntimeException('Post not found');
        }

        // 로그인 안 해도 항상 내려줌
        $viewer = [
            'is_liked' => false,
            'is_bookmarked' => false,
        ];

        // 로그인 사용자 기준으로 viewer 상태 계산
        if($token){
            $auth = new AuthService();
            $userId = (int)$auth -> verifyAndGetUserId($token);

            $viewer['is_liked'] = $this -> postRepository -> existsLike($postId, $userId);
            $viewer['is_bookmarked'] = $this -> postRepository -> existsBookmark($postId, $userId);
        }

        return [
            'post' => $post,
            'viewer' => $viewer,
        ];
    }
}