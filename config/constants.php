<?php
// Load environment variables
$envFile = ROOT_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Skip comments
        
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        if (!empty($key)) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Database Constants
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'veterinary_ims');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8mb4');

// Application Constants
define('APP_NAME', $_ENV['APP_NAME'] ?? 'Veterinary IMS');
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost/v-ims');
define('BASE_URL', $_ENV['BASE_URL'] ?? '/v-ims/');
define('DEBUG_MODE', filter_var($_ENV['DEBUG_MODE'] ?? true, FILTER_VALIDATE_BOOLEAN));

// Security Constants
define('SECRET_KEY', $_ENV['SECRET_KEY'] ?? 'default-secret-key');
define('CSRF_PROTECTION', filter_var($_ENV['CSRF_PROTECTION'] ?? true, FILTER_VALIDATE_BOOLEAN));
define('SESSION_TIMEOUT', (int)($_ENV['SESSION_TIMEOUT'] ?? 3600));
define('PASSWORD_MIN_LENGTH', (int)($_ENV['PASSWORD_MIN_LENGTH'] ?? 6));

// File Upload Constants
define('MAX_FILE_SIZE', (int)($_ENV['MAX_FILE_SIZE'] ?? 5242880));
define('ALLOWED_IMAGE_TYPES', explode(',', $_ENV['ALLOWED_IMAGE_TYPES'] ?? 'jpg,jpeg,png,gif'));
define('ALLOWED_DOC_TYPES', explode(',', $_ENV['ALLOWED_DOC_TYPES'] ?? 'pdf,doc,docx'));
define('UPLOAD_PATH', $_ENV['UPLOAD_PATH'] ?? 'public/uploads');

// Email Constants
define('EMAIL_FROM', $_ENV['EMAIL_FROM'] ?? 'noreply@veterinary-system.com');
define('EMAIL_FROM_NAME', $_ENV['EMAIL_FROM_NAME'] ?? 'Veterinary System');
define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com');
define('SMTP_PORT', (int)($_ENV['SMTP_PORT'] ?? 587));
define('SMTP_USER', $_ENV['SMTP_USER'] ?? '');
define('SMTP_PASS', $_ENV['SMTP_PASS'] ?? '');

// SMS Constants
define('SMS_GATEWAY_URL', $_ENV['SMS_GATEWAY_URL'] ?? '');
define('SMS_API_KEY', $_ENV['SMS_API_KEY'] ?? '');

// System Constants
define('TIMEZONE', $_ENV['TIMEZONE'] ?? 'Africa/Blantyre');
define('ENABLE_LOGGING', filter_var($_ENV['ENABLE_LOGGING'] ?? true, FILTER_VALIDATE_BOOLEAN));
define('LOG_LEVEL', $_ENV['LOG_LEVEL'] ?? 'DEBUG');

// Feature Flags
define('ENABLE_REGISTRATION', filter_var($_ENV['ENABLE_REGISTRATION'] ?? true, FILTER_VALIDATE_BOOLEAN));
define('ENABLE_EMAIL_NOTIFICATIONS', filter_var($_ENV['ENABLE_EMAIL_NOTIFICATIONS'] ?? false, FILTER_VALIDATE_BOOLEAN));
define('ENABLE_SMS_REMINDERS', filter_var($_ENV['ENABLE_SMS_REMINDERS'] ?? false, FILTER_VALIDATE_BOOLEAN));

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_VETERINARY', 'veterinary');
define('ROLE_CLIENT', 'client');

// Status Constants
define('STATUS_ACTIVE', 1);
define('STATUS_INACTIVE', 0);
define('STATUS_COMPLETED', 'completed');

// Treatment Status
define('TREATMENT_ONGOING', 'ongoing');
define('TREATMENT_FOLLOW_UP', 'follow_up');

// Vaccine Status
define('VACCINE_SCHEDULED', 'scheduled');
define('VACCINE_COMPLETED', 'completed');
define('VACCINE_OVERDUE', 'overdue');

// Billing Status
define('BILLING_PENDING', 'pending');
define('BILLING_PAID', 'paid');
define('BILLING_CANCELLED', 'cancelled');

// Reminder Priorities
define('PRIORITY_LOW', 'low');
define('PRIORITY_MEDIUM', 'medium');
define('PRIORITY_HIGH', 'high');
define('PRIORITY_URGENT', 'urgent');

// Set timezone
date_default_timezone_set(TIMEZONE);

// Error reporting based on debug mode
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>