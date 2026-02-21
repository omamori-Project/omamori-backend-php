<?php

use App\Modules\Omamori\Controllers\OmamoriController;

global $router;

// 오마모리 생성
$router -> post('/api/omamoris', function($request){
    return (new OmamoriController()) -> store($request);
}, 'omamoris.store');

// 오미모리 복제
$router -> post('/api/omamoris/[i:omamoriId]/duplicate', function($request){
    return (new OmamoriController())-> duplicate($request);
}, 'omamoris.duplicate');

// 오마모리 조회(편집/확인)
$router -> get('/api/omamoris/[i:omamoriId]', function($request){
    return (new OmamoriController()) -> show($request);
}, 'omamoris.show');

// 오마모리 내 목록
$router -> get('/api/omamoris', function($request){
    return (new OmamoriController()) -> index($request);
}, 'omamoris.index');

// 오마모리 최종 공개(draft -> published)
$router -> post('/api/omamoris/[i:omamoriId]/publish', function($request){
    return (new OmamoriController()) -> publish($request);
}, 'omamoris.publish');

// 오마모리 정보 수정
$router -> patch('/api/omamoris/[i:omamoriId]', function($request){
    return (new OmamoriController()) -> update($request);
}, 'omamoris.update');

// 오마모리 삭제
$router -> delete('/api/omamoris/[i:omamoriId]', function($request){
    return (new OmamoriController()) -> destroy($request);
}, 'omamoris.destroy');