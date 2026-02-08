<?php
$app = require __DIR__ . '/../bootstrap/app.php';

// Handle The Request

try {
    $app->run();
} catch (Exception $e) {
    http_response_code(500);
    
    if ($_ENV['APP_DEBUG'] === 'true') {
        echo json_encode([
            'error' => 'Internal Server Error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], JSON_PRETTY_PRINT);
    } else {
        echo json_encode([
            'error' => 'Internal Server Error',
            'message' => 'Something went wrong'
        ]);
    }
}