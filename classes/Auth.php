<?php
class Auth {
    private $user;
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
        $this->user = new User($db);
    }
    
    public function login($email, $password) {
        $this->user->email = $email;
        
        if($this->user->emailExists() && password_verify($password, $this->user->password)) {
            $_SESSION['user_id'] = $this->user->id;
            $_SESSION['username'] = $this->user->username;
            $_SESSION['role'] = $this->user->role;
            $_SESSION['logged_in'] = true;
            return true;
        }
        return false;
    }
    
    public function logout() {
        session_destroy();
        session_unset();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public function getUserRole() {
        return $_SESSION['role'] ?? null;
    }
    
    // Create default admin
    public function createDefaultAdmin() {
        $check_query = "SELECT COUNT(*) FROM users WHERE role = 'admin'";
        $stmt = $this->db->prepare($check_query);
        $stmt->execute();
        $admin_count = $stmt->fetchColumn();
        
        if($admin_count == 0) {
            $admin_user = new User($this->db);
            $admin_user->username = 'admin';
            $admin_user->email = 'admin@vet.com';
            $admin_user->password = 'admin123';
            $admin_user->role = 'admin';
            $admin_user->is_active = 1;
            
            return $admin_user->create();
        }
        return true;
    }

    // Register new user
    public function registerUser($username, $email, $password, $role = 'client') {
        $user = new User($this->db);
        $user->username = $username;
        $user->email = $email;
        $user->password = $password;
        $user->role = $role;
        $user->is_active = 1;
        
        return $user->create();
    }

    // Check if user is active
    public function isUserActive($email) {
        $query = "SELECT is_active FROM users WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['is_active'] == 1;
        }
        return false;
    }
}
?>