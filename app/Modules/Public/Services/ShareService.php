<?php

namespace App\Modules\Public\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\Public\Repositories\ShareRepository;


// 상속
class ShareService extends BaseService{
    protected ShareRepository $shareRepository;

    public function __construct(){
        $db = new Database();
        $this -> shareRepository = new ShareRepository($db);
    }

    // 외부 공유용 오마모리 조회
    public function showByToken(string $token): array{
        $share = $this-> getValidShareByCode($token);

        $omamori = $this -> shareRepository -> findOmamoriById((int)$share['omamori_id']);
        if(!$omamori){
            throw new \Exception('Omamori not found');
        }
        return $omamori;
    }


    // 공유 설정 수정
    public function updateShare(int $shareId): array{
        $share = $this -> shareRepository -> findById($shareId);
        if(!$share){
            throw new \Exception('Share not found');
        }

        $current = filter_var($share['is_public'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        $current = $current ?? false;

        $updateData = ['is_public' => $current ? 'false' : 'true'];

        $updated = $this -> shareRepository -> updateShare($shareId, $updateData);
        if(!$updated){
            throw new \Exception('Failed to update share');
        }
        return $this -> shareRepository -> findById($shareId);
    }


    // 미리보기 카드
    public function preview(string $token): array{
        $share = $this -> getValidShareByCode($token);

        $omamori = $this -> shareRepository -> findOmamoriById((int)$share['omamori_id']);
        if(!$omamori){
            throw new \Exception('Omamori not found');
        }

        return [
            'id' => $omamori['id'],
            'title' => $omamori['title'],
        ];
    }


    // 공유 링크 생성
    public function createShare(int $omamoriId, array $data): array{
        $this -> validateRequired($data, ['option', 'expires_at']);

        // 오마모리 확인
        $omamori = $this -> shareRepository -> findOmamoriById($omamoriId);
        if(!$omamori){
            throw new \Exception('Omamori not found');
        }

        // 공유 코드 생성
        $shareCode = bin2hex(random_bytes(16));
        
        $shareId = $this -> shareRepository -> createShare([
            'omamori_id' => $omamoriId,
            'share_code' => $shareCode,
            'option' => $data['option'],
            'expires_at' => $data['expires_at'],
        ]);

        // 링크 만들기
        $share = $this -> shareRepository -> findById($shareId);
        if(!$share){
            throw new \Exception('Failed to create share');
        }
        return $share;
    }


    // 공통 검증
    private function getValidShareByCode(string $shareCode): array{
        $share = $this -> shareRepository -> findByShareCode($shareCode);
        if(!$share){
            throw new \Exception('Share not found');
        }

        if(!(bool)$share['is_public']){
            throw new \Exception('Share is not public');
        }

        if(!empty($share['revoked_at'])){
            throw new \Exception('Share has been revoked');
        }

        if(!empty($share['expires_at']) && strtotime($share['expires_at']) < time()){
            throw new \Exception('Share has expired');
        }
        return $share;
    }


    // 내보내기(다운로드 URL 반환)
    public function exportOmamori(int $omamoriId, array $options): array{
        // 오마모리 존제 확인
        $omamori = $this -> shareRepository -> findOmamoriById($omamoriId);
        if (!$omamori) {
            throw new \Exception('Omamori not found');
        }

        // URL
        $downloadUrl = "/downloads/omamori_{$omamoriId}.png";
        return ['download_url' => $downloadUrl];
    }
}