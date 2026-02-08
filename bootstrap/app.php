<?php
// Register The Auto Loader
require __DIR__ . '/../vendor/autoload.php';

// Load Environment Variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Set Error Reporting

error_reporting(E_ALL);
ini_set('display_errors', $_ENV['APP_DEBUG'] === 'true' ? '1' : '0');

// Set Timezone
date_default_timezone_set('Asia/Seoul');

// Load Configuration Files
$config = [];
foreach (glob(__DIR__ . '/../config/*.php') as $file) {
    $name = basename($file, '.php');
    $config[$name] = require $file;
}

define('CONFIG', $config);

// Return Application Instance
return new App\Core\Application();