<?php

use App\Modules\Public\Controllers\ShareController;

global $router;

// 외부 공유용 오마모리 조회
$router -> get('/api/public/shares/[*:token]', function($request){
    return (new ShareController()) -> show($request);
}, 'omamoris.shares.show');

// 공유 설정 수정
$router -> patch('/api/shares/[i:shareId]', function($request){
    return (new ShareController()) -> update($request);
}, 'omamoris.shares.update');