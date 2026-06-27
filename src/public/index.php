<?php
declare(strict_types=1);

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$scriptPath = $_SERVER['SCRIPT_NAME'] ?? '/index.php';

if (!in_array($requestPath, ['/', $scriptPath], true)) {
    http_response_code(404);
    echo 'Page not found.';
    return;
}

require __DIR__ . '/../index.php';
