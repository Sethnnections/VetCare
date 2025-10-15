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
            PDO::ATTR_PERSISTENT => false,
        ];
        
        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            logError("Database connection failed: " . $this->error);
            throw new Exception("Database connection failed. Please check your configuration.");
        }
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    public function testConnection() {
        try {
            $this->pdo->query("SELECT 1");
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}

// Global database helper functions
function fetchAll($sql, $params = []) {
    global $database;
    try {
        $stmt = $database->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logError("Database fetchAll error: " . $e->getMessage() . " | SQL: " . $sql);
        throw $e;
    }
}

function fetchOne($sql, $params = []) {
    global $database;
    try {
        $stmt = $database->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    } catch (PDOException $e) {
        logError("Database fetchOne error: " . $e->getMessage() . " | SQL: " . $sql);
        throw $e;
    }
}

function execute($sql, $params = []) {
    global $database;
    try {
        $stmt = $database->getConnection()->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        logError("Database execute error: " . $e->getMessage() . " | SQL: " . $sql);
        throw $e;
    }
}

function lastInsertId() {
    global $database;
    return $database->getConnection()->lastInsertId();
}

// Initialize database connection
try {
    $database = new Database();
    
    if (DEBUG_MODE && !$database->testConnection()) {
        logError("Database connection test failed");
    }
} catch (Exception $e) {
    if (DEBUG_MODE) {
        die("Database Error: " . $e->getMessage());
    } else {
        die("System temporarily unavailable. Please try again later.");
    }
}
?>