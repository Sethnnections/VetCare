<?php

class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'status', 'profile_image'
    ];
    protected $hidden = ['password'];
    
    // User properties
    private $userId;
    private $name;
    private $email;
    private $password;
    private $role;
    private $phone;
    private $status;
    private $profileImage;
    private $createdAt;
    private $updatedAt;
    
    // Getters
    public function getUserId() {
        return $this->userId;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function getRole() {
        return $this->role;
    }
    
    public function getPhone() {
        return $this->phone;
    }
    
    public function getStatus() {
        return $this->status;
    }
    
    public function getProfileImage() {
        return $this->profileImage;
    }
    
    public function getCreatedAt() {
        return $this->createdAt;
    }
    
    public function getUpdatedAt() {
        return $this->updatedAt;
    }
    
    // Setters
    public function setUserId($userId) {
        $this->userId = $userId;
    }
    
    public function setName($name) {
        $this->name = sanitize($name);
    }
    
    public function setEmail($email) {
        $this->email = strtolower(sanitize($email));
    }
    
    public function setPassword($password) {
        $this->password = hashPassword($password);
    }
    
    public function setRole($role) {
        $allowedRoles = [ROLE_ADMIN, ROLE_VETERINARY, ROLE_CLIENT];
        if (in_array($role, $allowedRoles)) {
            $this->role = $role;
        }
    }
    
    public function setPhone($phone) {
        $this->phone = sanitize($phone);
    }
    
    public function setStatus($status) {
        $this->status = (int)$status;
    }
    
    public function setProfileImage($profileImage) {
        $this->profileImage = sanitize($profileImage);
    }
    
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }
    
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
    }
    
    // Business logic methods
    public function authenticate($email, $password) {
        $user = $this->findBy('email', $email);
        
        if ($user && $user['status'] == STATUS_ACTIVE) {
            // Verify password against the stored hash
            $sql = "SELECT password FROM {$this->table} WHERE email = :email";
            $result = fetchOne($sql, ['email' => $email]);
            
            if ($result && verifyPassword($password, $result['password'])) {
                return $user;
            }
        }
        
        return false;
    }
    
    public function createUser($userData) {
        // Hash password before saving
        if (isset($userData['password'])) {
            $userData['password'] = hashPassword($userData['password']);
        }
        
        // Set default status
        $userData['status'] = $userData['status'] ?? STATUS_ACTIVE;
        
        return $this->create($userData);
    }
    
    public function updateUser($userId, $userData) {
        // Hash password if provided
        if (isset($userData['password']) && !empty($userData['password'])) {
            $userData['password'] = hashPassword($userData['password']);
        } else {
            unset($userData['password']);
        }
        
        return $this->update($userId, $userData);
    }
    
    public function changePassword($userId, $currentPassword, $newPassword) {
        $user = $this->find($userId);
        if (!$user) {
            return ['success' => false, 'error' => 'User not found'];
        }
        
        // Get current password hash
        $sql = "SELECT password FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $result = fetchOne($sql, ['id' => $userId]);
        
        if (!$result || !verifyPassword($currentPassword, $result['password'])) {
            return ['success' => false, 'error' => 'Current password is incorrect'];
        }
        
        $hashedPassword = hashPassword($newPassword);
        $updated = $this->update($userId, ['password' => $hashedPassword]);
        
        return [
            'success' => $updated > 0,
            'message' => $updated > 0 ? 'Password updated successfully' : 'Failed to update password'
        ];
    }
    
    public function getUsersByRole($role) {
        $sql = "SELECT * FROM {$this->table} WHERE role = :role AND status = :status ORDER BY name";
        $results = fetchAll($sql, ['role' => $role, 'status' => STATUS_ACTIVE]);
        return array_map([$this, 'hideFields'], $results);
    }
    
    public function getActiveUsers() {
        return $this->findAllBy('status', STATUS_ACTIVE);
    }
    
    public function findAllBy($column, $value) {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value ORDER BY name";
        $results = fetchAll($sql, ['value' => $value]);
        return array_map([$this, 'hideFields'], $results);
    }
    
    public function deactivateUser($userId) {
        return $this->update($userId, ['status' => STATUS_INACTIVE]);
    }
    
    public function activateUser($userId) {
        return $this->update($userId, ['status' => STATUS_ACTIVE]);
    }
    
    public function isActive($userId) {
        $user = $this->find($userId);
        return $user && $user['status'] == STATUS_ACTIVE;
    }
    
    public function isAdmin($userId) {
        $user = $this->find($userId);
        return $user && $user['role'] === ROLE_ADMIN;
    }
    
    public function isVeterinary($userId) {
        $user = $this->find($userId);
        return $user && $user['role'] === ROLE_VETERINARY;
    }
    
    public function isClient($userId) {
        $user = $this->find($userId);
        return $user && $user['role'] === ROLE_CLIENT;
    }
    
    public function getFullName() {
        return $this->name;
    }
    
    public function getDisplayName() {
        return $this->name . ' (' . ucfirst($this->role) . ')';
    }
    
    // Validation
    public function validate($data, $id = null) {
        $errors = [];
        
        // Required fields
        $required = ['name', 'email', 'role'];
        if (!$id) {
            $required[] = 'password';
        }
        
        $errors = array_merge($errors, validateRequired($required, $data));
        
        // Email validation
        if (!empty($data['email']) && !validateEmail($data['email'])) {
            $errors['email'] = 'Invalid email format';
        }
        
        // Check unique email
        if (!empty($data['email'])) {
            $existingUser = $this->findBy('email', $data['email']);
            if ($existingUser && (!$id || $existingUser['user_id'] != $id)) {
                $errors['email'] = 'Email already exists';
            }
        }
        
        // Phone validation
        if (!empty($data['phone']) && !validatePhone($data['phone'])) {
            $errors['phone'] = 'Invalid phone number format';
        }
        
        // Password validation
        if (!empty($data['password']) && strlen($data['password']) < PASSWORD_MIN_LENGTH) {
            $errors['password'] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
        }
        
        // Role validation
        $allowedRoles = [ROLE_ADMIN, ROLE_VETERINARY, ROLE_CLIENT];
        if (!empty($data['role']) && !in_array($data['role'], $allowedRoles)) {
            $errors['role'] = 'Invalid role selected';
        }
        
        return $errors;
    }
    
    // Search users
    public function searchUsers($term) {
        return $this->search($term, ['name', 'email', 'phone']);
    }
    
    // Get user statistics
    public function getStats() {
        $stats = [
            'total' => $this->count(),
            'active' => $this->count(['status' => STATUS_ACTIVE]),
            'inactive' => $this->count(['status' => STATUS_INACTIVE]),
            'admin' => $this->count(['role' => ROLE_ADMIN]),
            'veterinary' => $this->count(['role' => ROLE_VETERINARY]),
            'client' => $this->count(['role' => ROLE_CLIENT])
        ];
        
        return $stats;
    }
    
    // Load user data into object properties
    public function load($userData) {
        if (is_array($userData)) {
            $this->setUserId($userData['user_id'] ?? null);
            $this->setName($userData['name'] ?? '');
            $this->setEmail($userData['email'] ?? '');
            $this->setRole($userData['role'] ?? '');
            $this->setPhone($userData['phone'] ?? '');
            $this->setStatus($userData['status'] ?? STATUS_ACTIVE);
            $this->setProfileImage($userData['profile_image'] ?? '');
            $this->setCreatedAt($userData['created_at'] ?? null);
            $this->setUpdatedAt($userData['updated_at'] ?? null);
        }
        return $this;
    }
    
    // Convert object to array
    public function toArray() {
        return [
            'user_id' => $this->userId,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone,
            'status' => $this->status,
            'profile_image' => $this->profileImage,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}
?>