<?php
// Environment helper functions
function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

function config($key = null, $default = null) {
    static $config = null;
    
    if ($config === null) {
        $configFile = CONFIG_PATH . '/app.php';
        $config = file_exists($configFile) ? require $configFile : [];
    }
    
    if ($key === null) {
        return $config;
    }
    
    $keys = explode('.', $key);
    $value = $config;
    
    foreach ($keys as $k) {
        if (isset($value[$k])) {
            $value = $value[$k];
        } else {
            return $default;
        }
    }
    
    return $value;
}

// Authentication helpers
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        redirect('/login');
    }
}

function requireRole($allowedRoles) {
    requireLogin();
    
    $userRole = getCurrentUserRole();
    if (!in_array($userRole, (array)$allowedRoles)) {
        setFlash('error', 'Access denied. Insufficient permissions.');
        redirect('/dashboard');
    }
}

function isLoggedIn() {
    startSession();
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

function getCurrentUser() {
    startSession();
    return $_SESSION['user'] ?? null;
}

// Session management
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => SESSION_TIMEOUT,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        session_start();
        
        // Check session timeout
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
            session_destroy();
            session_start();
        }
        $_SESSION['last_activity'] = time();
    }
}

// CSRF Protection
function generateCsrfToken() {
    startSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    startSession();
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

function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length));
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

// Date and time helpers
function getCurrentDate() {
    return date('Y-m-d H:i:s');
}

function formatDate($date, $format = 'M j, Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

function formatDateTime($date, $format = 'M j, Y g:i A') {
    if (empty($date)) return '';
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
function uploadFile($file, $directory, $allowedTypes = null) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload error: ' . $file['error']);
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        throw new Exception('File size exceeds maximum allowed size of ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB');
    }
    
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Use default allowed types if not specified
    if ($allowedTypes === null) {
        $allowedTypes = ALLOWED_IMAGE_TYPES;
    }
    
    if (!in_array($fileExtension, $allowedTypes)) {
        throw new Exception('Invalid file type. Allowed types: ' . implode(', ', $allowedTypes));
    }
    
    // Create directory if it doesn't exist
    $uploadDir = PUBLIC_PATH . '/uploads/' . $directory;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
    $uploadPath = $uploadDir . '/' . $fileName;
    
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to move uploaded file');
    }
    
    return $fileName;
}

// Logging functions
function logError($message) {
    if (!ENABLE_LOGGING) return;
    
    $logFile = ROOT_PATH . '/storage/logs/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] ERROR: {$message}" . PHP_EOL;
    
    // Create logs directory if it doesn't exist
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

function logActivity($message, $userId = null) {
    if (!ENABLE_LOGGING) return;
    
    $logFile = ROOT_PATH . '/storage/logs/activity.log';
    $timestamp = date('Y-m-d H:i:s');
    $userInfo = $userId ? "User ID: {$userId}" : 'System';
    $logMessage = "[{$timestamp}] ACTIVITY: {$userInfo} - {$message}" . PHP_EOL;
    
    // Create logs directory if it doesn't exist
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

function logQuery($sql, $params = []) {
    if (!ENABLE_LOGGING || !DEBUG_MODE) return;
    
    $logFile = ROOT_PATH . '/storage/logs/query.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] QUERY: {$sql}";
    
    if (!empty($params)) {
        $logMessage .= " | Params: " . json_encode($params);
    }
    
    $logMessage .= PHP_EOL;
    
    // Create logs directory if it doesn't exist
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

// Flash messages
function setFlash($type, $message) {
    startSession();
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    startSession();
    if (isset($_SESSION['flash'])) {
        $message = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $message;
    }
    return null;
}

// URL and routing helpers
function url($path = '') {
    return Router::url($path);
}

function redirect($url) {
    header('Location: ' . Router::url($url));
    exit();
}

function back() {
    $referer = $_SERVER['HTTP_REFERER'] ?? '/';
    redirect($referer);
}

// Debug helpers
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

function dump($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

// Array helpers
function arrayOnly($array, $keys) {
    return array_intersect_key($array, array_flip((array)$keys));
}

function arrayExcept($array, $keys) {
    return array_diff_key($array, array_flip((array)$keys));
}

// String helpers
function strSlug($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
}

function strLimit($string, $limit = 100, $end = '...') {
    if (mb_strlen($string) <= $limit) {
        return $string;
    }
    
    return rtrim(mb_substr($string, 0, $limit, 'UTF-8')) . $end;
}

// Notification helpers
function sendEmail($to, $subject, $message, $headers = []) {
    if (!ENABLE_EMAIL_NOTIFICATIONS) {
        logActivity("Email notification skipped (disabled): {$subject} to {$to}");
        return true;
    }
    
    $defaultHeaders = [
        'From: ' . EMAIL_FROM_NAME . ' <' . EMAIL_FROM . '>',
        'Reply-To: ' . EMAIL_FROM,
        'Content-Type: text/html; charset=UTF-8',
        'X-Mailer: PHP/' . phpversion()
    ];
    
    $allHeaders = array_merge($defaultHeaders, $headers);
    
    try {
        $result = mail($to, $subject, $message, implode("\r\n", $allHeaders));
        if ($result) {
            logActivity("Email sent: {$subject} to {$to}");
        } else {
            logError("Failed to send email: {$subject} to {$to}");
        }
        return $result;
    } catch (Exception $e) {
        logError("Email sending error: " . $e->getMessage());
        return false;
    }
}

function sendSMS($to, $message) {
    if (!ENABLE_SMS_REMINDERS) {
        logActivity("SMS notification skipped (disabled): to {$to}");
        return true;
    }
    
    // Implement SMS sending logic based on your SMS gateway
    // This is a placeholder implementation
    logActivity("SMS would be sent to {$to}: " . substr($message, 0, 50) . "...");
    return true;
}

// Check if request is AJAX
function isAjaxRequest() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// Get client IP address
function getClientIp() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}
?>