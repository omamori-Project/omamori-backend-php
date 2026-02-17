<?php

namespace App\Modules\Auth\Services;

// import
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthService{
    public function generateToken(int $userId): string{
        $secret = $_ENV['TOKEN_SECRET'];
        $ttl = (int)($_ENV['TOKEN_TTL'] ?? 3600);

        $payload = [
            'user_id' => $userId,
            'exp' => time() + $ttl,
        ];
        return JWT:: encode($payload, $secret, 'HS256');
    }

    public function verifyAndGetUserId(string $token): int{
        $secret = $_ENV['TOKEN_SECRET'];
        $decoded = JWT:: decode($token, new Key($secret, 'HS256'));

        if(!isset($decoded -> user_id)){
            throw new \Exception('Token unauthorized');
        }
        return (int)$decoded -> user_id;
    }
}