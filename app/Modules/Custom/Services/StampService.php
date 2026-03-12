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

    public function getStampList(array $query): array{
        return $this -> stampRepository -> getList($query);
    }
}