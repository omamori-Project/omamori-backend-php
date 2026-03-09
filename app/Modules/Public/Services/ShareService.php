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
        $share = $this -> shareRepository -> findByShareCode($token);
        if (!$share) {
            throw new \Exception('Share not found');
        }

        if (!(bool)$share['is_public']) {
            throw new \Exception('Share is not public');
        }

        if (!empty($share['revoked_at'])) {
            throw new \Exception('Share has been revoked');
        }

        if (!empty($share['expires_at']) && strtotime($share['expires_at']) < time()) {
            throw new \Exception('Share has expired');
        }

        $omamori = $this -> shareRepository -> findOmamoriById((int)$share['omamori_id']);
        if (!$omamori) {
            throw new \Exception('Omamori not found');
        }
        return $omamori;
    }


    // 공유 설정 수정
    public function updateShare(int $shareId, array $data): array{
        $share = $this -> shareRepository -> findById($shareId);
        if (!$share) {
            throw new \Exception('Share not found');
        }

        $updateData = [
            'is_active' => isset($data['is_active'])
                ? (bool)$data['is_active']
                : (bool)$share['is_active']
        ];

        $updated = $this -> shareRepository -> updateShare($shareId, $updateData);
        if (!$updated) {
            throw new \Exception('Failed to update share');
        }
        return $this -> shareRepository -> findById($shareId);
    }
}