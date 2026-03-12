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


    // 프레임 수정 (관리자)
    public function updateFrame(int $frameId, array $data): array{
        $frame = $this -> frameRepository -> findById($frameId);

        if (!$frame) {
            throw new \Exception('Frame not found');
        }

        // 요청값 중 수정 가능한 항목만 추출
        $updateData = $this -> only($data, [
            'name',
            'frameKey',
            'previewUrl',
            'isActive',
            'meta'
        ]);

        // API 필드명을 DB 컬럼명으로 변경
        if (isset($updateData['frameKey'])) {
            $updateData['frame_key'] = $updateData['frameKey'];
            unset($updateData['frameKey']);
        }

        if (isset($updateData['previewUrl'])) {
            $updateData['preview_url'] = $updateData['previewUrl'];
            unset($updateData['previewUrl']);
        }

        if (isset($updateData['isActive'])) {
            $updateData['is_active'] = $updateData['isActive'];
            unset($updateData['isActive']);
        }

        if (isset($updateData['meta'])) {
            $updateData['meta'] = json_encode($updateData['meta']);
        }

        // 수정할 값이 없으면 기존 데이터 반환
        if (empty($updateData)) {
            $result = $frame;
        } else {
            $updateData['updated_at'] = $this -> now();

            $this -> frameRepository -> update($frameId, $updateData);

            $result = $this -> frameRepository -> findById($frameId) ?? [];
        }

        // meta를 보기 좋게 다시 배열로 변환
        if ($result && isset($result['meta']) && is_string($result['meta'])) {
            $decoded = json_decode($result['meta'], true);
            $result['meta'] = $decoded ?? $result['meta'];
        }
        return $result;
    }
}