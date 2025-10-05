<?php
require_once 'includes/init.php';
$auth->logout();
header("Location: login.php");
exit();
?>