<?php

namespace App\Common\Base;

/**
 * BaseService
 * 
 * 모든 서비스가 상속받는 베이스 서비스
 * 
 * 제공 기능:
 * - 공통 비즈니스 로직
 * - 데이터 검증
 * - 트랜잭션 헬퍼
 * 
 * 사용 예시:
 * class UserService extends BaseService
 * {
 *     public function createUser(array $data): string
 *     {
 *         $this->validateRequired($data, ['email', 'password']);
 *         
 *         // 비즈니스 로직...
 *         
 *         return $userId;
 *     }
 * }
 */
abstract class BaseService
{
    /**
     * 필수 필드 검증
     * 
     * @param array $data
     * @param array $requiredFields
     * @throws \InvalidArgumentException
     */
    protected function validateRequired(array $data, array $requiredFields): void
    {
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                throw new \InvalidArgumentException("The {$field} field is required.");
            }
        }
    }
    
    /**
     * 데이터 검증 (간단한 버전)
     * 
     * @param array $data
     * @param array $rules
     * @throws \InvalidArgumentException
     */
    protected function validate(array $data, array $rules): void
    {
        $errors = [];
        
        foreach ($rules as $field => $ruleString) {
            $ruleList = explode('|', $ruleString);
            
            foreach ($ruleList as $rule) {
                if ($rule === 'required' && empty($data[$field])) {
                    $errors[$field][] = "The {$field} field is required.";
                }
                
                if (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if (isset($data[$field]) && strlen($data[$field]) < $min) {
                        $errors[$field][] = "The {$field} must be at least {$min} characters.";
                    }
                }
                
                if ($rule === 'email' && isset($data[$field])) {
                    if (!filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                        $errors[$field][] = "The {$field} must be a valid email address.";
                    }
                }
            }
        }
        
        if (!empty($errors)) {
            throw new \InvalidArgumentException(json_encode($errors));
        }
    }
    
    /**
     * 현재 타임스탬프 반환
     */
    protected function now(): string
    {
        return date('Y-m-d H:i:s');
    }
    
    /**
     * 배열에서 특정 키만 추출
     */
    protected function only(array $data, array $keys): array
    {
        return array_intersect_key($data, array_flip($keys));
    }
    
    /**
     * 배열에서 특정 키 제외
     */
    protected function except(array $data, array $keys): array
    {
        return array_diff_key($data, array_flip($keys));
    }
}
