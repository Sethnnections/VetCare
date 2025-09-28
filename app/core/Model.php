<?php

abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = ['password'];
    protected $timestamps = true;
    
   public function __construct() {
    }

    protected function getDatabase() {
        if ($this->db === null) {
            $this->db = getDB();
        }
        return $this->db;
    }
        
    // Create a new record
    public function create($data) {
        $data = $this->filterFillable($data);
        
        if ($this->timestamps) {
            $data['created_at'] = getCurrentDateTime();
            $data['updated_at'] = getCurrentDateTime();
        }
        
        return insertRecord($this->table, $data);
    }
    
    // Find a record by ID
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $result = fetchOne($sql, ['id' => $id]);
        return $result ? $this->hideFields($result) : null;
    }
    
    // Find a record by condition
    public function findBy($column, $value) {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value";
        $result = fetchOne($sql, ['value' => $value]);
        return $result ? $this->hideFields($result) : null;
    }
    
    // Get all records
    public function all($orderBy = null) {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        $results = fetchAll($sql);
        return array_map([$this, 'hideFields'], $results);
    }
    
    // Get records with pagination
    public function paginate($page = 1, $perPage = RECORDS_PER_PAGE, $conditions = [], $orderBy = null) {
        $offset = ($page - 1) * $perPage;
        
        $whereClause = '';
        $params = [];
        
        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $column => $value) {
                $whereParts[] = "{$column} = :{$column}";
                $params[$column] = $value;
            }
            $whereClause = 'WHERE ' . implode(' AND ', $whereParts);
        }
        
        $orderClause = $orderBy ? "ORDER BY {$orderBy}" : '';
        
        // Get total count
        $countSql = "SELECT COUNT(*) as count FROM {$this->table} {$whereClause}";
        $totalRecords = fetchOne($countSql, $params)['count'];
        
        // Get records
        $sql = "SELECT * FROM {$this->table} {$whereClause} {$orderClause} LIMIT {$perPage} OFFSET {$offset}";
        $results = fetchAll($sql, $params);
        
        return [
            'data' => array_map([$this, 'hideFields'], $results),
            'pagination' => paginate($totalRecords, $page, $perPage)
        ];
    }
    
    // Update a record
    public function update($id, $data) {
        $data = $this->filterFillable($data);
        
        if ($this->timestamps) {
            $data['updated_at'] = getCurrentDateTime();
        }
        
        return updateRecord(
            $this->table, 
            $data, 
            "{$this->primaryKey} = :id", 
            ['id' => $id]
        );
    }
    
    // Delete a record
    public function delete($id) {
        return deleteRecord($this->table, "{$this->primaryKey} = :id", ['id' => $id]);
    }
    
    // Search records
    public function search($term, $columns = []) {
        if (empty($columns)) {
            return [];
        }
        
        $whereParts = [];
        $params = [];
        
        foreach ($columns as $column) {
            $whereParts[] = "{$column} LIKE :term";
        }
        
        $whereClause = implode(' OR ', $whereParts);
        $params['term'] = "%{$term}%";
        
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause}";
        $results = fetchAll($sql, $params);
        
        return array_map([$this, 'hideFields'], $results);
    }
    
    // Count records
    public function count($conditions = []) {
        $whereClause = '';
        $params = [];
        
        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $column => $value) {
                $whereParts[] = "{$column} = :{$column}";
                $params[$column] = $value;
            }
            $whereClause = 'WHERE ' . implode(' AND ', $whereParts);
        }
        
        return countRecords($this->table, $whereClause ?: '1=1', $params);
    }
    
    // Check if record exists
    public function exists($id) {
        return $this->find($id) !== null;
    }
    
    // Filter fillable fields
    protected function filterFillable($data) {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    // Hide sensitive fields
    protected function hideFields($data) {
        if (!is_array($data)) {
            return $data;
        }
        
        foreach ($this->hidden as $field) {
            unset($data[$field]);
        }
        
        return $data;
    }
    
    // Get table name
    public function getTable() {
        return $this->table;
    }
    
    // Get primary key
    public function getPrimaryKey() {
        return $this->primaryKey;
    }
    
    // Execute raw SQL
    protected function query($sql, $params = []) {
        return executeQuery($sql, $params);
    }
    
    // Begin transaction
    public function beginTransaction() {
        $this->getDatabase()->beginTransaction();
    }
    
    // Commit transaction
    public function commit() {
        $this->getDatabase()->commit();
    }
    
    // Rollback transaction
    public function rollback() {
        $this->getDatabase()->rollback();
    }
    
    // Validation method to be overridden in child classes
    public function validate($data, $id = null) {
        return [];
    }
    
    // Save method (create or update)
    public function save($data, $id = null) {
        $errors = $this->validate($data, $id);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        try {
            if ($id) {
                $result = $this->update($id, $data);
                return ['success' => true, 'id' => $id, 'updated' => $result];
            } else {
                $newId = $this->create($data);
                return ['success' => true, 'id' => $newId, 'created' => true];
            }
        } catch (Exception $e) {
            logError("Model save error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Database error occurred'];
        }
    }
}
?>