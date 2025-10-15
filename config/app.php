<?php
// Application Configuration File

return [
    // Application Information
    'name' => APP_NAME,
    'url' => APP_URL,
    'base_url' => BASE_URL,
    'debug' => DEBUG_MODE,
    
    // Paths
    'paths' => [
        'controllers' => CONTROLLER_PATH,
        'models' => MODEL_PATH,
        'views' => VIEW_PATH,
        'uploads' => UPLOAD_PATH,
        'logs' => ROOT_PATH . '/storage/logs',
    ],
    
    // Security
    'security' => [
        'csrf_protection' => CSRF_PROTECTION,
        'session_timeout' => SESSION_TIMEOUT,
        'password_min_length' => PASSWORD_MIN_LENGTH,
    ],
    
    // Database
    'database' => [
        'host' => DB_HOST,
        'name' => DB_NAME,
        'username' => DB_USER,
        'password' => DB_PASS,
        'charset' => DB_CHARSET,
    ],
    
    // File Upload
    'upload' => [
        'max_file_size' => MAX_FILE_SIZE,
        'allowed_image_types' => ALLOWED_IMAGE_TYPES,
        'allowed_doc_types' => ALLOWED_DOC_TYPES,
        'upload_path' => UPLOAD_PATH,
    ],
    
    // Email
    'email' => [
        'from' => EMAIL_FROM,
        'from_name' => EMAIL_FROM_NAME,
        'smtp' => [
            'host' => SMTP_HOST,
            'port' => SMTP_PORT,
            'username' => SMTP_USER,
            'password' => SMTP_PASS,
        ],
    ],
    
    // Features
    'features' => [
        'enable_registration' => ENABLE_REGISTRATION,
        'enable_email_notifications' => ENABLE_EMAIL_NOTIFICATIONS,
        'enable_sms_reminders' => ENABLE_SMS_REMINDERS,
    ],
    
    // Timezone
    'timezone' => TIMEZONE,
];
?>