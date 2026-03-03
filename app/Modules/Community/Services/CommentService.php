<?php

namespace App\Modules\Community\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\Community\Repositories\CommentRepository;

class CommentService extends BaseService{
    protected Database $db;
    protected CommentRepository $commentRepository;

    public function __construct()
    {
        $this -> db = new Database();
        $this -> commentRepository = new CommentRepository($this -> db);
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
}