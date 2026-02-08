<?php

namespace App\Core;

class Request
{
    protected string $method;
    protected string $uri;
    protected array $params = [];
    protected array $query = [];
    protected array $body = [];
    protected array $files = [];
    protected array $headers = [];
    
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->query = $_GET;
        $this->files = $_FILES;
        $this->headers = getallheaders() ?: [];
        
        $this->parseBody();
    }
    
    protected function parseBody(): void
    {
        $contentType = $this->header('Content-Type', '');
        
        if (str_contains($contentType, 'application/json')) {
            $json = file_get_contents('php://input');
            $this->body = json_decode($json, true) ?: [];
        } else {
            $this->body = $_POST;
        }
    }
    
    public function method(): string
    {
        return $this->method;
    }
    
    public function uri(): string
    {
        return $this->uri;
    }
    
    public function setParams(array $params): void
    {
        $this->params = $params;
    }
    
    public function param(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }
    
    public function query(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->query;
        }
        return $this->query[$key] ?? $default;
    }
    
    public function input(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->body;
        }
        return $this->body[$key] ?? $default;
    }
    
    public function all(): array
    {
        return array_merge($this->query, $this->body, $this->params);
    }
    
    public function file(string $key)
    {
        return $this->files[$key] ?? null;
    }
    
    public function header(string $key, $default = null)
    {
        return $this->headers[$key] ?? $default;
    }
    
    public function bearerToken(): ?string
    {
        $header = $this->header('Authorization');
        
        if ($header && preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
}
