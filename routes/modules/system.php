<?php

// import
use App\Modules\System\Controllers\RenderController;
use App\Modules\System\Controllers\FileController;

global $router;


// 레이어 합성 → 이미지 생성
$router -> post('/api/render/omamori', function($request){
    return (new RenderController()) -> create($request);
}, 'omamoris.render.create');

// 내 렌더 히스토리
$router -> get('/api/renders/me', function($request){
    return (new RenderController()) -> myHistory($request);
}, 'omamoris.render.myHistory');

// 렌더 결과 조회
$router -> get('/api/renders/[*:renderCode]', function($request){
    return (new RenderController()) -> show($request);
}, 'omamoris.render.show');

// 렌더 결과 삭제 (만료 전 수동 삭제)
$router -> delete('/api/renders/[*:renderCode]', function($request){
    return (new RenderController()) -> destroy($request);
}, 'omamoris.render.destroy');

// 파일 업로드 → url 반환
$router -> post('/api/files', function($request){
    return (new FileController()) -> upload($request);
}, 'omamoris.file.upload');

// 파일 삭제
$router -> delete('/api/files/[*:fileName]', function($request){
    return (new FileController()) -> destroy($request);
}, 'omamoris.file.destroy');