<?php

namespace App\Modules\Omamori\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\Omamori\Repositories\OmamoriRepository;
use App\Modules\Auth\Services\AuthService;

// 상속
class OmamoriServise extends BaseService{
    protected OmamoriRepository $omamoriRepository;

    public function __construct(){
        $db = new Database();
        $this -> omamoriRepository = new OmamoriRepository($db);
    }

    // 오마모리 생성
    public function createOmamori(string $token, array $input): array{
        $auth = new AuthService();
        $userId = $auth -> verifyAndGetUserId($token);

        //  title, meaning만 받기
        $data = $this -> only($input, ['title', 'meaning']);

        // title 정리: 키 없음 / "" / 공백만 => null
        $title = null;
        if(array_key_exists('title', $data)){
            $t = trim((string)$data['title']);
            if($t !== ''){
                $title = $t;
            }
        }

        // meaning 정리: 키 없음 / "" / 공백만 => null
        $meaning = null;
        if(array_key_exists('meaning', $data)){
            $m = trim((string)$data['meaning']);
            if($m !== ''){
                $meaning = $m;
            }
        }

        // title 자동 생성
        if($title === null){
            $count = $this -> omamoriRepository -> countByUser($userId);
            $title = 'omamori'. ($count + 1);
        }

        $id = $this -> omamoriRepository -> insertDraft($userId, $title, $meaning);
        return [
            'id' => $id,
            'title' => $title,
            'meaning' => $meaning,
            'status' => 'draft',
        ];
    }
}