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
}