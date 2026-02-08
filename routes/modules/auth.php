<?php

/**
 * Auth Module Routes
 */

use App\Core\Response;

global $router;

// 회원가입
$router->post('/api/auth/register', function ($request) {
    return Response::success(null, 'Register endpoint - Auth 모듈에서 구현 필요');
}, 'auth.register');

// 로그인
$router->post('/api/auth/login', function ($request) {
    return Response::success(null, 'Login endpoint - Auth 모듈에서 구현 필요');
}, 'auth.login');

// 로그아웃
$router->post('/api/auth/logout', function ($request) {
    return Response::success(null, 'Logout endpoint - Auth 모듈에서 구현 필요');
}, 'auth.logout');

// 비밀번호 재설정
$router->post('/api/auth/password/reset', function ($request) {
    return Response::success(null, 'Password reset - Auth 모듈에서 구현 필요');
}, 'auth.password.reset');
