<?php

/**
 * Helper Functions
 */

if (!function_exists('env')) {
    /**
     * 환경 변수 가져오기
     */
    function env(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('config')) {
    /**
     * 설정 값 가져오기
     */
    function config(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = CONFIG;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and Die (디버깅용)
     */
    function dd(...$vars)
    {
        foreach ($vars as $var) {
            var_dump($var);
        }
        die();
    }
}

if (!function_exists('dump')) {
    /**
     * Dump (디버깅용)
     */
    function dump(...$vars)
    {
        foreach ($vars as $var) {
            var_dump($var);
        }
    }
}

if (!function_exists('storage_path')) {
    /**
     * Storage 경로 반환
     */
    function storage_path(string $path = ''): string
    {
        $base = __DIR__ . '/../../storage';
        return $path ? $base . '/' . ltrim($path, '/') : $base;
    }
}

if (!function_exists('public_path')) {
    /**
     * Public 경로 반환
     */
    function public_path(string $path = ''): string
    {
        $base = __DIR__ . '/../../public';
        return $path ? $base . '/' . ltrim($path, '/') : $base;
    }
}

if (!function_exists('now')) {
    /**
     * 현재 시간 반환
     */
    function now(string $format = 'Y-m-d H:i:s'): string
    {
        return date($format);
    }
}
