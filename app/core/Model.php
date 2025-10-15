<?php
class Model {
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    
    protected $db;
    
    public function __construct() {
        global $database;
        $this->db = $database->getConnection();
    }
    
    // Find record by ID
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    // Find all records
    public function findAll($offset = null, $limit = null) {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        if ($offset !== null) {
            $sql .= " OFFSET " . (int)$offset;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Find by column
    public function findBy($column, $value) {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$value]);
        return $stmt->fetch();
    }
    
    // Create new record
    public function create($data) {
        $filteredData = array_intersect_key($data, array_flip($this->fillable));
        
        $columns = implode(', ', array_keys($filteredData));
        $placeholders = ':' . implode(', :', array_keys($filteredData));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute($filteredData);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
    
    // Update record
    public function update($id, $data) {
        $filteredData = array_intersect_key($data, array_flip($this->fillable));
        
        $setClause = implode(', ', array_map(function($column) {
            return "{$column} = :{$column}";
        }, array_keys($filteredData)));
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        
        $filteredData['id'] = $id;
        
        try {
            return $stmt->execute($filteredData);
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
    
    // Delete record
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    // Count records
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if (!empty($conditions)) {
            $whereClause = implode(' AND ', array_map(function($column) {
                return "{$column} = :{$column}";
            }, array_keys($conditions)));
            $sql .= " WHERE {$whereClause}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($conditions);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    
    // Search records
    public function search($term, $columns = []) {
        if (empty($columns)) {
            $columns = $this->fillable;
        }
        
        $searchConditions = implode(' OR ', array_map(function($column) {
            return "{$column} LIKE :term";
        }, $columns));
        
        $sql = "SELECT * FROM {$this->table} WHERE {$searchConditions}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['term' => "%{$term}%"]);
        return $stmt->fetchAll();
    }
    
    // Check if record exists
    public function exists($id) {
        $sql = "SELECT 1 FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch() !== false;
    }
    
    // Execute raw query
    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    // Hide sensitive fields
    protected function hideFields($data) {
        foreach ($this->hidden as $field) {
            unset($data[$field]);
        }
        return $data;
    }
}
?>