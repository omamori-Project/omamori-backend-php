<?php

namespace App\Modules\Omamori\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\Omamori\Repositories\OmamoriRepository;
use App\Modules\Auth\Services\AuthService;

// 상속
class OmamoriService extends BaseService{
    protected Database $db;
    protected OmamoriRepository $omamoriRepository;

    public function __construct(){
        $this -> db = new Database();
        $this -> omamoriRepository = new OmamoriRepository($this -> db);
    }


    // 오마모리 생성
    public function createOmamori(string $token, array $input): array{
        // 토큰 검증
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


    // 오마모리 복제
    public function duplicateOmamori(string $token, int $omamoriId): array{
        $auth = new AuthService();
        $userId = $auth -> verifyAndGetUserId($token);

        $newId = $this -> omamoriRepository -> duplicateById($userId, $omamoriId);
        $row = $this -> omamoriRepository -> findOwnById($userId, $newId);
        if(!$row){
            throw new \RuntimeException('Duplicated omamori not found');
        }

        return [
            'id' => (int)$row['id'],
            'title' => $row['title'],
            'meaning' => $row['meaning'],
            'status' => $row['status'],
            'applied_frame_id' => $row['applied_frame_id'] ?? null,
        ];
    }


    // 오마모리 조회
    public function getOwnOmamoriById(string $token, int $omamoriId): array{
        // 토큰 검증
        $auth = new AuthService();
        $userId = $auth -> verifyAndGetUserId($token);

        $row = $this -> omamoriRepository -> findOwnById($userId, $omamoriId);

        if(!$row){
            throw new \RuntimeException('Omamori not found');
        }
        return[
            'id' => (int)$row['id'],
            'title' => $row['title'],
            'meaning' => $row['meaning'],
            'status' => $row['status'],
            'back_message' => $row['back_message'],
            'applied_frame_id' => $row['applied_frame_id'] ?? null,
            'published_at' => $row['published_at'] ?? null,
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ];
    }


    // 오마모리 내 목록
    public function getList(
        string $token,
        int $page,
        int $size,
        ?string $status = null,
        ?string $sort = 'latest'): array{
        // 1 이하
            if($page < 1) $page = 1;

            if($size < 1) $size = 10;
            if($size > 50) $size = 50;

            if($status !== null){
                $status = trim((string)$status);

                if($status == ''){
                    $status = null;

                }elseif(!in_array($status, ['draft', 'published'], true)){
                    throw new \InvalidArgumentException('Invalid status');
                }
            }

            if($sort !== null){
                $sort = trim((string)$sort);

                if(!in_array($sort, ['latest', 'oldest', 'updated'], true)){
                    throw new \InvalidArgumentException('Invalid sort');
                }
            }

            $offset = ($page - 1) * $size;

            $auth = new AuthService();
            $userId = $auth -> verifyAndGetUserId($token);

            $items = $this -> omamoriRepository -> findByUserId($userId, $size, $offset, $status, $sort);
            $total = $this -> omamoriRepository -> countByUserId($userId, $status);

            return [
                'items' => $items,
                'meta' => [
                    'page' => $page,
                    'size' => $size,
                    'total' => $total,
                    'total_pages' => (int)ceil($total / $size),
                ],
            ];
    }


    // 오마모리 공개
    public function publishOmamori(string $token, int $omamoriId): array{
        // 토큰 검증
        $auth = new AuthService();
        $userId = $auth -> verifyAndGetUserId($token);

        // 내 omamori 조회
        $row = $this -> omamoriRepository -> findOwnById($userId, $omamoriId);
        if(!$row){
            throw new \RuntimeException('Omamori not found');
        }

        // 이미 published면 같은 결과(200)
        if(($row['status'] ?? null) === 'published'){
            return [
                'id' => (int)$row['id'],
                'title' => $row['title'],
                'meaning' => $row['meaning'],
                'status' => $row['status'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
                'published_at' => $row['published_at'] ?? null,
                'applied_frame_id' => $row['applied_frame_id'] ?? null,
            ];
        }

        // draft만 publish 가능
        if(($row['status'] ?? null) !== 'draft'){
            throw new \InvalidArgumentException('Invalid status');
        }

        // draft -> published 업데이트
        $updated = $this -> omamoriRepository -> publish($userId, $omamoriId);

        return [
            'id' => (int)$updated['id'],
            'title' => $updated['title'],
            'meaning' => $updated['meaning'],
            'status' => $updated['status'],
            'created_at' => $updated['created_at'],
            'updated_at' => $updated['updated_at'],
            'published_at' => $updated['published_at'] ?? null,
            'applied_frame_id' => $row['applied_frame_id'] ?? null,

        ];
    }


    // 오마모리 정보 수정
    public function updateOmamori(string $token, int $omamoriId, array $input): array{
        // 토큰 검증
        $auth = new AuthService();
        $userId = $auth -> verifyAndGetUserId($token);

         // 허용 필드만 받기
        $data = $this -> only($input, ['title', 'meaning', 'applied_frame_id']);

        // title 정리
        if(array_key_exists('title', $data)){
        $title = trim((string)$data['title']);
            if($title === ''){
                throw new \InvalidArgumentException('title required');
            }
            $data['title'] = $title;
        }      

        // meaning 정리
        if(array_key_exists('meaning', $data)){
            if($data['meaning'] === null){
                $data['meaning'] = null;
            }else{
                $meaning = trim((string)$data['meaning']);
                $data['meaning'] = ($meaning === '') ? null : $meaning;
            }
        }

        // applied_frame_id 정리
        if(array_key_exists('applied_frame_id', $data)){
            if($data['applied_frame_id'] === null){
                $data['applied_frame_id'] = null;
            }else{
                if(!is_int($data['applied_frame_id']) && !(is_string($data['applied_frame_id']) && ctype_digit($data['applied_frame_id']))){
                    throw new \InvalidArgumentException('applied_frame_id must be int or null');
                }
                $data['applied_frame_id'] = (int)$data['applied_frame_id'];
            }
        }

        // 하나도 없으면 실패
        if(empty($data)){
            throw new \InvalidArgumentException('At least one field is required');
        }
    

        // update
        $updated = $this -> omamoriRepository -> updateDraftInfo($userId, $omamoriId, $data);
        return [
            'id' => (int)$updated['id'],
            'title' => $updated['title'],
            'meaning' => $updated['meaning'],
            'status' => $updated['status'],
            'applied_frame_id' => $updated['applied_frame_id'] ?? null,
            'published_at' => $updated['published_at'],
            'created_at' => $updated['created_at'],
            'updated_at' => $updated['updated_at'],
        ];
    }


    // 오마모리 삭제
    public function deleteOmamori(string $token, int $omamoriId): array{
        // 토큰 검증
        $auth = new AuthService();
        $userId = $auth -> verifyAndGetUserId($token);

        return $this -> omamoriRepository -> softDelete($userId, $omamoriId);
    }

    
    // 오마모리 뒷면 메시지 입력/수정
    public function updateBackMessage(string $token, int $omamoriId, array $input): array{
        // 토큰 검증
        $auth = new AuthService();
        $userId = $auth -> verifyAndGetUserId($token);

        $current = $this -> omamoriRepository -> findOwnById($userId, $omamoriId);
        if(!$current){
            throw new \RuntimeException('Omamori not found');
        }

        // 입력값 추출
        $backMessage = $input['back_message'] ?? null;

        // 빈 문자열은 null로 통일
        if(is_string($backMessage)){
            $backMessage = trim($backMessage);
            // 없으면 null
            if($backMessage === ''){
                $backMessage = null;
            }
        }

        // 배열/객체 들어오면 실패
        if(!is_null($backMessage) && !is_string($backMessage)){
            throw new \InvalidArgumentException('back_message must be string or null');
        }
        $updated = $this -> omamoriRepository -> updateBackMessage($userId, $omamoriId, $backMessage);
        if(!$updated){
            throw new \RuntimeException('Update back message failed');
        }
        return $updated;
    }

    // 오마모리 임시 저장
    public function saveDraft(string $token, int $omamoriId): array{
        // 토큰 검증
        $auth = new AuthService();
        $userId = $auth -> verifyAndGetUserId($token);

        // 오마모리 확인
        $current = $this -> omamoriRepository -> findOwnById($userId, $omamoriId);
        if(!$current){
            throw new \RuntimeException('Omamori not found');
        }

        // draft 상태만 임시 저장 가능
        if(($current['status'] ?? null) !== 'draft'){
            throw new \RuntimeException('Omamori already published');
        }

        // updated_at만 갱신
        $updated = $this -> omamoriRepository -> touchDraft($userId, $omamoriId);
        if(!$updated){
            throw new \RuntimeException('Save draft failed');
        }
        return $updated;
    }
}