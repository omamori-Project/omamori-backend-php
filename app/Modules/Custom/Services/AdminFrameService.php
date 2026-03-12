<?php

namespace App\Modules\Custom\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\Custom\Repositories\FrameRepository;
use App\Modules\Omamori\Repositories\FileRepository;


// 상속
class AdminFrameService extends BaseService{
    protected FrameRepository $frameRepository;
    protected FileRepository $fileRepository;

    public function __construct(){
        $db = new Database();
        $this -> frameRepository = new FrameRepository($db);
        $this -> fileRepository = new FileRepository($db);
    }


    // 프레임 등록 (관리자)
    public function createFrame(array $data): array{
        $this -> validateRequired($data, [
            'name',
            'frameKey',
            'previewUrl',
            'assetUrl'
        ]);

        $file = $this -> fileRepository -> findByFileKey($data['frameKey']);
        if ($file) {
            $fileId = $file['id'];
        } else {
            $fileId = $this -> fileRepository -> create([
                'user_id' => 1,
                'purpose' => 'frame',
                'visibility' => 'public',
                'file_key' => $data['frameKey'],
                'url' => $data['assetUrl'],
                'created_at' => $this -> now(),
            ]);
        }

        $frameId = $this -> frameRepository -> create([
            'name' => $data['name'],
            'frame_key' => $data['frameKey'],
            'preview_url' => $data['previewUrl'],
            'asset_file_id' => $fileId,
            'is_active' => $data['isActive'] ?? true,
            'meta' => isset($data['meta']) ? json_encode($data['meta']) : null,
            'created_at' => $this -> now(),
            'updated_at' => $this -> now(),
        ]);

        $result = $this -> frameRepository -> findById($frameId) ?? [];
        if ($result && isset($result['meta'])) {
            $result['meta'] = json_decode($result['meta'], true);
        }
        return $result;
    }
}