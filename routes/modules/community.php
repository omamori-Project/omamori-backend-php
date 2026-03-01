<?php

// import
use App\Modules\Community\Controllers\PostController;

global $router;

// 게시글 작성
$router -> post('/api/posts', function($request){
    return (new PostController()) -> store($request);
}, 'omamoris.community.store');

// 전체 게시글 목록 조회 (공개 피드)
$router -> get('/api/posts', function($request){
    return (new PostController()) -> index($request);
}, 'omamoris.community.index');

// 상세 조회
$router -> get('/api/posts/[i:postId]', function($request){
    return (new PostController()) -> show($request);
}, 'omamoris.community.show');

// 특정 유저 게시글 목록 조회
$router -> get('/api/users/[i:userId]/posts', function($request){
    return (new PostController()) -> indexByUser($request);
}, 'omamoris.community.');