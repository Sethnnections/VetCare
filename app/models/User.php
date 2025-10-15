<?php
class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'username', 'email', 'password', 'role', 'first_name', 'last_name', 
        'phone', 'address', 'profile_picture', 'is_active'
    ];
    protected $hidden = ['password'];
    
    public function __construct() {
        parent::__construct();
    }
    
    // ==================== AUTHENTICATION METHODS ====================
    
    /**
     * Authenticate user with email and password
     */
    public function authenticate($email, $password) {
        $user = $this->findBy('email', $email);
        
        if ($user && $user['is_active'] == 1) {
            if (verifyPassword($password, $user['password'])) {
                return $user;
            }
        }
        
        return false;
    }
    
    /**
     * Check if email exists and user is active
     */
    public function emailExists($email) {
        $user = $this->findBy('email', $email);
        return $user && $user['is_active'] == 1;
    }
    
    /**
     * Check if username exists
     */
    public function usernameExists($username) {
        $user = $this->findBy('username', $username);
        return $user !== false;
    }
    
    // ==================== USER MANAGEMENT METHODS ====================
    
    /**
     * Create new user with hashed password
     */
    public function createUser($userData) {
        // Hash password before saving
        if (isset($userData['password'])) {
            $userData['password'] = hashPassword($userData['password']);
        }
        
        // Set default values
        $userData['is_active'] = $userData['is_active'] ?? 1;
        
        try {
            return $this->create($userData);
        } catch (Exception $e) {
            logError("User creation failed: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update user information
     */
    public function updateUser($userId, $userData) {
        // Hash password if provided
        if (isset($userData['password']) && !empty($userData['password'])) {
            $userData['password'] = hashPassword($userData['password']);
        } else {
            unset($userData['password']);
        }
        
        try {
            return $this->update($userId, $userData);
        } catch (Exception $e) {
            logError("User update failed: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update user profile information
     */
    public function updateProfile($userId, $profileData) {
        $allowedFields = ['first_name', 'last_name', 'phone', 'address', 'profile_picture'];
        $filteredData = array_intersect_key($profileData, array_flip($allowedFields));
        
        return $this->update($userId, $filteredData);
    }
    
    /**
     * Change user password
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        $user = $this->find($userId);
        if (!$user) {
            return ['success' => false, 'error' => 'User not found'];
        }
        
        // Verify current password
        if (!verifyPassword($currentPassword, $user['password'])) {
            return ['success' => false, 'error' => 'Current password is incorrect'];
        }
        
        // Validate new password
        if (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
            return ['success' => false, 'error' => 'New password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'];
        }
        
        // Update password
        $hashedPassword = hashPassword($newPassword);
        $updated = $this->update($userId, ['password' => $hashedPassword]);
        
        if ($updated) {
            logActivity("Password changed for user ID: {$userId}");
            return ['success' => true, 'message' => 'Password updated successfully'];
        }
        
        return ['success' => false, 'error' => 'Failed to update password'];
    }
    
    /**
     * Update last login timestamp
     */
    public function updateLastLogin($userId) {
        $sql = "UPDATE {$this->table} SET last_login = NOW() WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }
    
    // ==================== USER STATUS METHODS ====================
    
    /**
     * Activate user account
     */
    public function activateUser($userId) {
        return $this->update($userId, ['is_active' => 1]);
    }
    
    /**
     * Deactivate user account
     */
    public function deactivateUser($userId) {
        return $this->update($userId, ['is_active' => 0]);
    }
    
    /**
     * Check if user is active
     */
    public function isActive($userId) {
        $user = $this->find($userId);
        return $user && $user['is_active'] == 1;
    }
    
    // ==================== ROLE-BASED METHODS ====================
    
    /**
     * Get users by role
     */
    public function getUsersByRole($role) {
        $sql = "SELECT * FROM {$this->table} WHERE role = :role AND is_active = 1 ORDER BY first_name, last_name";
        $results = fetchAll($sql, ['role' => $role]);
        return array_map([$this, 'hideFields'], $results);
    }
    
    /**
     * Get all active users
     */
    public function getActiveUsers() {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY first_name, last_name";
        $results = fetchAll($sql);
        return array_map([$this, 'hideFields'], $results);
    }
    
    /**
     * Check if user is admin
     */
    public function isAdmin($userId) {
        $user = $this->find($userId);
        return $user && $user['role'] === ROLE_ADMIN;
    }
    
    /**
     * Check if user is veterinary
     */
    public function isVeterinary($userId) {
        $user = $this->find($userId);
        return $user && $user['role'] === ROLE_VETERINARY;
    }
    
    /**
     * Check if user is client
     */
    public function isClient($userId) {
        $user = $this->find($userId);
        return $user && $user['role'] === ROLE_CLIENT;
    }
    
    // ==================== PROFILE METHODS ====================
    
    /**
     * Get user profile with full details
     */
    public function getProfile($userId) {
        $user = $this->find($userId);
        if ($user) {
            return $this->hideFields($user);
        }
        return false;
    }
    
    /**
     * Get user's full name
     */
    public function getFullName($userId) {
        $user = $this->find($userId);
        if ($user && !empty($user['first_name']) && !empty($user['last_name'])) {
            return trim($user['first_name'] . ' ' . $user['last_name']);
        } elseif ($user) {
            return $user['username'];
        }
        return 'Unknown User';
    }
    
    /**
     * Get user's display name with role
     */
    public function getDisplayName($userId) {
        $user = $this->find($userId);
        if ($user) {
            $name = $this->getFullName($userId);
            return $name . ' (' . ucfirst($user['role']) . ')';
        }
        return 'Unknown User';
    }
    
    // ==================== VALIDATION METHODS ====================
    
    /**
     * Validate user data
     */
    public function validate($data, $id = null) {
        $errors = [];
        
        // Required fields
        $required = ['email', 'role'];
        if (!$id) {
            $required[] = 'password';
            $required[] = 'username';
        }
        
        $errors = array_merge($errors, validateRequired($required, $data));
        
        // Username validation (only for new users or if username is being changed)
        if (!empty($data['username'])) {
            if (strlen($data['username']) < 3) {
                $errors['username'] = 'Username must be at least 3 characters';
            } else {
                $existingUser = $this->findBy('username', $data['username']);
                if ($existingUser && (!$id || $existingUser['user_id'] != $id)) {
                    $errors['username'] = 'Username already exists';
                }
            }
        }
        
        // Email validation
        if (!empty($data['email'])) {
            if (!validateEmail($data['email'])) {
                $errors['email'] = 'Invalid email format';
            } else {
                $existingUser = $this->findBy('email', $data['email']);
                if ($existingUser && (!$id || $existingUser['user_id'] != $id)) {
                    $errors['email'] = 'Email already exists';
                }
            }
        }
        
        // Phone validation
        if (!empty($data['phone']) && !validatePhone($data['phone'])) {
            $errors['phone'] = 'Invalid phone number format';
        }
        
        // Password validation
        if (!empty($data['password'])) {
            if (strlen($data['password']) < PASSWORD_MIN_LENGTH) {
                $errors['password'] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
            }
        }
        
        // Role validation
        $allowedRoles = [ROLE_ADMIN, ROLE_VETERINARY, ROLE_CLIENT];
        if (!empty($data['role']) && !in_array($data['role'], $allowedRoles)) {
            $errors['role'] = 'Invalid role selected';
        }
        
        return $errors;
    }
    
    // ==================== SEARCH & STATISTICS ====================
    
    /**
     * Search users by term
     */
    public function searchUsers($term) {
        $columns = ['username', 'email', 'first_name', 'last_name', 'phone'];
        $results = $this->search($term, $columns);
        return array_map([$this, 'hideFields'], $results);
    }
    
    /**
     * Get user statistics
     */
    public function getStats() {
        return [
            'total' => $this->count(),
            'active' => $this->count(['is_active' => 1]),
            'inactive' => $this->count(['is_active' => 0]),
            'admin' => $this->count(['role' => ROLE_ADMIN]),
            'veterinary' => $this->count(['role' => ROLE_VETERINARY]),
            'client' => $this->count(['role' => ROLE_CLIENT])
        ];
    }
    
    // ==================== HELPER METHODS ====================
    
    /**
     * Get all users with pagination
     */
    public function getAllUsers($page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        $users = $this->findAll($offset, $perPage);
        
        return [
            'data' => array_map([$this, 'hideFields'], $users),
            'total' => $this->count(),
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($this->count() / $perPage)
        ];
    }
    
    /**
     * Override find to hide sensitive fields
     */
    public function find($id) {
        $user = parent::find($id);
        if ($user) {
            return $this->hideFields($user);
        }
        return false;
    }
    
    /**
     * Override findBy to hide sensitive fields
     */
    public function findBy($column, $value) {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$value]);
        $user = $stmt->fetch();
        
        // Don't hide password for authentication purposes
        if ($user && $column !== 'email') {
            return $this->hideFields($user);
        }
        
        return $user;
    }
}