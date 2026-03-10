<?php

namespace App\Modules\Custom\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\Custom\Repositories\FortuneColorRepository;

// 상속
class FortuneColorService extends BaseService{
    protected Database $db;
    protected FortuneColorRepository $fortuneColorRepository;

    public function __construct(){
        $this -> db = new Database();
        $this -> fortuneColorRepository = new FortuneColorRepository($this -> db);
    }


    // 생년월일 기반 1회성 결과
    public function getTodayResult(array $input): array{
        // 입력 받기
        $data = $this -> only($input, ['birth_date']);
        $birthDate = $data['birth_date'] ?? null;
        if(!$birthDate){
            throw new \InvalidArgumentException('birth_date required');
        }

        // 색을 받기
        $colors = $this -> fortuneColorRepository->findActiveColors();
        if(empty($colors)){
            throw new \RuntimeException('No fortune colors');
        }

        // 계산
        [$y,$m,$d] = explode('-', $birthDate);
        $sum = (int)$y + (int)$m + (int)$d;
        $index = $sum % count($colors);
        // 결과 보내기
        return $colors[$index];
    }


    // 행운컬러 목록
    public function getList(int $page, int $size, ?string $sort = 'latest', ?string $category = null, bool $active = true): array{
        if($page < 1) $page = 1;
        if($size < 1) $size = 10;
        if($size > 50) $size = 50;

        if($sort !== null){
            $sort = trim($sort);

            if($sort === ''){
                $sort = 'latest';
            }

            if(!in_array($sort, ['latest', 'popular', 'name'], true)){
                throw new \InvalidArgumentException('Invalid sort');
            }
        }

        if($category !== null){
            $category = trim($category);
            if($category === ''){
                $category = null;
            }
        }

        $offset = ($page - 1) * $size;
        $items = $this -> fortuneColorRepository -> findList($size, $offset, $sort, $category, $active);
        $total = $this -> fortuneColorRepository -> countList($category, $active);
        return [
            'items' => $items,
            'meta' => [
                'page' => $page,
                'size' => $size,
                'total' => $total,
                'total_pages' => (int)ceil($total / $size),
            ],
        ];
    }


    // 행운컬러 단건 조회
    public function getById(int $fortuneColorId): array{
        // fortuneColorId 없으면 오류
        if($fortuneColorId <= 0){
            throw new \InvalidArgumentException('Invalid fortuneColorId');
        }

        $row = $this -> fortuneColorRepository -> findOneActiveById($fortuneColorId);
        if(!$row){
            throw new \RuntimeException('Fortune color not found');
        }
        return $row;
    }
}