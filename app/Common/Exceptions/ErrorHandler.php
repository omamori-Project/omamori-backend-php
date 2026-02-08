<?php

namespace App\Common\Exceptions;

use App\Core\Response;

/**
 * ErrorHandler
 * 
 * 애플리케이션 전역 에러 핸들러
 * 
 * 기능:
 * - Try-Catch로 잡은 예외 처리
 * - 에러 로그 기록
 * - 사용자 친화적 에러 메시지 반환
 * 
 * 사용 예시:
 * try {
 *     // 로직
 * } catch (\Exception $e) {
 *     return ErrorHandler::handle($e);
 * }
 */
class ErrorHandler
{
    /**
     * 예외 처리
     * 
     * @param \Throwable $e
     * @return Response
     */
    public static function handle(\Throwable $e): Response
    {
        // 에러 로그 기록
        self::log($e);
        
        // 예외 타입별 처리
        if ($e instanceof \InvalidArgumentException) {
            // Validation 에러
            $message = $e->getMessage();
            
            // JSON 형식의 에러인 경우
            if (self::isJson($message)) {
                $errors = json_decode($message, true);
                return Response::validationError($errors);
            }
            
            return Response::error($message, 422);
        }
        
        if ($e instanceof \PDOException) {
            // 데이터베이스 에러
            return Response::error(
                'Database error occurred',
                500,
                $_ENV['APP_DEBUG'] === 'true' ? ['details' => $e->getMessage()] : null
            );
        }
        
        if ($e instanceof \Exception && str_contains($e->getMessage(), 'not found')) {
            // Not Found 에러
            return Response::notFound($e->getMessage());
        }
        
        if ($e instanceof \Exception && str_contains($e->getMessage(), 'unauthorized')) {
            // Unauthorized 에러
            return Response::unauthorized($e->getMessage());
        }
        
        if ($e instanceof \Exception && str_contains($e->getMessage(), 'forbidden')) {
            // Forbidden 에러
            return Response::forbidden($e->getMessage());
        }
        
        // 기본 에러 처리
        $message = $_ENV['APP_DEBUG'] === 'true' 
            ? $e->getMessage() 
            : 'An error occurred';
        
        $details = $_ENV['APP_DEBUG'] === 'true' ? [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ] : null;
        
        return Response::error($message, 500, $details);
    }
    
    /**
     * 에러 로그 기록
     * 
     * @param \Throwable $e
     */
    private static function log(\Throwable $e): void
    {
        $logFile = storage_path('logs/error-' . date('Y-m-d') . '.log');
        
        $message = sprintf(
            "[%s] %s: %s in %s:%d\nStack trace:\n%s\n\n",
            date('Y-m-d H:i:s'),
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );
        
        // 로그 디렉토리가 없으면 생성
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        error_log($message, 3, $logFile);
    }
    
    /**
     * JSON 형식인지 확인
     * 
     * @param string $string
     * @return bool
     */
    private static function isJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    /**
     * 커스텀 에러 생성 헬퍼
     */
    public static function notFound(string $message = 'Resource not found'): \Exception
    {
        return new \Exception($message . ' not found');
    }
    
    public static function unauthorized(string $message = 'Unauthorized'): \Exception
    {
        return new \Exception($message . ' unauthorized');
    }
    
    public static function forbidden(string $message = 'Forbidden'): \Exception
    {
        return new \Exception($message . ' forbidden');
    }
    
    public static function validation(array $errors): \InvalidArgumentException
    {
        return new \InvalidArgumentException(json_encode($errors));
    }
}
