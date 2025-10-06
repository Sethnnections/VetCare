<?php
session_start();

// Include database and classes
require_once 'config/database.php';
require_once 'classes/User.php';
require_once 'classes/Auth.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize auth
$auth = new Auth($db);

// Create default admin if not exists
$auth->createDefaultAdmin();

// Redirect if not logged in
function requireLogin() {
    if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: login.php");
        exit();
    }
}

// Check role access
function requireRole($allowed_roles) {
    if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
        header("Location: dashboard.php");
        exit();
    }
}


?>