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
    public function getCommentsByPost(int $postId, int $page, int $size): array{
        if($postId < 1){
            throw new \InvalidArgumentException('postId must be positive integer');
        }

        // 기본값 보정
        if($page < 1){
            $page = 1;
        }
        if($size < 1){
            $size = 10;
        }

        // 과도한 요청 방지
        if($size > 50){
            $size = 50;
        }
        return $this -> commentRepository -> pagenateByPostId($postId, $page, $size);
    }
}