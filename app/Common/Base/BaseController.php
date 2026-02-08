<?php

namespace App\Common\Base;

use App\Core\Request;
use App\Core\Response;

/**
 * BaseController
 * 
 * 모든 컨트롤러가 상속받는 베이스 컨트롤러
 * 
 * 제공 기능:
 * - 입력 검증 (validate)
 * - 응답 헬퍼 메서드 (success, error, notFound 등)
 * - 공통 에러 처리
 * 
 * 사용 예시:
 * class UserController extends BaseController
 * {
 *     public function store(Request $request): Response
 *     {
 *         $data = $this->validate($request, [
 *             'email' => 'required|email',
 *             'name' => 'required|min:2'
 *         ]);
 *         
 *         return $this->success($data, 'Created', 201);
 *     }
 * }
 */
abstract class BaseController
{
    /**
     * 요청 데이터 검증
     * 
     * @param Request $request
     * @param array $rules 검증 규칙
     * @return array 검증된 데이터
     * @throws \InvalidArgumentException
     */
    protected function validate(Request $request, array $rules): array
    {
        $errors = [];
        $data = $request->all();
        
        foreach ($rules as $field => $ruleString) {
            $ruleList = explode('|', $ruleString);
            
            foreach ($ruleList as $rule) {
                // required
                if ($rule === 'required' && empty($data[$field])) {
                    $errors[$field][] = "The {$field} field is required.";
                    continue;
                }
                
                // 값이 없으면 다른 검증은 스킵
                if (!isset($data[$field]) || $data[$field] === '') {
                    continue;
                }
                
                // min:n
                if (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if (strlen($data[$field]) < $min) {
                        $errors[$field][] = "The {$field} must be at least {$min} characters.";
                    }
                }
                
                // max:n
                if (str_starts_with($rule, 'max:')) {
                    $max = (int) substr($rule, 4);
                    if (strlen($data[$field]) > $max) {
                        $errors[$field][] = "The {$field} must not exceed {$max} characters.";
                    }
                }
                
                // email
                if ($rule === 'email') {
                    if (!filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                        $errors[$field][] = "The {$field} must be a valid email address.";
                    }
                }
                
                // numeric
                if ($rule === 'numeric') {
                    if (!is_numeric($data[$field])) {
                        $errors[$field][] = "The {$field} must be a number.";
                    }
                }
            }
        }
        
        if (!empty($errors)) {
            throw new \InvalidArgumentException(json_encode($errors));
        }
        
        return $data;
    }
    
    /**
     * 성공 응답
     */
    protected function success($data = null, string $message = 'Success', int $statusCode = 200): Response
    {
        return Response::success($data, $message, $statusCode);
    }
    
    /**
     * 에러 응답
     */
    protected function error(string $message = 'Error', int $statusCode = 400, $errors = null): Response
    {
        return Response::error($message, $statusCode, $errors);
    }
    
    /**
     * Not Found 응답
     */
    protected function notFound(string $message = 'Not Found'): Response
    {
        return Response::notFound($message);
    }
    
    /**
     * Unauthorized 응답
     */
    protected function unauthorized(string $message = 'Unauthorized'): Response
    {
        return Response::unauthorized($message);
    }
    
    /**
     * Forbidden 응답
     */
    protected function forbidden(string $message = 'Forbidden'): Response
    {
        return Response::forbidden($message);
    }
    
    /**
     * Validation Error 응답
     */
    protected function validationError(array $errors, string $message = 'Validation failed'): Response
    {
        return Response::validationError($errors, $message);
    }
}
