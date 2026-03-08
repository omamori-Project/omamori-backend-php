<?php

// import
use App\Modules\Community\Controllers\PostController;
use App\Modules\Community\Controllers\CommentController;
use App\Modules\Community\Controllers\LikeController;

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

// 댓글 작성
$router -> post('/api/posts/[i:postId]/comments', function($request){
    return (new CommentController()) -> create($request);
}, 'omamoris.comment.create');

// 댓글 목록
$router -> get('/api/me/comments', function($request){
    return (new CommentController()) -> list($request);
}, 'omamoris.comment.list');

// 댓글 수정
$router -> patch('/api/comments/[i:commentId]', function($request){
    return (new CommentController()) -> update($request);
}, 'omamoris.comment.update');

// 댓글 삭제
$router -> delete('/api/comments/[i:commentId]', function($request){
    return (new CommentController()) -> commentDestroy($request);
}, 'omamoris.comment.destroy');

// 답글 작성
$router -> post('/api/comments/[i:commentId]/replies', function($request){
    return (new CommentController()) -> reply($request);
}, 'omamoris.comment.reply');

// 내 댓글/답글 목록
$router -> get('/api/me/comments', function($request){
    return (new CommentController()) -> myCommentList($request);
}, 'omamoris.comment.myCommentList');

// 좋아요 추가
$router -> post('/api/posts/[i:post]/likes', function($request){
    return (new LikeController()) -> create($request);
}, 'omamoris.likes.create');

// 좋아요 취소
$router -> delete('/api/posts/[i:post]/likes', function($request){
    return (new LikeController()) -> destroy($request);
}, 'omamoris.likes.destroy');

// 좋아요 여부 조회
$router -> get('/api/posts/[i:post]/likes/me', function($request){
    return (new LikeController()) -> show($request);
}, 'omamoris.likes.show');