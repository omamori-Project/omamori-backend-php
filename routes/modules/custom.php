<?php

use App\Modules\Custom\Controllers\FortuneColorController;

global $router;

// 생년월일 기반 1회성 결과
$router -> post('/api/fortune-colors/today', function($request){
    return (new FortuneColorController()) -> today($request);
}, 'omamoris.fortune-colors.today');

// 행운컬러 목록
$router -> get('/api/fortune-colors', function($request){
    return (new FortuneColorController()) -> index($request);
}, 'omamoris.fortune-colors.index');

// 행운컬러 단건 조회
$router -> get('/api/fortune-colors/[i:fortuneColorId]', function($request){
    return (new FortuneColorController()) -> show($request);
}, 'omamoris.fortune-colors.show');

// 테마 적용/변경
$router -> patch('/api/me/them', function($request){
    return (new FortuneColorController()) -> updateThem($request);
}, 'omamoris.fortune-colors.updateThem');