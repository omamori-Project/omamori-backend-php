<?php

/**
 * User Module Routes
 * 
 * AltoRouter 패턴:
 * - [i:id] → 숫자만 허용
 */

global $router;

// 사용자 목록
$router->get('/api/users', 'App\Modules\User\Controllers\UserController@index', 'users.index');

// 사용자 상세
$router->get('/api/users/[i:id]', 'App\Modules\User\Controllers\UserController@show', 'users.show');

// 사용자 생성
$router->post('/api/users', 'App\Modules\User\Controllers\UserController@store', 'users.store');

// 사용자 수정
$router->put('/api/users/[i:id]', 'App\Modules\User\Controllers\UserController@update', 'users.update');

// 사용자 삭제
$router->delete('/api/users/[i:id]', 'App\Modules\User\Controllers\UserController@destroy', 'users.destroy');
