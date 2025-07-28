<?php
header('Content-Type: application/json');
echo json_encode([
    'message' => 'Debug test working!',
    'php_version' => PHP_VERSION,
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'not set',
    'path' => parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH)
]);
?> 