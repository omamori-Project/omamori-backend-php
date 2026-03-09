<?php

use App\Modules\Public\Controllers\ShareController;

global $router;

// 외부 공유용 오마모리 조회
$router -> get('/api/public/shares/[:token]', function($request){
    return (new ShareController()) -> show($request);
}, 'omamoris.shares.show');

// 공유 설정 수정
$router -> patch('/api/shares/[i:shareId]', function($request){
    return (new ShareController()) -> update($request);
}, 'omamoris.shares.update');

// 미리보기 카드
$router -> get('/api/public/shares/[:token]/preview', function($request){
    return (new ShareController()) -> preview($request);
}, 'omamoris.shares.preview');

// 공유 링크 생성
$router -> post('/api/omamoris/[i:omamoriId]/share', function($request){
    return (new ShareController()) -> create($request);
}, 'omamoris.shares.create');

// 내보내기(다운로드 URL 반환)
$router -> post('/api/omamoris/[i:omamoriId]/export', function($request){
    return (new ShareController()) -> download($request);
}, 'omamoris.shares.download');