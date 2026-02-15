<?php

namespace App\Modules\Auth\Services;

class TokenService{
    // 프로젝트 방식
    public static function issue(int $userID): string{
        throw new \Exception('TokenService::issue not implemented');
    }

    public static function userIdFromBearer(?string $bearerToken): int{
        throw new \Exception('TokenService::userIdFromNearer not implemented');
    }
}