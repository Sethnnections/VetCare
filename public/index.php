<?php

define('CONFIG_PATH', ROOT_PATH . '/config');



// Load configuration
require_once CONFIG_PATH . '/constants.php';

// Load helpers
require_once ROOT_PATH . '/utils/helpers.php';

// Autoload classes
spl_autoload_register(function ($className) {
    $directories = [
        APP_PATH . '/core/',
        APP_PATH . '/controllers/',
        APP_PATH . '/models/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Start session
startSession();

// Handle the request
try {
    $url = $_GET['url'] ?? '';
    $router = new Router();
    $router->dispatch($url);
} catch (Exception $e) {
    $statusCode = $e->getCode() ?: 500;
    showError($e->getMessage(), $statusCode);
}
?>