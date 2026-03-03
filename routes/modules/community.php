<?php

// import
use App\Modules\Community\Controllers\PostController;
use App\Modules\Community\Controllers\CommentController;

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
}, 'omamoris.community.indexByUser');

// 게시글 수정
$router -> patch('/api/posts/[i:postId]', function($request){
    return (new PostController()) -> update($request);
}, 'omamoris.community.update');

// 게시글 삭제
$router -> delete('/api/posts/[i:postId]', function($request){
    return (new PostController()) -> destroy($request);
}, 'omamoris.community.destroy');

// 내 게시글 목록 조회
$router -> get('/api/me/posts', function($request){
    return (new PostController()) -> indexByMe($request);
}, 'omamoris.community.indexByMe');

// 댓글 조회
$router -> get('/api/posts/[i:postId]/comments', function($request){
    return (new CommentController()) -> index($request);
}, 'omamoris.comment.show');