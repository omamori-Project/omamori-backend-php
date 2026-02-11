<?php

/**
 * User Module Routes
 * 
 * AltoRouter 패턴:
 * - [i:id] → 숫자만 허용
 */

use App\Modules\User\Controllers\UserController;

global $router;

// 사용자 목록
$router -> get('/api/users', function($request) {
    return (new UserController())->index($request);
}, 'users.index');

// 사용자 상세
$router -> get('/api/users/[i:id]', function($request){
    return (new UserController()) -> show($request);
}, 'users.show');

// 사용자 생성
$router -> post('/api/users', function($request){
    return (new UserController()) -> store($request);
}, 'users.store');

// // 사용자 수정
// $router -> put('/api/users/[i:id]', function($request){
//     return (new UserController()) -> update($request);
// }, 'users.update');

// // 사용자 삭제
// $router -> delete('/api/users/[i:id]', function($request){
//     return (new UserController()) -> destroy($request);
// }, 'users.destroy');
