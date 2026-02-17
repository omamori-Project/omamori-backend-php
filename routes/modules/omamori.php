<?php

use App\Modules\Omamori\Controllers\OmamoriController;

global $router;

// 오마모리 생성
$router -> post('/api/omamoris', function($request){
    return (new OmamoriController()) -> store($request);
}, 'omamoris.store');