<?php
// Define application constants
define('ROOT_PATH_II', dirname(__FILE__));

// Load configuration first
require_once ROOT_PATH_II . '/config/constants.php';

// Load database configuration and helpers
require_once ROOT_PATH_II . '/utils/database.php'; // Fixed path
require_once ROOT_PATH_II . '/utils/helpers.php';

// Autoload classes
spl_autoload_register(function ($className) {
    $directories = [
        ROOT_PATH_II . '/app/core/',
        ROOT_PATH_II . '/app/controllers/',
        ROOT_PATH_II . '/app/models/'
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