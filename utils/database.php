<?php

/**
 * Get database connection
 * @return PDO
 */
function getDB() {
    static $db = null;
    
    if ($db === null) {
        try {
            // Use constants from constants.php
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $db = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            logError("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection error");
        }
    }
    
    return $db;
}

/**
 * Execute a query and return results
 * @param string $sql
 * @param array $params
 * @return array
 */
function fetchAll($sql, $params = []) {
    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Execute a query and return single result
 * @param string $sql
 * @param array $params
 * @return array|null
 */
function fetchOne($sql, $params = []) {
    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch() ?: null;
}

/**
 * Execute a query (INSERT, UPDATE, DELETE)
 * @param string $sql
 * @param array $params
 * @return int Number of affected rows
 */
function executeQuery($sql, $params = []) {
    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

/**
 * Insert a record
 * @param string $table
 * @param array $data
 * @return int Last insert ID
 */
function insertRecord($table, $data) {
    $columns = implode(', ', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));
    
    $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
    $stmt = getDB()->prepare($sql);
    $stmt->execute($data);
    
    return getDB()->lastInsertId();
}

/**
 * Update records
 * @param string $table
 * @param array $data
 * @param string $where
 * @param array $whereParams
 * @return int Number of affected rows
 */
function updateRecord($table, $data, $where, $whereParams = []) {
    $setParts = [];
    foreach (array_keys($data) as $column) {
        $setParts[] = "{$column} = :{$column}";
    }
    $setClause = implode(', ', $setParts);
    
    $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
    $stmt = getDB()->prepare($sql);
    $stmt->execute(array_merge($data, $whereParams));
    
    return $stmt->rowCount();
}

/**
 * Delete records
 * @param string $table
 * @param string $where
 * @param array $whereParams
 * @return int Number of affected rows
 */
function deleteRecord($table, $where, $whereParams = []) {
    $sql = "DELETE FROM {$table} WHERE {$where}";
    $stmt = getDB()->prepare($sql);
    $stmt->execute($whereParams);
    return $stmt->rowCount();
}

/**
 * Count records
 * @param string $table
 * @param string $where
 * @param array $params
 * @return int
 */
function countRecords($table, $where = '1=1', $params = []) {
    $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$where}";
    $result = fetchOne($sql, $params);
    return $result ? (int)$result['count'] : 0;
}


