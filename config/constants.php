<?php
// Database constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'veterinary_system');
define('DB_USER', 'root');
define('DB_PASS', '');

// Define ROOT_PATH first
define('ROOT_PATH', dirname(__DIR__));

// Application paths
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('LOG_PATH', ROOT_PATH . '/logs');

// View paths
define('VIEW_PATH', APP_PATH . '/views');
define('CONTROLLER_PATH', APP_PATH . '/controllers');
define('MODEL_PATH', APP_PATH . '/models');

// Application constants
define('APP_NAME', 'Veterinary Health Management System');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/v-ims');
define('BASE_URL', '/v-ims/');

// User roles
define('ROLE_ADMIN', 'admin');
define('ROLE_VETERINARY', 'veterinary');
define('ROLE_CLIENT', 'client');

// Status constants
define('STATUS_ACTIVE', 1);
define('STATUS_INACTIVE', 0);
define('STATUS_PENDING', 'pending');
define('STATUS_COMPLETED', 'completed');
define('STATUS_CANCELLED', 'cancelled');

// Medicine types
define('MEDICINE_ANTIBIOTIC', 'antibiotic');
define('MEDICINE_VACCINE', 'vaccine');
define('MEDICINE_SUPPLEMENT', 'supplement');
define('MEDICINE_ANESTHETIC', 'anesthetic');

// Treatment status
define('TREATMENT_ONGOING', 'ongoing');
define('TREATMENT_COMPLETED', 'completed');
define('TREATMENT_FOLLOW_UP', 'follow_up');

// Payment methods
define('PAYMENT_CASH', 'cash');
define('PAYMENT_MOBILE', 'mobile_money');
define('PAYMENT_BANK', 'bank_transfer');

// Pagination
define('RECORDS_PER_PAGE', 10);

// File upload
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_DOC_TYPES', ['pdf', 'doc', 'docx']);

// Security
define('SESSION_TIMEOUT', 3600); // 1 hour
define('PASSWORD_MIN_LENGTH', 6);
define('HASH_ALGORITHM', PASSWORD_DEFAULT);

// SMS/Email settings
define('SMS_GATEWAY_URL', 'http://sms-gateway.api');
define('SMS_API_KEY', 'your-sms-api-key');
define('EMAIL_FROM', 'noreply@veterinary-system.com');
define('EMAIL_FROM_NAME', 'Veterinary System');

// Date/Time formats
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'd/m/Y');
define('DISPLAY_DATETIME_FORMAT', 'd/m/Y H:i');

// Error reporting
define('DEBUG_MODE', true);
define('ENABLE_LOGGING', true);

// Default timezone
date_default_timezone_set('Africa/Blantyre');

// Error reporting based on debug mode
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>