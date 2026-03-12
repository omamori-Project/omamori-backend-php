<?php

namespace App\Modules\Custom\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;


// 상속
class AdminFrameRepository extends BaseRepository{
    protected string $table = 'frames';
    protected string $primaryKey = 'id';

    public function __construct(){
        parent::__construct(new Database());
    }


    // 프레임 등록 (관리자)
    public function findByFileKey(string $fileKey): ?array{
        return $this -> findOneBy(['file_key' => $fileKey]);
    }
}