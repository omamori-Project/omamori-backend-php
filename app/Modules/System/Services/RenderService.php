<?php

namespace App\Modules\System\Services;

// import
use App\Common\Base\BaseService;
use App\Core\Database;
use App\Modules\System\Repositories\RenderRepository;


// 상속
class RenderService extends BaseService{
    protected RenderRepository $renderRepository;

    public function __construct(){
        $db = new Database();
        $this -> renderRepository = new RenderRepository($db);
    }

    // 레이어 합성 → 이미지 생성
    public function createRender(array $data): array{
        // 바깥 필수값 확인
        $this -> validateRequired($data, ['canvas', 'side', 'format', 'dpi', 'layers', 'store']);

        // canvas 확인
        if(!isset($data['canvas']['width']) || !isset($data['canvas']['height'])){
            throw new \InvalidArgumentException('canvas width and height are required.');
        }
        $width = (int)$data['canvas']['width'];
        $height = (int)$data['canvas']['height'];
        if($width <= 0 || $height <= 0){
            throw new \InvalidArgumentException('Invalid canvas size.');
        }

        // side 확인
        $side = $data['side'];
        if(!in_array($side, ['front', 'back'], true)){
            throw new \InvalidArgumentException('Invalid side value.');
        }

        // format 확인
        $format = $data['format'];
        if(!in_array($format, ['png', 'jpg', 'jpeg'], true)){
            throw new \InvalidArgumentException('Invalid format value.');
        }

        // dpi 확인
        $dpi = (int)$data['dpi'];
        if($dpi <= 0){
            throw new \InvalidArgumentException('Invalid dpi value.');
        }

        // layers 확인
        if(!is_array($data['layers']) || empty($data['layers'])){
            throw new \InvalidArgumentException('layers must be a non-empty array.');
        }

        // store 확인
        $store = $data['store'];
        if(!in_array($store, ['temp', 'permanent'], true)){
            throw new \InvalidArgumentException('Invalid store value.');
        }

        // render_code 생성
        $renderCode = $this -> generateRenderCode();

        // 임시로 file_id, user_id, omamori_id 는 나중에 연결
        $renderData = [
            'render_code' => $renderCode,
            'user_id' => 0,
            'omamori_id' => $data['omamori_id'] ?? null,
            'side' => $side,
            'format' => $format,
            'dpi' => $dpi,
            'width' => $width,
            'height' => $height,
            'store' => $store,
            'file_id' => null,
            'expires_at' => $store === 'temp' ? date('Y-m-d H:i:s', strtotime('+1 day')) : null,
            'created_at' => $this -> now(),
            'updated_at' => $this -> now(),
        ];

        $renderId = $this -> renderRepository -> create($renderData);
        return [
            'id' => $renderId,
            'render_code' => $renderCode,
            'side' => $side,
            'format' => $format,
            'dpi' => $dpi,
            'width' => $width,
            'height' => $height,
            'store' => $store,
        ];
    }


    // 공유 코드
    protected function generateRenderCode(): string{
        return 'rnd_' . bin2hex(random_bytes(8));
    }
}