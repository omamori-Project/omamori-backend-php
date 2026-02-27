<?php

namespace App\Modules\Community\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\Auth\Services\AuthService;
use App\Modules\Community\Repositories\PostRepository;

// 상속
class PostService extends BaseService{
    protected Database $db;
    protected PostRepository $postrepository;

    public function __construct()
    {
        $this -> db = new Database();
        $this -> postrepository = new PostRepository($this -> db);
    }


    // 게시글 작성
    public function createPost(string $token, array $input){
        // 토큰 겁증
        $auth = new AuthService();
        $userId = $auth -> verifyAndGetUserId($token);

        // 입력값 검증
        $this -> validateRequired($input, ['title', 'content']);

        // 숫자인지만 체크
        if(isset($input['omamori_id']) && !is_numeric($input['omamori_id'])){
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
        $postId = $this -> postrepository -> createPost($data);

        // postId 받기
        return ['id' => $postId];

    }



}