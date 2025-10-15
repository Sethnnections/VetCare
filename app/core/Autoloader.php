<?php
class Autoloader {
    public static function register() {
        spl_autoload_register(function ($className) {
            $directories = [
                APP_PATH . '/core/',
                APP_PATH . '/models/',
                APP_PATH . '/controllers/',
                APP_PATH . '/classes/'
            ];
            
            foreach ($directories as $directory) {
                $file = $directory . $className . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            return false;
        });
    }
}

// Register autoloader
Autoloader::register();
?>