<?php

use App\Modules\Community\Controllers\CommunityController;

global $router;

// 게시글 작성
$router -> post('/api/post/', function($request){
    return (new CommunityController()) -> post($request);
}, 'omamoris.community.post');