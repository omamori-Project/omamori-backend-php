<?php

namespace App\Modules\Custom\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\Custom\Repositories\FortuneColorRepository;


// 상속
class AdminFortuneColorService extends BaseService{
    protected FortuneColorRepository $fortuneColorRepository;

    public function __construct(){
        $db = new Database();
        $this -> fortuneColorRepository = new FortuneColorRepository($db);
    }


    // 목록(관리용)
    public function index(array $query): array{
        $active = $query['active'] ?? 'all';
        $category = $query['category'] ?? null;
        $page = (int)($query['page'] ?? 1);
        $size = (int)($query['size'] ?? 10);

        if($page <= 0){
            $page = 1;
        }

        if($size <= 0){
            $size = 10;
        }

        $criteria = [];

        if($active === 'true'){
            $criteria['active'] = 1;
        }elseif($active === 'false'){
            $criteria['active'] = 0;
        }

        if(!empty($category)){
            $criteria['category'] = $category;
        }
        return $this -> fortuneColorRepository -> paginate($page, $size, $criteria);
    }
}