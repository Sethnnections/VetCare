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
    
    // Render view
    protected function view($view) {
        $viewFile = VIEW_PATH . '/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            // Extract data for the view
            extract($this->data);
            
            // Start output buffering
            ob_start();
            require_once $viewFile;
            $content = ob_get_clean();
            
            // Include layout
            require_once VIEW_PATH . '/layouts/main.php';
        } else {
            throw new Exception("View file $viewFile not found");
        }
    }
    
    // Redirect to another URL
    protected function redirect($url) {
        header('Location: ' . Router::url($url));
        exit();
    }
    
    // Get POST data
    protected function input($key = null) {
        if ($key) {
            return isset($_POST[$key]) ? $this->sanitize($_POST[$key]) : null;
        }
        return $this->sanitize($_POST);
    }
    
    // Get GET data
    protected function get($key = null, $default = null) {
        if ($key) {
            return isset($_GET[$key]) ? $this->sanitize($_GET[$key]) : $default;
        }
        return $this->sanitize($_GET);
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
    
    // Sanitize input data
    private function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
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