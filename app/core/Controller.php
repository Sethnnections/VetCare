<?php
class Controller {
    protected $params = [];
    protected $data = [];
    
    public function __construct($params = []) {
        $this->params = $params;
    }
    
    // Set data for views
    protected function setData($key, $value) {
        $this->data[$key] = $value;
    }
    
    // Set page title
    protected function setTitle($title) {
        $this->setData('title', $title);
    }
    
    // Render view with layout
    protected function view($view, $layout = 'main') {
        // Extract data for the view
        extract($this->data);
        
        // Start output buffering for view content
        ob_start();
        
        // Include the view file
        $viewFile = VIEW_PATH . '/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            throw new Exception("View file not found: $viewFile");
        }
        
        // Get the view content
        $content = ob_get_clean();
        
        // Include the layout
        $layoutFile = VIEW_PATH . '/layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            require_once $layoutFile;
        } else {
            // If layout doesn't exist, just output content
            echo $content;
        }
    }
    
    // Redirect to another URL
    protected function redirect($url) {
        $baseUrl = Router::url($url);
        header('Location: ' . $baseUrl);
        exit();
    }
    
    // Get POST data
    protected function input($key = null) {
        if ($key) {
            return isset($_POST[$key]) ? sanitize($_POST[$key]) : null;
        }
        return sanitize($_POST);
    }
    
    // Get GET data
    protected function get($key = null, $default = null) {
        if ($key) {
            return isset($_GET[$key]) ? sanitize($_GET[$key]) : $default;
        }
        return sanitize($_GET);
    }
    
    // Get files data
    protected function files($key = null) {
        if ($key) {
            return isset($_FILES[$key]) ? $_FILES[$key] : null;
        }
        return $_FILES;
    }
    
    // Check if request is POST
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    // Check if request is AJAX
    protected function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    // Set flash message
    protected function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
    
    // Validate CSRF token
    protected function validateCsrf() {
        $token = $this->input('csrf_token');
        if (!$token || !validateCsrfToken($token)) {
            throw new Exception('Invalid CSRF token');
        }
    }
    
    // Authorize user role
    protected function authorize($allowedRoles) {
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], (array)$allowedRoles)) {
            $this->setFlash('error', 'Access denied');
            $this->redirect('/dashboard');
        }
    }
    
    // Send JSON response
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    
    // Pagination helper
    protected function paginate($model, $page = 1, $perPage = 10) {
        $page = max(1, (int)$page);
        $offset = ($page - 1) * $perPage;
        
        $total = $model->count();
        $data = $model->findAll($offset, $perPage);
        
        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => ceil($total / $perPage)
        ];
    }
}
?>