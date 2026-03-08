<?php

namespace App\Modules\Community\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\Auth\Services\AuthService;
use App\Modules\Community\Repositories\BookmarkRepository;


// 상속
class BookmarkService extends BaseService{
    protected Database $db;
    protected BookmarkRepository $bookmarkRepository;

    public function __construct()
    {
        $this -> db = new Database();
        $this -> bookmarkRepository = new BookmarkRepository($this -> db);
    }

    // 북마크 추가
    public function createBookmark(string $token, int $postId): array{
        // postId 검증
        if ($postId <= 0) {
            throw new \InvalidArgumentException('Invalid postId');
        }

        // 토큰 검증
        $auth = new AuthService();
        $userId = $auth -> verifyAndGetUserId($token);

        // 이미 북마크 했는지 확인
        if ($this -> bookmarkRepository -> existsBookmark($postId, $userId)) {
            throw new \InvalidArgumentException('Already bookmarked');
        }

        // 북마크 저장
        $bookmarkId = $this -> bookmarkRepository -> createBookmark($postId, $userId);
        return [
            'id' => $bookmarkId,
            'post_id' => $postId,
            'user_id' => $userId
        ];
    }
}