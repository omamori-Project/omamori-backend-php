<?php

use App\Modules\Custom\Controllers\FortuneColorController;
use App\Modules\Custom\Controllers\AdminFortuneColorController;

global $router;

// 생년월일 기반 1회성 결과
$router -> post('/api/fortune-colors/today', function($request){
    return (new FortuneColorController()) -> today($request);
}, 'omamoris.fortune-colors.today');

// 행운컬러 목록
$router -> get('/api/fortune-colors', function($request){
    return (new FortuneColorController()) -> index($request);
}, 'omamoris.fortune-colors.index');

// 행운컬러 단건 조회
$router -> get('/api/fortune-colors/[i:fortuneColorId]', function($request){
    return (new FortuneColorController()) -> show($request);
}, 'omamoris.fortune-colors.show');

// 테마 적용/변경/해제
$router -> patch('/api/me/theme', function($request){
    return (new FortuneColorController()) -> updateTheme($request);
}, 'omamoris.fortune-colors.updateTheme');

// 목록(관리용)
$router -> get('/api/admin/fortune-colors', function($request){
    return (new AdminFortuneColorController()) -> index($request);
}, 'omamoris.admin.fortune-colors.index');

// 생성(관리용)
$router -> post('/api/admin/fortune-colors', function($request){
    return (new AdminFortuneColorController()) -> create($request);
}, 'omamoris.admin.fortune-colors.create');

// 수정(UI 수정)
$router -> patch('/api/admin/fortune-colors/[i:fortuneColorId]', function($request){
    return (new AdminFortuneColorController()) -> update($request);
}, 'omamoris.admin.fortune-colors.update');