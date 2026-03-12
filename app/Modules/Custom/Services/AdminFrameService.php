<?php

namespace App\Modules\Custom\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\Custom\Repositories\FrameRepository;


// 상속
class AdminFrameService extends BaseService{
    protected FrameRepository $frameRepository;

    public function __construct(){
        $db = new Database();
        $this -> frameRepository = new FrameRepository($db);
    }


    // 프레임 등록 (관리자)
    public function createFrame(array $data): array{
        $this -> validateRequired($data, [
            'name',
            'frameKey',
            'previewUrl',
            'assetUrl'
        ]);

        $frameData = [
            'name' => $data['name'],
            'frame_key' => $data['frameKey'],
            'preview_url' => $data['previewUrl'],
            'asset_url' => $data['assetUrl'],
            'is_active' => $data['isActive'] ?? true,
            'meta' => isset($data['meta']) ? json_encode($data['meta']) : null,
            'created_at' => $this -> now(),
            'updated_at' => $this -> now(),
        ];

        $frameId = $this -> frameRepository -> create($frameData);

        $result = $this -> frameRepository -> findById($frameId);
        if ($result && isset($result['meta']) && is_string($result['meta'])) {
            $decoded = json_decode($result['meta'], true);
            $result['meta'] = $decoded ?? $result['meta'];
        }
        return $result ?? [];
    }
}