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


    // 특정 유저 게시글 목록 조회
    public function indexByUser(int $userId, array $query): array{
        // 사용자 검증
        if($userId < 1){
            throw new \InvalidArgumentException('userId must be positive integer');
        }

        // query 정규화
        $page = isset($query['page']) && is_numeric($query['page']) ? (int)$query['page'] : 1;
        $size = isset($query['size']) && is_numeric($query['size']) ? (int)$query['size'] : 10;
        $sort = isset($query['sort']) ? (string)$query['sort'] : 'latest';

        if(!in_array($sort, ['latest', 'popular'])) $sort = 'latest';
        if($page < 1) $page = 1;
        if($size < 1) $size = 1;
        if($size > 50) $size = 50;

        // sort(string) -> orderBy(array)변환
        $orderBy = ['created_at' => 'DESC'];
        if($sort === 'popular'){
            $orderBy = [
                'like_count' => 'DESC',
                'created_at' => 'DESC',
            ];
        }

        $p = $this -> postRepository -> paginateByUserId($userId, $page, $size, $orderBy);
        return [
            'items' => $p['items'],
            'meta' => [
                'page' => $page,
                'size' => $size,
                'total' => $p['total'],
                'sort' => $sort,
            ],
        ];
    }


    // 게시글 수정
    public function updatePost(string $token, int $postId, array $input): array{
        // 토큰 검증
        $auth = new AuthService();
        $userId = (int)$auth -> verifyAndGetUserId($token);

        // postId 검증
        if($postId < 1){
            throw new \InvalidArgumentException('postId must be positive integer');
        }

        // title/content 둘 중 하나는 있어야 함
        $hasTitle = array_key_exists('title', $input);
        $hasContent = array_key_exists('content', $input);
        if(!$hasTitle && !$hasContent){
            throw new \InvalidArgumentException('title or content is required');
        }

        // 전달된 필드만 업데이트
        $data = [];
        if($hasTitle){
            $title = trim((string)$input['title']);
            if($title === ''){
                throw new \InvalidArgumentException('title can not be empty');
            }
            $data['title'] = $title;
        }

        if($hasContent){
            $content = trim((string)$input['content']);
            if($content === ''){
                throw new \InvalidArgumentException('content can not be empty');
            }
            $data['content'] = $content;
        }

        // 대상 게시글 조회
        $post = $this -> postRepository -> findPublishedPostById($postId);
        if(!$post){
            throw new RuntimeException('Post not found');
        }
        
        if((int)$post['usser_id'] !== (int)$userId){
            throw new RuntimeException('Forbidden');
        }

        // 업데이트
        $affected = $this -> postRepository -> updatePost($postId, $data);
        if($affected < 1){
            throw new RuntimeException('Update failed');
        }

        // 데이터 반환
        $updated = $this -> postRepository -> findPublishedPostById($postId);
        if(!$updated){
            throw new RuntimeException('Post not found after update');
        }
        return $updated;
    }


    // 게시글 삭제
    public function deletePost(string $token, int $postId): array{
        // 토큰 검증
        $auth = new AuthService();
        $userId = $auth -> verifyAndGetUserId($token);

        // 존제 확인
        $post = $this -> postRepository -> findPublishedPostById($postId);
        if(!$postId){
            throw new \RuntimeException('Post not found');
        }

        // 사용자 확인
        if((int)$post['user_id'] !== (int)$userId){
            throw new \RuntimeException('Forbidden');
        }

        // soft delete
        $affected = $this -> postRepository -> deletePost($postId);
        if($affected === 0){
            throw new \RuntimeException('Delete faild');
        }
        return ['post' => $postId];
    }

}