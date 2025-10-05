<?php
require_once 'includes/init.php';
requireLogin();
requireRole(['admin']);

if($_POST && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $current_status = $_POST['current_status'];
    $new_status = $current_status ? 0 : 1;
    
    $user = new User($db);
    $user->id = $user_id;
    $user->is_active = $new_status;
    
    if($user->updateStatus()) {
        $_SESSION['message'] = "User status updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update user status!";
    }
}

header("Location: dashboard.php");
exit();
?>