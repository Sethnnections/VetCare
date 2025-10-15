<?php
// Authentication helpers
function requireLogin() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: ' . Router::url('/login'));
        exit();
    }
}

function requireRole($allowedRoles) {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], (array)$allowedRoles)) {
        header('Location: ' . Router::url('/dashboard'));
        exit();
    }
}

function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

// CSRF Protection
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Security helpers
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Validation helpers
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePhone($phone) {
    return preg_match('/^\+?[0-9\s\-\(\)]{10,}$/', $phone);
}

function validateRequired($fields, $data) {
    $errors = [];
    foreach ($fields as $field) {
        if (empty($data[$field])) {
            $errors[$field] = 'This field is required';
        }
    }
    return $errors;
}

// Date helpers
function getCurrentDate() {
    return date('Y-m-d H:i:s');
}

function formatDate($date, $format = 'M j, Y') {
    return date($format, strtotime($date));
}

function calculateAge($birthDate) {
    if (!$birthDate) return null;
    
    $birth = new DateTime($birthDate);
    $today = new DateTime();
    $age = $today->diff($birth);
    
    $years = $age->y;
    $months = $age->m;
    
    if ($years > 0) {
        return $years . ' year' . ($years > 1 ? 's' : '') . 
               ($months > 0 ? ', ' . $months . ' month' . ($months > 1 ? 's' : '') : '');
    } else {
        return $months . ' month' . ($months > 1 ? 's' : '');
    }
}

// File upload helper
function uploadFile($file, $directory, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif']) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload error: ' . $file['error']);
    }
    
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($fileExtension, $allowedTypes)) {
        throw new Exception('Invalid file type. Allowed types: ' . implode(', ', $allowedTypes));
    }
    
    $fileName = uniqid() . '.' . $fileExtension;
    $uploadPath = PUBLIC_PATH . '/uploads/' . $directory . '/' . $fileName;
    
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to move uploaded file');
    }
    
    return $fileName;
}

// Logging
function logError($message) {
    $logFile = ROOT_PATH . '/storage/logs/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] ERROR: {$message}" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

function logActivity($message, $userId = null) {
    $logFile = ROOT_PATH . '/storage/logs/activity.log';
    $timestamp = date('Y-m-d H:i:s');
    $userInfo = $userId ? "User ID: {$userId}" : 'Unknown User';
    $logMessage = "[{$timestamp}] ACTIVITY: {$userInfo} - {$message}" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

// Flash messages
function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $message = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $message;
    }
    return null;
}

// Redirect helper
function redirect($url) {
    header('Location: ' . Router::url($url));
    exit();
}

// Debug helper
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}
?>