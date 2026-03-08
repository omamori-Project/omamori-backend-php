<?php

namespace App\Modules\Community\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;


// 상속
class LikeRepository extends BaseRepository{
    protected string $table = 'post_likes';

    public function __construct(Database $db)
    {
        parent::__construct($db);
    }

    // 특정 유저가 특정 게시글에 좋아요를 눌렀는지 확인
    public function findLikeByUserAndPost(int $userId, int $postId): ?array{
        return $this->findOneBy([
            'user_id' => $userId,
            'post_id' => $postId
        ]);
    }


    // 좋아요 추가
    public function createLike(int $userId, int $postId): int{
        return (int)$this->create([
            'user_id' => $userId,
            'post_id' => $postId
        ]);
    }
}