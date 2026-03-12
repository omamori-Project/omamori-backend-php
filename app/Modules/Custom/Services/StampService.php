<?php

namespace App\Modules\Custom\Services;

// import
use App\Common\Base\BaseService;
use App\Modules\Custom\Repositories\StampRepository;


// 상속
class StampService extends BaseService{
    protected StampRepository $stampRepository;

    public function __construct()
    {
        $this -> stampRepository = new StampRepository();
    }

    // 스탬프 목록 조회 + 필터링
    public function getStampList(array $query): array{
        return $this -> stampRepository -> getList($query);
    }
}