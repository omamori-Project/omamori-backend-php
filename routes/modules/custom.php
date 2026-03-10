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