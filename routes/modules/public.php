<?php

use App\Modules\Public\Controller\ShareController;

global $router;

// 외부 공유용 오마모리 조회
$router -> get('/api/public/shares/[i:token]', function($request){
    return (new ShareController()) -> show($request);
}, 'omamoris.shares.show');