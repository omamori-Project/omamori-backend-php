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

    // 좋아요 존재 여부 확인
    public function existsLike(int $userId, int $postId): ?array{
        return $this -> findOneBy([
            'user_id' => $userId,
            'post_id' => $postId
        ]);
    }


    // 좋아요 추가
    public function createLike(int $userId, int $postId): int{
        return (int)$this -> create([
            'user_id' => $userId,
            'post_id' => $postId
        ]);
    }
}