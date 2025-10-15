<?php
// Define basic constants first
define('ROOT_PATH', dirname(__FILE__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONTROLLER_PATH', APP_PATH . '/controllers');
define('MODEL_PATH', APP_PATH . '/models');
define('VIEW_PATH', APP_PATH . '/views');
define('CORE_PATH', APP_PATH . '/core');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration FIRST - this defines BASE_URL and other constants
require_once CONFIG_PATH . '/constants.php';

// Check if it's a static file request
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$basePath = parse_url(BASE_URL, PHP_URL_PATH);

// Remove base path from request URI
if ($basePath && $basePath !== '/' && strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

// Serve static files directly
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/i', $requestUri)) {
    $filePath = PUBLIC_PATH . $requestUri;
    if (file_exists($filePath)) {
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject'
        ];
        
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (isset($mimeTypes[$extension])) {
            header('Content-Type: ' . $mimeTypes[$extension]);
        }
        
        readfile($filePath);
        exit;
    }
}

// Load core classes SECOND
require_once CORE_PATH . '/Model.php';
require_once CORE_PATH . '/Controller.php';
require_once CORE_PATH . '/Router.php';

// Load helpers THIRD
require_once ROOT_PATH . '/helpers.php';

// Load database configuration FOURTH
require_once CONFIG_PATH . '/database.php';

// Load models FIFTH - Load all model files
$modelFiles = [
    'User.php',
    'Client.php', 
    'Animal.php',
    'Treatment.php',
    'Vaccine.php',
    'Billing.php',
    'Reminder.php'
];

foreach ($modelFiles as $modelFile) {
    $modelPath = MODEL_PATH . '/' . $modelFile;
    if (file_exists($modelPath)) {
        require_once $modelPath;
    }
}

// Load Auth class if it exists
$authFile = APP_PATH . '/classes/Auth.php';
if (file_exists($authFile)) {
    require_once $authFile;
}

try {
    // Get requested URL
    $url = $_GET['url'] ?? '';
    
    // Debug output
    if (DEBUG_MODE) {
        echo "<!-- Debug Info -->";
        echo "<!-- URL: " . htmlspecialchars($url) . " -->";
        echo "<!-- Base URL: " . BASE_URL . " -->";
        echo "<!-- App URL: " . APP_URL . " -->";
    }
    
    // Create router and dispatch
    $router = new Router();
    $router->dispatch($url);
    
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    
    if (isAjaxRequest()) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    } else {
        // Better error display
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>Error - V-MIS</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; background: #f9f1d5; }
                .error-container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .error-code { color: #fd742a; font-size: 24px; font-weight: bold; }
                .error-message { color: #134d60; margin: 10px 0; }
                .debug-info { background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0; font-family: monospace; }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="error-code">' . ($e->getCode() ?: 'Error') . '</div>
                <div class="error-message">' . htmlspecialchars($e->getMessage()) . '</div>';
        
        if (DEBUG_MODE) {
            echo '<div class="debug-info">
                    <strong>Debug Information:</strong><br>
                    URL: ' . htmlspecialchars($_GET['url'] ?? '') . '<br>
                    Base URL: ' . BASE_URL . '<br>
                    File: ' . $e->getFile() . ':' . $e->getLine() . '<br>
                    Trace: ' . htmlspecialchars($e->getTraceAsString()) . '
                  </div>';
        }
        
        echo '<p><a href="' . Router::url('/') . '">Go to Homepage</a></p>
            </div>
        </body>
        </html>';
    }
}