<?php

namespace App\Modules\Custom\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\Custom\Repositories\FortuneColorRepository;
use App\Modules\Custom\Repositories\AdminFortuneColorRepository;


// 상속
class AdminFortuneColorService extends BaseService{
    protected FortuneColorRepository $fortuneColorRepository;
    protected AdminFortuneColorRepository $adminFortuneColorRepository;

    public function __construct(){
        $db = new Database();
        $this -> fortuneColorRepository = new FortuneColorRepository($db);
        $this -> adminFortuneColorRepository = new AdminFortuneColorRepository($db);
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
            $criteria['is_active'] = 1;
        }elseif($active === 'false'){
            $criteria['is_active'] = 0;
        }

        if(!empty($category)){
            $criteria['category'] = $category;
        }
        return $this -> fortuneColorRepository -> paginate($page, $size, $criteria);
    }


    // 생성(관리용)
    public function store(): array
    {
        $now = $this->now();

        $data = [
            'code' => 'temp-color-' . time(),
            'name' => 'Temp Color',
            'hex' => '#000000',
            'is_active' => true,
            'category' => null,
            'short_meaning' => null,
            'meaning' => null,
            'tips' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $id = $this ->adminFortuneColorRepository -> create($data);
        $row = $this ->adminFortuneColorRepository -> findById($id);

        if(!$row){
            throw new \RuntimeException('Fortune color create failed');
        }
        return $row;
    }


    // 수정(관리용)
    public function update(int $fortuneColorId): array{
        $fortuneColor = $this -> adminFortuneColorRepository -> findById($fortuneColorId);
        if(!$fortuneColor){
            throw new \RuntimeException('Fortune color not found');
        }

        $updateData = [
            'name' => 'Updated Temp Color',
            'hex' => '#111111',
            'is_active' => false,
            'updated_at' => $this->now(),
        ];

        $updated = $this -> adminFortuneColorRepository -> update($fortuneColorId, $updateData);
        if(!$updated){
            throw new \RuntimeException('Fortune color update failed');
        }

        $row = $this -> adminFortuneColorRepository -> findById($fortuneColorId);
        if(!$row){
            throw new \RuntimeException('Fortune color not found after update');
        }
        return $row;
    }
}