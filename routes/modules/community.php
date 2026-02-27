<?php

// import
use App\Modules\Community\Controllers\PostController;

global $router;

// 게시글 작성
$router -> post('/api/posts', function($request){
    return (new PostController()) -> store($request);
}, 'omamoris.community.store');

// 전체 게시글 목록 조회 (공개 피드)
$route -> get('/api/posts', function($request){
    return (new PostController()) -> show($request);
}, 'omamoris.community.show');