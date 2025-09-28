<?php
// Session management
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUser() {
    startSession();
    return $_SESSION['user'] ?? null;
}

function getUserRole() {
    $user = getCurrentUser();
    return $user['role'] ?? null;
}

function hasRole($role) {
    return getUserRole() === $role;
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/auth/login');
        exit;
    }
}

function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        showError('Access denied. Insufficient permissions.');
        exit;
    }
}

// Redirection
function redirect($path) {
    $url = BASE_URL . ltrim($path, '/');
    header("Location: {$url}");
    exit;
}

// URL helpers
function url($path = '') {
    return BASE_URL . ltrim($path, '/');
}

function asset($path) {
    return url('public/assets/' . ltrim($path, '/'));
}

// Flash messages
function setFlash($type, $message) {
    startSession();
    $_SESSION['flash'][$type] = $message;
}

function getFlash($type = null) {
    startSession();
    if ($type) {
        $message = $_SESSION['flash'][$type] ?? null;
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function hasFlash($type = null) {
    startSession();
    if ($type) {
        return isset($_SESSION['flash'][$type]);
    }
    return !empty($_SESSION['flash']);
}

// Validation helpers
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePhone($phone) {
    return preg_match('/^(\+265|0)?[0-9]{9}$/', $phone);
}

function validateRequired($fields, $data) {
    $errors = [];
    foreach ($fields as $field) {
        if (empty($data[$field])) {
            $errors[$field] = ucfirst($field) . ' is required';
        }
    }
    return $errors;
}

// Password helpers
function hashPassword($password) {
    return password_hash($password, HASH_ALGORITHM);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function generateRandomPassword($length = 8) {
    return bin2hex(random_bytes($length / 2));
}

// Date helpers
function formatDate($date, $format = DISPLAY_DATE_FORMAT) {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

function formatDateTime($datetime, $format = DISPLAY_DATETIME_FORMAT) {
    if (empty($datetime)) return '';
    return date($format, strtotime($datetime));
}

function getCurrentDate() {
    return date(DATE_FORMAT);
}



// File upload helpers
function uploadFile($file, $destination, $allowedTypes = null) {
    if (!is_dir($destination)) {
        mkdir($destination, 0777, true);
    }
    
    $allowedTypes = $allowedTypes ?: array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_DOC_TYPES);
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($fileExtension, $allowedTypes)) {
        return ['success' => false, 'error' => 'File type not allowed'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'File size too large'];
    }
    
    $fileName = uniqid() . '.' . $fileExtension;
    $filePath = $destination . '/' . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return ['success' => true, 'filename' => $fileName, 'path' => $filePath];
    }
    
    return ['success' => false, 'error' => 'Upload failed'];
}
// Date helpers
function getCurrentDateTime() {
    return date(DATETIME_FORMAT);
}
// Logging
function logError($message, $file = 'error.log') {
    if (!ENABLE_LOGGING) return;
    
    $logFile = LOG_PATH . '/' . $file;
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

// Response helpers
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function showError($message, $statusCode = 500) {
    http_response_code($statusCode);
    include VIEW_PATH . '/error.php';
    exit;
}

// Pagination helpers
function paginate($totalRecords, $currentPage = 1, $recordsPerPage = RECORDS_PER_PAGE) {
    $totalPages = ceil($totalRecords / $recordsPerPage);
    $currentPage = max(1, min($totalPages, $currentPage));
    $offset = ($currentPage - 1) * $recordsPerPage;
    
    return [
        'total_records' => $totalRecords,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'records_per_page' => $recordsPerPage,
        'offset' => $offset,
        'has_previous' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}

// Debugging
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    exit;
}

function dump($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

// Generate unique ID
function generateUniqueId($prefix = '') {
    return $prefix . uniqid() . mt_rand(1000, 9999);
}

// Format currency
function formatCurrency($amount, $currency = 'MWK') {
    return $currency . ' ' . number_format($amount, 2);
}

// Array helpers
function arrayGet($array, $key, $default = null) {
    return isset($array[$key]) ? $array[$key] : $default;
}

function arrayOnly($array, $keys) {
    return array_intersect_key($array, array_flip($keys));
}

// String helpers
function str_limit($string, $limit = 100, $end = '...') {
    if (strlen($string) <= $limit) return $string;
    return substr($string, 0, $limit) . $end;
}

function slug($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
}

// CSRF Protection
function generateCsrfToken() {
    startSession();
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    startSession();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField() {
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}
?>