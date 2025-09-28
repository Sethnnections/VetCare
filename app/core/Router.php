<?php
class Router {
    private $routes = [];
    private $params = [];
    
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
                        $controllerObject->$action();
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
    
    protected function convertToCamelCase($string) {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $string))));
    }
    
    protected function removeQueryStringVariables($url) {
        if ($url != '') {
            $parts = explode('&', $url, 2);
            
            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }
        
        return $url;
    }
}
?>