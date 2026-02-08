<?php

namespace App\Core;

use AltoRouter;
use App\Common\Exceptions\ErrorHandler;

/**
 * Router
 * 
 * AltoRouter 기반 라우팅 시스템
 * 
 * 기능:
 * - RESTful 라우팅
 * - 동적 파라미터 지원
 * - 라우트 그룹핑
 * 
 * 사용 예시:
 * $router->get('/users', 'App\Modules\User\Controllers\UserController@index');
 * $router->post('/users', 'App\Modules\User\Controllers\UserController@store');
 */
class Router
{
    protected AltoRouter $router;
    protected string $basePath;
    
    public function __construct(string $basePath = '')
    {
        $this->router = new AltoRouter();
        $this->basePath = $basePath;
        
        if ($basePath) {
            $this->router->setBasePath($basePath);
        }
    }
    
    /**
     * GET 라우트 등록
     */
    public function get(string $route, $handler, string $name = null): void
    {
        $this->router->map('GET', $route, $handler, $name);
    }
    
    /**
     * POST 라우트 등록
     */
    public function post(string $route, $handler, string $name = null): void
    {
        $this->router->map('POST', $route, $handler, $name);
    }
    
    /**
     * PUT 라우트 등록
     */
    public function put(string $route, $handler, string $name = null): void
    {
        $this->router->map('PUT', $route, $handler, $name);
    }
    
    /**
     * PATCH 라우트 등록
     */
    public function patch(string $route, $handler, string $name = null): void
    {
        $this->router->map('PATCH', $route, $handler, $name);
    }
    
    /**
     * DELETE 라우트 등록
     */
    public function delete(string $route, $handler, string $name = null): void
    {
        $this->router->map('DELETE', $route, $handler, $name);
    }
    
    /**
     * 여러 HTTP 메서드 동시 등록
     */
    public function match(array $methods, string $route, $handler, string $name = null): void
    {
        $this->router->map(implode('|', $methods), $route, $handler, $name);
    }
    
    /**
     * 라우트 디스패치
     */
    public function dispatch(Request $request): Response
    {
        $match = $this->router->match();
        
        if (!$match) {
            return Response::notFound('Route not found');
        }
        
        // URL 파라미터를 Request에 설정
        $request->setParams($match['params']);
        
        // 핸들러 실행
        return $this->handleRoute($match['target'], $request);
    }
    
    /**
     * 라우트 핸들러 실행
     */
    protected function handleRoute($handler, Request $request): Response
    {
        try {
            // Closure 처리
            if (is_callable($handler)) {
                return $handler($request);
            }
            
            // Controller@method 형식 처리
            if (is_string($handler) && str_contains($handler, '@')) {
                [$controller, $method] = explode('@', $handler);
                
                if (!class_exists($controller)) {
                    throw new \Exception("Controller {$controller} not found");
                }
                
                $controllerInstance = new $controller();
                
                if (!method_exists($controllerInstance, $method)) {
                    throw new \Exception("Method {$method} not found in {$controller}");
                }
                
                return $controllerInstance->$method($request);
            }
            
            throw new \Exception('Invalid route handler');
            
        } catch (\Throwable $e) {
            return ErrorHandler::handle($e);
        }
    }
    
    /**
     * URL 생성
     */
    public function generate(string $name, array $params = []): string
    {
        try {
            return $this->router->generate($name, $params);
        } catch (\Exception $e) {
            return '';
        }
    }
}
