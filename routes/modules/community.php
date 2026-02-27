<?php

// import
use App\Modules\Community\Controllers\PostController;

global $router;

// 게시글 작성
$router -> post('/api/posts/', function($request){
    return (new PostController()) -> post($request);
}, 'omamoris.community.post');