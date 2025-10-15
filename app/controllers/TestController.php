<?php
class TestController extends Controller {
    
    public function index() {
        echo "<h1>V-MIS System Test</h1>";
        echo "<p>System is working correctly!</p>";
        echo "<ul>";
        echo "<li>Session ID: " . session_id() . "</li>";
        echo "<li>Logged in: " . (isLoggedIn() ? 'Yes' : 'No') . "</li>";
        echo "<li>Base URL: " . Router::url('/') . "</li>";
        echo "<li>Debug Mode: " . (DEBUG_MODE ? 'On' : 'Off') . "</li>";
        echo "</ul>";
        echo "<a href='" . Router::url('/') . "'>Go to Home</a>";
        exit();
    }
}
?>