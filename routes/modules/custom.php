<?php

use App\Modules\Omamori\Controllers\FortuneColorController;

global $router;

// 생년월일 기반 1회성 결과
$router -> post('/api/fortune-colors/today', function($request){
    return (new FortuneColorController()) -> today($request);
}, 'omamoris.fortune-colors.today');