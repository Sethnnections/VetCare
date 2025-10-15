<?php
class Auth {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Authenticate user with email and password
     */
    public function login($email, $password) {
        $sql = "SELECT * FROM users WHERE email = :email AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['last_activity'] = time();
            
            // Update last login
            $this->updateLastLogin($user['user_id']);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Register new user
     */
    public function register($userData) {
        try {
            // Check if email already exists
            $sql = "SELECT user_id FROM users WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['email' => $userData['email']]);
            
            if ($stmt->fetch()) {
                return ['success' => false, 'error' => 'Email already exists'];
            }
            
            // Check if username already exists
            $sql = "SELECT user_id FROM users WHERE username = :username";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['username' => $userData['username']]);
            
            if ($stmt->fetch()) {
                return ['success' => false, 'error' => 'Username already exists'];
            }
            
            // Hash password
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            // Insert user
            $sql = "INSERT INTO users (username, email, password, role, first_name, last_name, phone, is_active) 
                    VALUES (:username, :email, :password, :role, :first_name, :last_name, :phone, 1)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'username' => $userData['username'],
                'email' => $userData['email'],
                'password' => $hashedPassword,
                'role' => $userData['role'],
                'first_name' => $userData['first_name'] ?? null,
                'last_name' => $userData['last_name'] ?? null,
                'phone' => $userData['phone'] ?? null
            ]);
            
            $userId = $this->db->lastInsertId();
            
            return ['success' => true, 'user_id' => $userId];
            
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Registration failed. Please try again.'];
        }
    }
    
    /**
     * Check if user is active
     */
    public function isUserActive($email) {
        $sql = "SELECT is_active FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch();
        
        return $result && $result['is_active'] == 1;
    }
    
    /**
     * Update last login timestamp
     */
    private function updateLastLogin($userId) {
        $sql = "UPDATE users SET last_login = NOW() WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
    }
    
    /**
     * Logout user
     */
    public function logout() {
        // Unset all session variables
        $_SESSION = [];
        
        // Destroy session
        session_destroy();
        
        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
    }
    
    /**
     * Verify user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Get current user ID
     */
    public function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Get current user role
     */
    public function getCurrentUserRole() {
        return $_SESSION['role'] ?? null;
    }
    
    /**
     * Check if user has required role
     */
    public function hasRole($role) {
        return $this->getCurrentUserRole() === $role;
    }
    
    /**
     * Check if user has any of the required roles
     */
    public function hasAnyRole($roles) {
        $userRole = $this->getCurrentUserRole();
        return in_array($userRole, (array)$roles);
    }
}
?>