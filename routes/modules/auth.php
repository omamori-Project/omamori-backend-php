<?php

/**
 * Auth Module Routes
 */

use App\Core\Response;

global $router;

// 회원가입
$router -> post('/api/auth/register', function ($request) {
    return Response::success(null, 'Register endpoint');
}, 'auth.register');

// 로그인
$router -> post('/api/auth/login', function ($request) {
    return Response::success(null, 'Login endpoint');
}, 'auth.login');

// 로그아웃
$router -> post('/api/auth/logout', function ($request) {
    return Response::success(null, 'Logout endpoint');
}, 'auth.logout');

// 비밀번호 재설정
$router -> post('/api/auth/password/reset', function ($request) {
    return Response::success(null, 'Password reset');
}, 'auth.password.reset');

// 내 정보 조회
$router -> get ('/api/me', function($request){
    return Response:: success(null, 'Me(GET)');
}, 'me.show');

// 내 정보 수정
$router -> patch('/api/me', 'App\Modules\User\Controllers\UserController@updateMe');

// 회원 탈퇴
$router -> delete('/api/me', 'App\Modules\User\Controllers\UserController@deleteMe');