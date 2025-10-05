<?php
// Define application constants
define('ROOT_PATH_III', dirname(__FILE__));

// Load configuration first
require_once ROOT_PATH_III . '/../config/constants.php';

// Load helpers BEFORE autoloader
require_once ROOT_PATH_III . '/../utils/helpers.php';
require_once ROOT_PATH_III . '/../utils/database.php';

// Autoload classes
spl_autoload_register(function ($className) {
    $directories = [
        ROOT_PATH_III . '/../app/core/',
        ROOT_PATH_III . '/../app/controllers/',
        ROOT_PATH_III . '/../app/models/'
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