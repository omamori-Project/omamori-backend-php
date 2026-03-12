<?php

namespace App\Modules\Custom\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;


// 상속
class FrameRepository extends BaseRepository{
    // table명
    protected string $table = 'frames';
    protected string $primaryKey = 'id';

    public function __construct(Database $db){
        parent::__construct($db);
    }


    // 유저용 프레임 목록
    public function getFrames(?int $isActive, int $page = 1, int $size = 10): array{
        $criteria = [];
        if ($isActive !== null) {
            $criteria['is_active'] = $isActive;
        }
        return $this -> paginate($page, $size, $criteria);
    }
}