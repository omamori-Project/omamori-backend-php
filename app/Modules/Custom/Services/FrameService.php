<?php

namespace App\Modules\Custom\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\Custom\Repositories\FrameRepository;


// 상속
class FrameService extends BaseService{
    protected FrameRepository $frameRepository;

    public function __construct(){
        $db = new Database();
        $this -> frameRepository = new FrameRepository($db);
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
}