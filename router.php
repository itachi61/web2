<?php
// Router for PHP built-in server
// Handles URL rewriting since .htaccess doesn't work with php -S

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Check if requesting a static file (CSS, JS, images, etc.)
if (preg_match('/\.(?:css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$/i', $uri)) {
    // For static files, let PHP built-in server handle them directly
    // Return false tells the server to serve the file from public/ directory
    return false;
}

// For dynamic requests (non-static files):
// Extract URL for routing
$url = ltrim($uri, '/');

// Set $_GET['url'] for App router
$_GET['url'] = $url;

// Change to public directory so relative paths in index.php work correctly
chdir(__DIR__ . '/public');

// Include the application entry point
require __DIR__ . '/public/index.php';
