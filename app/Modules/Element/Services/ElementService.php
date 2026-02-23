<?php

namespace App\Modules\Element\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\Auth\Services\AuthService;
use App\Modules\File\Repositories\FileRepository;
use App\Modules\Omamori\Repositories\OmamoriRepository;
use App\Modules\Element\Repositories\ElementRepository;


// 상속
class ElementService extends BaseService{
    protected FileRepository $fileRepository;
    protected OmamoriRepository $omamoriRepository;
    protected ElementRepository $elementRepository;

    public function __construct()
    {
        $db = new Database();
        $this -> fileRepository = new FileRepository($db);
        $this -> omamoriRepository = new OmamoriRepository($db);
        $this -> elementRepository = new ElementRepository($db);
    }


    // 오마모리 요소 추가
    public function createElement(string $token, int $omamoriId, array $input): array{
        // 토큰 검증
        $auth = new AuthService();
        $userId = $auth -> verifyAndGetUserId($token);

        // 오마모리 존재/소유자 확인
        $omamori = $this -> omamoriRepository -> findOwnById($userId, $omamoriId);
        if(!$omamori){
            throw new \RuntimeException('Omamori not found or not allowed');
        }

        // background upsert
        $type = $input['type'] ?? null;
        if($type === 'background'){
            $props = $input['props'] ?? null;

            if(!is_array($props)){
                throw new \InvalidArgumentException('props required');
            }

            if(!isset($props['fortune_color_id'])){
                throw new \InvalidArgumentException('fortune_color_id required');
            }

            $fortuneColorId = (int)$props['fortune_color_id'];
            if($fortuneColorId <= 0){
                throw new \InvalidArgumentException('Invalid fortune_color_id');
            }

            if(!$this -> omamoriRepository -> existsActiveFortuneColor($fortuneColorId)){
                throw new \InvalidArgumentException('Fortune color not found');
            }

            $updated = $this -> omamoriRepository -> updateAppliedFortuneColor($userId, $omamoriId, $fortuneColorId);

            return [
                'omamori_id' => (int)$omamoriId,
                'type' => 'background',
                'applied_fortune_color_id' => (int)$updated['applied_fortune_color_id'],
                'updated_at' => $updated['updated_at'],
            ];
        }

        

        // 필수값 체크
        if(!isset($input['type']) || !isset($input['layer']) || !isset($input['props']) || !isset($input['transform'])){
            throw new \InvalidArgumentException('Invalid input');
        }

        // 오마모리 요소
        $type = $input['type'];
        $layer = $input['layer'];
        $props = $input['props'];
        $transform = $input['transform'];

        if(!is_array($props) || !is_array($transform)){
            throw new \InvalidArgumentException('props and transform must be object');
        }

        if($type === 'stamp'){
            if(!isset($props['asset_key'])){
                throw new \InvalidArgumentException('asset_key required');
            }

            $file = $this -> fileRepository -> findByFileKey($props['asset_key']);
            if(!$file){
                throw new \RuntimeException('Invalid asset_key');
            }
        }

        // 저장
        $result = $this -> elementRepository -> insert($omamoriId, $type, $layer, $props, $transform);

        // DB에서 돌아온 Json을 배열에 톨린다
        $result['props'] = is_string($result['props']) ? json_decode($result['props'], true) : $result['props'];
        $result['transform'] = is_string($result['transform']) ? json_decode($result['transform'], true) : $result['transform'];
        return $result;

    }
}