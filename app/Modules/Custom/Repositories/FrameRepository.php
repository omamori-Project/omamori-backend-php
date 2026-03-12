<?php

namespace App\Modules\Custom\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;
use App\Modules\Custom\Repositories\FrameRepository;


// 상속
class FrameRepository extends BaseRepository{
    // table명
    protected string $table = 'frames';
    protected string $primaryKey = 'id';

    public function __construct(Database $db){
        $this -> frameRepository = new FrameRepository(new Database());
    }


    // 유저용 프레임 목록
    public function getFrames(?int $isActive, int $page = 1, int $size = 10): array{
        $criteria = [];
        if ($isActive !== null) {
            $criteria['is_active'] = $isActive;
        }
        return $this -> paginate($page, $size, $criteria);
    }


    // 프레임 적용
    public function findByFrameKey(string $frameKey): ?array{
        return $this -> findOneBy(['frame_key' => $frameKey]);
    }


    // 프레임 적용 해제
    public function removeFrame(int $omamoriId): bool{
        return $this -> update($omamoriId, [
            'applied_frame_id' => null,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}