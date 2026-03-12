<?php

namespace App\Modules\Custom\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\Custom\Repositories\FrameRepository;
use App\Modules\Omamori\Repositories\OmamoriRepository;


// 상속
class FrameService extends BaseService{
    protected FrameRepository $frameRepository;
    protected OmamoriRepository $omamoriRepository;

    public function __construct(){
        $db = new Database();
        $this -> frameRepository = new FrameRepository($db);
        $this -> omamoriRepository = new OmamoriRepository($db);
    }

    // 유저용 프레임 목록
    public function getFrames(array $query): array{
        $isActive = null;
        if (isset($query['isActive']) && $query['isActive'] !== '') {
            $isActive = (int) $query['isActive'];
        }

        // pagenation
        $page = isset($query['page']) ? max(1, (int) $query['page']) : 1;
        $size = isset($query['size']) ? max(1, (int) $query['size']) : 10;
        return $this -> frameRepository -> getFrames($isActive, $page, $size);
    }


    // 프레임 적용
    public function applyFrame(int $omamoriId, array $data): bool{
        $this -> validateRequired($data, ['frameKey']);
        $frameKey = $data['frameKey'];

        $frame = $this -> frameRepository -> findByFrameKey($frameKey);
        if (!$frame) {
            throw new \InvalidArgumentException('Frame not found');
        }
        return $this -> omamoriRepository -> applyFrame($omamoriId, (int) $frame['id']);
    }


    // 프레임 적용 해제
    public function removeFrame(int $omamoriId): bool{
        return $this -> omamoriRepository -> removeFrame($omamoriId);
    }
}