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
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                $this->params = $params;
                $this->matches = $matches;
                return true;
            }
        }
        return false;
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
                        throw new Exception("Method $action in controller $controller not found");
                    }
                } else {
                    throw new Exception("Controller class $controller not found");
                }
            } else {
                throw new Exception("Controller file $controllerFile not found");
            }
        } else {
            throw new Exception('No route matched', 404);
        }
    }
    
    protected function getUrlParameters() {
        $params = [];
        // Skip the first match (full match) and use capturing groups
        for ($i = 1; $i < count($this->matches); $i++) {
            $params[] = $this->matches[$i];
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
        $baseUrl .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
        
        return $baseUrl . ltrim($path, '/');
    }
}
?>