<?php
header('Content-Type: application/json');
echo json_encode([
    'HTTPS' => $_SERVER['HTTPS'] ?? 'not set',
    'HTTP_X_FORWARDED_PROTO' => $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'not set',
    'HTTP_X_FORWARDED_SSL' => $_SERVER['HTTP_X_FORWARDED_SSL'] ?? 'not set',
    'HTTP_X_FORWARDED_FOR' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 'not set',
    'SERVER_PORT' => $_SERVER['SERVER_PORT'] ?? 'not set',
    'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'not set',
], JSON_PRETTY_PRINT);