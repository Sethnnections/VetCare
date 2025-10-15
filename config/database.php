<?php
// Load constants first
require_once 'constants.php';

class Database {
    private $host = DB_HOST;
    private $dbname = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    
    private $pdo;
    private $error;
    
    public function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            throw new Exception("Database connection failed: " . $this->error);
        }
    }
    
    public function getConnection() {
        return $this->pdo;
    }
}

// Global database helper functions
// These functions are now available everywhere because helpers.php is loaded first

function fetchAll($sql, $params = []) {
    global $database;
    $stmt = $database->getConnection()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function fetchOne($sql, $params = []) {
    global $database;
    $stmt = $database->getConnection()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
}

function execute($sql, $params = []) {
    global $database;
    $stmt = $database->getConnection()->prepare($sql);
    return $stmt->execute($params);
}

// Initialize database connection
$database = new Database();
?>