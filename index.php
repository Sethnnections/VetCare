<?php
// Define constants
define('ROOT_PATH', dirname(__FILE__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONTROLLER_PATH', APP_PATH . '/controllers');
define('MODEL_PATH', APP_PATH . '/models');
define('VIEW_PATH', APP_PATH . '/views');
define('CORE_PATH', APP_PATH . '/core');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Load configuration FIRST
require_once CONFIG_PATH . '/constants.php';

// Start session
session_start();

// Error reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Load helpers SECOND - this makes helper functions available everywhere
require_once ROOT_PATH . '/helpers.php';

// Load core classes THIRD
require_once CORE_PATH . '/Router.php';
require_once CORE_PATH . '/Controller.php';
require_once CORE_PATH . '/Model.php';
require_once CONFIG_PATH . '/database.php';

try {
    // Get requested URL
    $url = $_GET['url'] ?? '';
    
    // Create router and dispatch
    $router = new Router();
    $router->dispatch($url);
    
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    
    if (isAjaxRequest()) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    } else {
        if ($e->getCode() === 404) {
            include VIEW_PATH . '/error/404.php';
        } else {
            include VIEW_PATH . '/error/500.php';
        }
    }
}

// Helper function to check if request is AJAX
function isAjaxRequest() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
?>