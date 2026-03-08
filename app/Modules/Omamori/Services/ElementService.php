<?php

namespace App\Modules\Omamori\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\Auth\Services\AuthService;
use App\Modules\Omamori\Repositories\FileRepository;
use App\Modules\Omamori\Repositories\OmamoriRepository;
use App\Modules\Omamori\Repositories\ElementRepository;


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


    // 오마모리 요소 재정렬 (background 제외)
    public function reorderElements(string $token, int $omamoriId, array $input): array{
        // 토큰 검증
        $auth = new AuthService();
        $userId = $auth -> verifyAndGetUserId($token);

        // 오마모리 존재/소유자 확인
        $omamori = $this -> omamoriRepository -> findOwnById($userId, $omamoriId);
        if(!$omamori){
            throw new \RuntimeException('Omamori not found or not allowed');
        }

        // Element 받기
        $elementIds = $input['elementIds'] ?? null;
        if (!is_array($elementIds) || empty($elementIds)){
            throw new \InvalidArgumentException('elementIds required');
        }
        if (count($elementIds) !== count(array_unique($elementIds))){
            throw new \InvalidArgumentException('elementIds duplicated');
        }

        // 이 오마모리의 요소인지 확인 (soft delete 제외)
        $row = $this -> elementRepository -> getElementsById($omamoriId, $elementIds);
        if(count($row) !== count($elementIds)){
            throw new \RuntimeException('Element not found or not allowed');
        }

        // background 포함 금지
        foreach($row as $r){
            if(($r['type'] ?? null) === 'background'){
                throw new \InvalidArgumentException('Background can not be reordered');
            }
        }

        // 누락 방지
        $allNonBgIds = $this -> elementRepository -> findNonBackgroundIdsByOmamoriId($omamoriId);
        $req = array_map('intval', $elementIds);
        $all = array_map('intval', $allNonBgIds);
        sort($req);
        sort($all);
        if($req !== $all){
            throw new \InvalidArgumentException('Invalid elementIds');
        }

        // 순서대로 layer 재부여
        $idToLayer = [];
        $layer = 1;
        foreach($elementIds as $id){
            $idToLayer[(int)$id] = $layer;
            $layer ++;
        }

        // 업데이트
        $updatedRows = $this -> elementRepository -> updateLayersBulk($omamoriId, $idToLayer);
        $this -> elementRepository -> setBackgroundLayerZero($omamoriId);
        return [
            'omamori_id' => (int)$omamoriId,
            'items' => $updatedRows
        ];
    }


    // 요소 수정
    public function updateElement(string $token, int $omamoriId, int $elementId, array $input): array{
        // 토큰 취득
        $auth = new AuthService();
        $userId = $auth -> verifyAndGetUserId($token);

        // 오마모리 취득
        $omamori = $this -> omamoriRepository -> findOwnById($userId, $omamoriId);
        if(!$omamori){
            throw new \RuntimeException('Omamori not found or not allowed');
        }
        
        // element 취득
        $element = $this -> elementRepository -> findElementById($elementId);
        if(!$element){
            throw new \RuntimeException('Element not found');
        }

        // 오마모리 요소 확인
        if((int)$element['omamori_id'] !== (int)$omamoriId){
            throw new \RuntimeException('Element not found or not allowed');
        }

        // soft delete 학인
        if(!empty($element['deleted_at'])){
            throw new \InvalidArgumentException('Element already deleted');
        }

        // background 제외
        if(($element['type'] ?? null) === 'background'){
            throw new \InvalidArgumentException('Background is not updatable');
        }

        // props/transform 둘 다 없어서는 안 됨
        $hasProps = array_key_exists('props', $input);
        $hasTransform = array_key_exists('transform', $input);
        if(!$hasProps && !$hasTransform){
            throw new \InvalidArgumentException('props or transform required');
        }

        // 타입 체크(있을 때만)
        if($hasProps && !is_array($input['props'])){
            throw new \InvalidArgumentException('props must be object');
        }
        if($hasTransform && !is_array($input['transform'])){
            throw new \InvalidArgumentException('transform must be object');
        }

        // 전부 변경
        $currentProps = $element['props'];
        $currentTransform = $element['transform'];

        $newPropsJson = $hasProps 
            ? json_encode($input['props'], JSON_UNESCAPED_UNICODE)
            : (is_string($currentProps)
                ? $currentProps
                : json_encode($currentProps, JSON_UNESCAPED_UNICODE));

        $newTransformJson = $hasTransform 
            ? json_encode($input['transform'], JSON_UNESCAPED_UNICODE)
            :(is_string($currentTransform) 
                ? $currentTransform 
                : json_encode($currentTransform, JSON_UNESCAPED_UNICODE));

        // 업데이트
        $updated = $this -> elementRepository -> updateElementPropsTransform($elementId, $newPropsJson, $newTransformJson);
        $updated['props'] = is_string($updated['props']) ? json_decode($updated['props'], true) : $updated['props'];
        $updated['transform'] = is_string($updated['transform']) ? json_decode($updated['transform'], true) : $updated['transform'];
        return $updated;
    }


     // 삭제 성공 후 layer 재정렬(1..N)
    private function normalizeLayers(int $omamoriId): void{
        $ids = $this -> elementRepository -> findActiveNonBackgroundIdsOrdered($omamoriId);
        $idToLayer = [];
        $layer = 1;
        foreach($ids as $id){
            $idToLayer[$id] = $layer;
            $layer ++;
        }

        $this -> elementRepository -> updateLayersBulk($omamoriId, $idToLayer);
        $this -> elementRepository -> setBackgroundLayerZero($omamoriId);
    }


    // 요소 삭제
    public function destroyElement(string $token, int $omamoriId, int $elementId): array{
        // 토큰 검증
        $auth = new AuthService();
        $userId = $auth -> verifyAndGetUserId($token);

        // 오마모리 존재 확인
        $omamori = $this -> omamoriRepository -> findOwnById($userId, $omamoriId);
        if(!$omamori){
            throw new \RuntimeException('Omamori not found or not allowed');
        }

        // 요소 존재 학인(소속 + 미삭제)
        $element = $this -> elementRepository -> findActiveElementByOmamoriId($omamoriId, $elementId);
        if(!$element){
            throw new \RuntimeException('Element not found or not allowed');
        }

        $deleted = $this -> elementRepository -> softDeleteElement($omamoriId, $elementId);
        if(!$deleted){
            throw new \RuntimeException('Element not found or not allowed');
        }        

        $this -> normalizeLayers($omamoriId);
        return $deleted;
    } 
}