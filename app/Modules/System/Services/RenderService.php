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


    // 레이어 합성
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
        // 이미지 생성
        $filePath = $this -> renderImage($data, $renderCode);

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
            'file_path' => $filePath,
            'side' => $side,
            'format' => $format,
            'dpi' => $dpi,
            'width' => $width,
            'height' => $height,
            'store' => $store,
        ];
    }


    // 이미지 생성
    protected function renderImage(array $data, string $renderCode): string{
        $width = (int)$data['canvas']['width'];
        $height = (int)$data['canvas']['height'];
        $layers = $data['layers'];

        $image = imagecreatetruecolor($width, $height);

        // ひとまず白背景
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $white);

        usort($layers, function ($a, $b) {
            return ($a['layer'] ?? 0) <=> ($b['layer'] ?? 0);
        });

        foreach($layers as $layer){
            $type = $layer['type'] ?? '';
            if ($type === 'background') {
                $this -> drawBackground($image, $layer, $width, $height);
            }
            if ($type === 'text') {
                $this -> drawText($image, $layer);
            }
        }

        $dir = __DIR__ . '/../../../../storage/renders';
        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }

        $filename = 'render_' . time() . '_' . mt_rand(1000, 9999) . '.png';
        $filePath = $dir . '/' . $filename;

        imagepng($image, $filePath);
        imagedestroy($image);
        return $filePath;
    }


    // 배경 생성
    protected function drawBackground($image, array $layer, int $width, int $height): void{
        $colorHex = $layer['color'] ?? '#ffffff';
        [$r, $g, $b] = $this -> hexToRgb($colorHex);

        $color = imagecolorallocate($image, $r, $g, $b);
        imagefilledrectangle($image, 0, 0, $width, $height, $color);
    }


    // 문자 생성
    protected function drawText($image, array $layer): void{
        $text = $layer['content'] ?? '';
        if ($text === '') {
            return;
        }

        $x = (int)($layer['transform']['x'] ?? 0);
        $y = (int)($layer['transform']['y'] ?? 30);

        $fontSize = (int)($layer['style']['fontSize'] ?? 20);
        $colorHex = $layer['style']['color'] ?? '#111111';
        [$r, $g, $b] = $this -> hexToRgb($colorHex);

        $color = imagecolorallocate($image, $r, $g, $b);

        // 자기 환경에 맞게 폰트를 두기
        $fontPath = __DIR__ . '/../../../../storage/fonts/NotoSansJP-Regular.ttf';

        if (!file_exists($fontPath)) {
            return;
        }

        imagettftext(
            $image,
            $fontSize,
            0,
            $x,
            $y,
            $color,
            $fontPath,
            $text
        );
    }


    // 색코드를 RGB 숫자로 바꾸기
    protected function hexToRgb(string $hex): array{
        $hex = ltrim($hex, '#');

        if(strlen($hex) === 3){
            $hex = $hex[0] . $hex[0]
                 . $hex[1] . $hex[1]
                 . $hex[2] . $hex[2];
        }
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }


    // 공유 코드
    protected function generateRenderCode(): string{
        return 'rnd_' . bin2hex(random_bytes(8));
    }


    // 렌더 결과 조회
    public function showRender(string $renderCode): array{
        if($renderCode === ''){
            throw new \InvalidArgumentException('renderCode is required.');
        }

        $render = $this -> renderRepository -> findByRenderCode($renderCode);
        if (!$render) {
            throw new \InvalidArgumentException('Render not found.');
        }
        return [
            'id' => $render['id'],
            'render_code' => $render['render_code'],
            'user_id' => $render['user_id'],
            'omamori_id' => $render['omamori_id'],
            'side' => $render['side'],
            'format' => $render['format'],
            'dpi' => $render['dpi'],
            'width' => $render['width'],
            'height' => $render['height'],
            'store' => $render['store'],
            'file_id' => $render['file_id'],
            'expires_at' => $render['expires_at'],
            'created_at' => $render['created_at'] ?? null,
            'updated_at' => $render['updated_at'] ?? null,
        ];
    }


    // 내 렌더 히스토리
    public function getMyRenders(int $page = 1, int $perPage = 15): array{
        // page
        if ($page <= 0) {
            $page = 1;
        }
        if ($perPage <= 0) {
            $perPage = 15;
        }

        // 임시: 현재는 user_id 0 기준
        $userId = 0;
        return $this -> renderRepository -> findMyRenders($userId, $page, $perPage);
    }


    // 렌더 결과 삭제
    public function deleteRender(string $renderCode): bool{
        if ($renderCode === '') {
            throw new \InvalidArgumentException('renderCode is required.');
        }

        $render = $this -> renderRepository -> findByRenderCode($renderCode);
        if (!$render) {
            throw new \InvalidArgumentException('Render not found.');
        }
        return $this -> renderRepository -> deleteByRenderCode($renderCode);
    }
}