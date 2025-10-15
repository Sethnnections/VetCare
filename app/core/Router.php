<?php
class Router {
    private $routes = [];
    private $params = [];
    private $matches = [];
    
    public function __construct() {
        $this->loadRoutes();
    }
    
    public function loadRoutes() {
        require_once CONFIG_PATH . '/routes.php';
        $this->routes = $routes;
    }
    
    public function match($url) {
        // Remove base path from URL
        $basePath = parse_url(BASE_URL, PHP_URL_PATH);
        if ($basePath && $basePath !== '/' && strpos($url, $basePath) === 0) {
            $url = substr($url, strlen($basePath));
        }
        
        // Ensure URL starts with /
        $url = '/' . ltrim($url, '/');
        
        // Remove query string
        $url = $this->removeQueryStringVariables($url);
        
        foreach ($this->routes as $route => $params) {
            // Convert route pattern to regex
            $pattern = $this->compileRoute($route);
            
            if (preg_match($pattern, $url, $matches)) {
                $this->params = $params;
                $this->matches = $matches;
                return true;
            }
        }
        return false;
    }
    
    private function compileRoute($route) {
        // Escape forward slashes for regex
        $pattern = preg_quote($route, '#');
        
        // Convert {param} to named capture groups
        $pattern = preg_replace('/\\\{([a-zA-Z0-9_]+)\\\}/', '(?P<$1>[^\/]+)', $pattern);
        
        // Return the complete regex pattern
        return '#^' . $pattern . '$#i';
    }
    
    public function dispatch($url) {
        $url = $this->removeQueryStringVariables($url);
        
        if ($this->match($url)) {
            $controller = $this->params['controller'] . 'Controller';
            $controllerFile = CONTROLLER_PATH . '/' . $controller . '.php';
            
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                
                if (class_exists($controller)) {
                    $controllerObject = new $controller($this->params);
                    
                    $action = $this->params['action'];
                    $action = $this->convertToCamelCase($action);
                    
                    if (is_callable([$controllerObject, $action])) {
                        // Pass URL parameters to the action method
                        $urlParams = $this->getUrlParameters();
                        call_user_func_array([$controllerObject, $action], $urlParams);
                    } else {
                        throw new Exception("Method $action in controller $controller not found", 404);
                    }
                } else {
                    throw new Exception("Controller class $controller not found", 404);
                }
            } else {
                throw new Exception("Controller file $controllerFile not found", 404);
            }
        } else {
            throw new Exception('No route matched for URL: ' . $url, 404);
        }
    }
    
    protected function getUrlParameters() {
        $params = [];
        // Use named parameters from matches
        foreach ($this->matches as $key => $value) {
            if (!is_numeric($key)) {
                $params[] = $value;
            }
        }
        return $params;
    }
    
    protected function convertToCamelCase($string) {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $string))));
    }
    
    protected function removeQueryStringVariables($url) {
        if ($url != '') {
            $parts = explode('?', $url, 2);
            
            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }
        
        return $url;
    }
    
    // Helper method to generate URLs
    public static function url($path = '') {
        $baseUrl = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $baseUrl .= "://" . $_SERVER['HTTP_HOST'];
        
        // Add base path
        $basePath = BASE_URL;
        if ($basePath && $basePath !== '/') {
            $baseUrl .= rtrim($basePath, '/');
        }
        
        return $baseUrl . '/' . ltrim($path, '/');
    }
}
?>