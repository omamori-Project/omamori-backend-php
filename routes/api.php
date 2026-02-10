<?php

/**
 * API Routes
 * 
 * AltoRouter 사용
 * 
 * 라우트 패턴:
 * - [i:id]      → 숫자 파라미터
 * - [a:name]    → 문자 파라미터
 * - [*:path]    → 모든 문자
 * - [slug:slug] → 슬러그 (a-z0-9-)
 */

use App\Core\Response;

global $router;

// Health Check Route

$router->get('/api/health', function ($request) {
    return Response::success([
        'status' => 'ok',
        'timestamp' => time(),
        'php_version' => PHP_VERSION,
        'architecture' => 'Modular with AltoRouter'
    ], 'Service is healthy');
});

// 존재 확인
$moduleFiles = ['user.php', 'auth.php', 'omamori.php', 'community.php'];

foreach ($moduleFiles as $file) {
    $path = __DIR__ . "/modules/{$file}";
    if (file_exists($path)) {
        require $path;
    }
}