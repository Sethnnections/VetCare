<?php
abstract class Controller {
    protected $data = [];
    protected $layout = 'layouts/main';
    
    // Load a view
    protected function view($view, $data = []) {
        $this->data = array_merge($this->data, $data);
        
        // Extract data variables
        extract($this->data);
        
        // Include the view file
        $viewFile = VIEW_PATH . '/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            if ($this->layout) {
                $content = $viewFile;
                include VIEW_PATH . '/' . $this->layout . '.php';
            } else {
                include $viewFile;
            }
        } else {
            throw new Exception("View file not found: {$viewFile}");
        }
    }
    
    // Load a partial view
    protected function partial($partial, $data = []) {
        extract($data);
        
        $partialFile = VIEW_PATH . '/partials/' . $partial . '.php';
        
        if (file_exists($partialFile)) {
            include $partialFile;
        } else {
            throw new Exception("Partial file not found: {$partialFile}");
        }
    }
    
    // Render JSON response
    protected function json($data, $statusCode = 200) {
        jsonResponse($data, $statusCode);
    }
    
    // Redirect to a URL
    protected function redirect($path) {
        redirect($path);
    }
    
    // Get request method
    protected function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    // Check if request is POST
    protected function isPost() {
        return $this->getMethod() === 'POST';
    }
    
    // Check if request is GET
    protected function isGet() {
        return $this->getMethod() === 'GET';
    }
    
    // Check if request is AJAX
    protected function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    // Get all input data
    protected function input() {
        $input = [];
        
        if ($this->isPost()) {
            $input = array_merge($input, $_POST);
        }
        
        $input = array_merge($input, $_GET);
        
        return sanitize($input);
    }
    
    // Get specific input value
    protected function get($key, $default = null) {
        $input = $this->input();
        return arrayGet($input, $key, $default);
    }
    
    // Validate CSRF token
    protected function validateCsrf() {
        if ($this->isPost()) {
            $token = $this->get('csrf_token');
            if (!validateCsrfToken($token)) {
                throw new Exception('Invalid CSRF token');
            }
        }
    }
    
    // Set flash message
    protected function setFlash($type, $message) {
        setFlash($type, $message);
    }
    
    // Get uploaded files
    protected function files($key = null) {
        if ($key) {
            return $_FILES[$key] ?? null;
        }
        return $_FILES;
    }
    
    // Validate required fields
    protected function validateRequired($fields) {
        $input = $this->input();
        return validateRequired($fields, $input);
    }
    
    // Set page data
    protected function setData($key, $value) {
        $this->data[$key] = $value;
    }
    
    // Get page data
    protected function getData($key = null) {
        if ($key) {
            return arrayGet($this->data, $key);
        }
        return $this->data;
    }
    
    // Set page title
    protected function setTitle($title) {
        $this->data['page_title'] = $title;
    }
    
    // Set no layout (for AJAX requests)
    protected function noLayout() {
        $this->layout = null;
    }
    
    // Authorize user role
    protected function authorize($role) {
        requireRole($role);
    }
    
    // Check if user has permission
    protected function can($permission) {
        $user = getCurrentUser();
        // This can be extended to check specific permissions
        return $user !== null;
    }
    
    // Handle file upload
    protected function handleUpload($fileKey, $destination, $allowedTypes = null) {
        $file = $this->files($fileKey);
        
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'No file uploaded or upload error'];
        }
        
        return uploadFile($file, $destination, $allowedTypes);
    }
    
    // Paginate data
    protected function paginate($model, $page = 1, $perPage = RECORDS_PER_PAGE) {
        return $model->paginate($page, $perPage);
    }
}