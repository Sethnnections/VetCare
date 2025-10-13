<?php
class User {
    protected $conn;
    protected $table_name = "users";

    public $user_id;
    public $username;
    public $first_name;
    public $last_name;
    public $phone;
    public $address;
    public $profile_picture;
    public $email;
    public $password;
    public $role;
    public $is_active;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create user
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET username=:username, email=:email, password=:password, role=:role, is_active=:is_active";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->role = htmlspecialchars(strip_tags($this->role));
        
        // Hash password
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        
        // Set active status - default to active
        $this->is_active = 1;
        
        // Bind values
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":is_active", $this->is_active);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Check if user exists and is active
    public function emailExists() {
        $query = "SELECT user_id, username, password, role, is_active 
                 FROM " . $this->table_name . " 
                 WHERE email = ? AND is_active = 1 
                 LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->user_id= $row['user_id'];
            $this->username = $row['username'];
            $this->password = $row['password'];
            $this->role = $row['role'];
            $this->is_active = $row['is_active'];
            return true;
        }
        return false;
    }

    // Get user by user_id
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id= ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->role = $row['role'];
            $this->is_active = $row['is_active'];
        }
    }

    // Get all users (for admin)
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Update user status
    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . " 
                 SET is_active = :is_active 
                 WHERE user_id= :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':is_active', $this->is_active);
        $stmt->bindParam(':user_id', $this->user_id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Get database connection
    public function getConnection() {
        return $this->conn;
    }

 // Add these methods to your existing User class

public function updateProfile() {
    $query = "UPDATE " . $this->table_name . " 
             SET first_name = :first_name, 
                 last_name = :last_name, 
                 phone = :phone, 
                 address = :address,
                 updated_at = CURRENT_TIMESTAMP
             WHERE user_id= :user_id";
    
    $stmt = $this->conn->prepare($query);
    
    // Sanitize
    $this->first_name = htmlspecialchars(strip_tags($this->first_name));
    $this->last_name = htmlspecialchars(strip_tags($this->last_name));
    $this->phone = htmlspecialchars(strip_tags($this->phone));
    $this->address = htmlspecialchars(strip_tags($this->address));
    
    // Bind values
    $stmt->bindParam(":first_name", $this->first_name);
    $stmt->bindParam(":last_name", $this->last_name);
    $stmt->bindParam(":phone", $this->phone);
    $stmt->bindParam(":address", $this->address);
    $stmt->bindParam(":user_id", $this->user_id);
    
    if($stmt->execute()) {
        return true;
    }
    return false;
}

public function getProfile() {
    $query = "SELECT * FROM " . $this->table_name . " WHERE user_id= ? LIMIT 0,1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $this->user_id);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($row) {
        $this->username = $row['username'];
        $this->email = $row['email'];
        $this->role = $row['role'];
        $this->first_name = $row['first_name'];
        $this->last_name = $row['last_name'];
        $this->phone = $row['phone'];
        $this->address = $row['address'];
        $this->profile_picture = $row['profile_picture'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
        return true;
    }
    return false;
}

public function updatePassword($new_password) {
    $query = "UPDATE " . $this->table_name . " 
             SET password = :password, 
                 updated_at = CURRENT_TIMESTAMP 
             WHERE user_id= :user_id";
    
    $stmt = $this->conn->prepare($query);
    
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    $stmt->bindParam(":password", $hashed_password);
    $stmt->bindParam(":user_id", $this->user_id);
    
    if($stmt->execute()) {
        return true;
    }
    return false;
}
}
?>