<?php
// Auth Module Routes

use App\Core\Response;

global $router;

// 회원가입
$router -> post('/api/auth/register', 'App\Modules\Auth\Controllers\AuthController@register');

// 로그인
$router -> post('/api/auth/login', 'App\Modules\Auth\Controllers\AuthController@login');

// 로그아웃
$router -> post('/api/auth/logout', 'App\Modules\Auth\Controllers\AuthController@logout');

// 내 정보 조회
$router -> get ('/api/me', 'App\Modules\User\Controllers\UserController@showMe');

// 내 정보 수정
$router -> patch('/api/me', 'App\Modules\User\Controllers\UserController@updateMe');

// 회원 탈퇴
$router -> delete('/api/me', 'App\Modules\User\Controllers\UserController@deleteMe');