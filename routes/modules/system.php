<?php

// import
use App\Modules\System\Controllers\RenderController;

global $router;


// 레이어 합성 → 이미지 생성
$router -> post('/api/render/omamori', function($request){
    return (new RenderController()) -> create($request);
}, 'omamoris.render.create');