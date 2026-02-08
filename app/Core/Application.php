<?php

namespace App\Core;

/**
 * Application
 * 
 * 애플리케이션 컨테이너
 * 
 * 기능:
 * - 라우팅 초기화
 * - 요청 처리
 * - 응답 반환
 */
class Application
{
    protected Router $router;
    protected Request $request;
    protected Database $database;
    
    public function __construct()
    {
        $this->router = new Router();
        $this->request = new Request();
        $this->database = new Database();
        
        $this->loadRoutes();
    }
    
    /**
     * 라우트 파일 로드
     */
    protected function loadRoutes(): void
    {
        // 글로벌 변수로 router 제공
        global $router;
        $router = $this->router;
        
        require __DIR__ . '/../../routes/api.php';
    }
    
    /**
     * 애플리케이션 실행
     */
    public function run(): void
    {
        $response = $this->router->dispatch($this->request);
        $response->send();
    }
    
    /**
     * Router 인스턴스 반환
     */
    public function getRouter(): Router
    {
        return $this->router;
    }
    
    /**
     * Database 인스턴스 반환
     */
    public function getDatabase(): Database
    {
        return $this->database;
    }
}
