<?php

// import
use App\Modules\System\Controllers\RenderController;

global $router;


// 레이어 합성 → 이미지 생성
$router -> post('/api/render/omamori', function($request){
    return (new RenderController()) -> create($request);
}, 'omamoris.render.create');

// 렌더 결과 조회
$router -> get('/api/renders/[*:renderCode]', function($request){
    return (new RenderController()) -> show($request);
}, 'omamoris.render.show');