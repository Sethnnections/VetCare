<?php

class Animal extends Model {
    protected $table = 'animals';
    protected $primaryKey = 'animal_id';
    protected $fillable = [
        'client_id', 'name', 'species', 'breed', 'gender', 'birth_date', 
        'color', 'weight', 'microchip', 'status', 'notes'
    ];
    
    // Animal properties
    private $animalId;
    private $clientId;
    private $name;
    private $species;
    private $breed;
    private $gender;
    private $birthDate;
    private $color;
    private $weight;
    private $microchip;
    private $status;
    private $notes;
    private $createdAt;
    private $updatedAt;
    
    // Getters
    public function getAnimalId() { return $this->animalId; }
    public function getClientId() { return $this->clientId; }
    public function getName() { return $this->name; }
    public function getSpecies() { return $this->species; }
    public function getBreed() { return $this->breed; }
    public function getGender() { return $this->gender; }
    public function getBirthDate() { return $this->birthDate; }
    public function getColor() { return $this->color; }
    public function getWeight() { return $this->weight; }
    public function getMicrochip() { return $this->microchip; }
    public function getStatus() { return $this->status; }
    public function getNotes() { return $this->notes; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
    
    // Setters
    public function setAnimalId($animalId) { $this->animalId = $animalId; }
    public function setClientId($clientId) { $this->clientId = (int)$clientId; }
    public function setName($name) { $this->name = sanitize($name); }
    public function setSpecies($species) { $this->species = sanitize($species); }
    public function setBreed($breed) { $this->breed = sanitize($breed); }
    public function setGender($gender) { $this->gender = sanitize($gender); }
    public function setBirthDate($birthDate) { $this->birthDate = $birthDate; }
    public function setColor($color) { $this->color = sanitize($color); }
    public function setWeight($weight) { $this->weight = (float)$weight; }
    public function setMicrochip($microchip) { $this->microchip = sanitize($microchip); }
    public function setStatus($status) { $this->status = (int)$status; }
    public function setNotes($notes) { $this->notes = sanitize($notes); }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
    public function setUpdatedAt($updatedAt) { $this->updatedAt = $updatedAt; }
    
    // Business logic methods
    public function getAnimalsByClient($clientId) {
        $sql = "SELECT * FROM {$this->table} WHERE client_id = :client_id ORDER BY name";
        return fetchAll($sql, ['client_id' => $clientId]);
    }
    
    public function getAnimalWithClient($animalId) {
        $sql = "SELECT a.*, c.name as client_name, c.phone as client_phone 
                FROM {$this->table} a 
                JOIN clients c ON a.client_id = c.client_id 
                WHERE a.animal_id = :animal_id";
        return fetchOne($sql, ['animal_id' => $animalId]);
    }
    
    public function getAnimalTreatments($animalId) {
        $sql = "SELECT * FROM treatments WHERE animal_id = :animal_id ORDER BY treatment_date DESC";
        return fetchAll($sql, ['animal_id' => $animalId]);
    }
    
    public function getAnimalVaccinations($animalId) {
        $sql = "SELECT * FROM vaccines WHERE animal_id = :animal_id ORDER BY vaccine_date DESC";
        return fetchAll($sql, ['animal_id' => $animalId]);
    }
    
    public function getAnimalBillings($animalId) {
        $sql = "SELECT * FROM billings WHERE animal_id = :animal_id ORDER BY billing_date DESC";
        return fetchAll($sql, ['animal_id' => $animalId]);
    }
    
    public function getAnimalReminders($animalId) {
        $sql = "SELECT * FROM reminders WHERE animal_id = :animal_id AND status = 'pending' ORDER BY reminder_date ASC";
        return fetchAll($sql, ['animal_id' => $animalId]);
    }
    
    public function getAge() {
        if (!$this->birthDate) return null;
        
        $birth = new DateTime($this->birthDate);
        $today = new DateTime();
        $age = $today->diff($birth);
        
        $years = $age->y;
        $months = $age->m;
        
        if ($years > 0) {
            return $years . ' year' . ($years > 1 ? 's' : '') . 
                   ($months > 0 ? ', ' . $months . ' month' . ($months > 1 ? 's' : '') : '');
        } else {
            return $months . ' month' . ($months > 1 ? 's' : '');
        }
    }
    
    public function getDisplayName() {
        return $this->name . ' (' . $this->species . ')';
    }
    
    public function isActive() {
        return $this->status == STATUS_ACTIVE;
    }
    
    public function getLastTreatment() {
        $sql = "SELECT * FROM treatments WHERE animal_id = :animal_id ORDER BY treatment_date DESC LIMIT 1";
        return fetchOne($sql, ['animal_id' => $this->animalId]);
    }
    
    public function getNextVaccination() {
        $sql = "SELECT * FROM vaccines WHERE animal_id = :animal_id AND vaccine_date > NOW() ORDER BY vaccine_date ASC LIMIT 1";
        return fetchOne($sql, ['animal_id' => $this->animalId]);
    }
    
    public function getTotalBilling() {
        $sql = "SELECT SUM(amount) as total FROM billings WHERE animal_id = :animal_id AND payment_status = 'paid'";
        $result = fetchOne($sql, ['animal_id' => $this->animalId]);
        return $result['total'] ?? 0;
    }
    
    public function getPendingBilling() {
        $sql = "SELECT SUM(amount) as total FROM billings WHERE animal_id = :animal_id AND payment_status = 'pending'";
        $result = fetchOne($sql, ['animal_id' => $this->animalId]);
        return $result['total'] ?? 0;
    }
    
    // Validation
    public function validate($data, $id = null) {
        $errors = [];
        
        // Required fields
        $required = ['client_id', 'name', 'species'];
        $errors = array_merge($errors, validateRequired($required, $data));
        
        // Validate client exists
        if (!empty($data['client_id'])) {
            $clientModel = new Client();
            if (!$clientModel->exists($data['client_id'])) {
                $errors['client_id'] = 'Invalid client selected';
            }
        }
        
        // Validate gender
        if (!empty($data['gender'])) {
            $allowedGenders = ['male', 'female', 'unknown'];
            if (!in_array(strtolower($data['gender']), $allowedGenders)) {
                $errors['gender'] = 'Invalid gender selected';
            }
        }
        
        // Validate birth date
        if (!empty($data['birth_date'])) {
            $birthDate = strtotime($data['birth_date']);
            if (!$birthDate || $birthDate > time()) {
                $errors['birth_date'] = 'Invalid birth date';
            }
        }
        
        // Validate weight
        if (!empty($data['weight']) && (!is_numeric($data['weight']) || $data['weight'] <= 0)) {
            $errors['weight'] = 'Weight must be a positive number';
        }
        
        // Check unique microchip if provided
        if (!empty($data['microchip'])) {
            $existingAnimal = $this->findBy('microchip', $data['microchip']);
            if ($existingAnimal && (!$id || $existingAnimal['animal_id'] != $id)) {
                $errors['microchip'] = 'Microchip number already exists';
            }
        }
        
        return $errors;
    }
    
    // Search animals
    public function searchAnimals($term) {
        $sql = "SELECT a.*, c.name as client_name 
                FROM {$this->table} a 
                JOIN clients c ON a.client_id = c.client_id 
                WHERE a.name LIKE :term 
                OR a.species LIKE :term 
                OR a.breed LIKE :term 
                OR a.microchip LIKE :term 
                OR c.name LIKE :term 
                ORDER BY a.name";
        return fetchAll($sql, ['term' => "%{$term}%"]);
    }
    
    // Get animal statistics
    public function getStats() {
        $stats = [
            'total' => $this->count(),
            'active' => $this->count(['status' => STATUS_ACTIVE]),
            'inactive' => $this->count(['status' => STATUS_INACTIVE])
        ];
        
        // Get animals by species
        $sql = "SELECT species, COUNT(*) as count FROM {$this->table} 
                WHERE status = :status GROUP BY species ORDER BY count DESC";
        $speciesCounts = fetchAll($sql, ['status' => STATUS_ACTIVE]);
        $stats['by_species'] = $speciesCounts;
        
        // Get animals by gender
        $sql = "SELECT gender, COUNT(*) as count FROM {$this->table} 
                WHERE status = :status GROUP BY gender ORDER BY count DESC";
        $genderCounts = fetchAll($sql, ['status' => STATUS_ACTIVE]);
        $stats['by_gender'] = $genderCounts;
        
        return $stats;
    }
    
    // Load animal data into object properties
    public function load($animalData) {
        if (is_array($animalData)) {
            $this->setAnimalId($animalData['animal_id'] ?? null);
            $this->setClientId($animalData['client_id'] ?? null);
            $this->setName($animalData['name'] ?? '');
            $this->setSpecies($animalData['species'] ?? '');
            $this->setBreed($animalData['breed'] ?? '');
            $this->setGender($animalData['gender'] ?? '');
            $this->setBirthDate($animalData['birth_date'] ?? null);
            $this->setColor($animalData['color'] ?? '');
            $this->setWeight($animalData['weight'] ?? 0);
            $this->setMicrochip($animalData['microchip'] ?? '');
            $this->setStatus($animalData['status'] ?? STATUS_ACTIVE);
            $this->setNotes($animalData['notes'] ?? '');
            $this->setCreatedAt($animalData['created_at'] ?? null);
            $this->setUpdatedAt($animalData['updated_at'] ?? null);
        }
        return $this;
    }



   
    
    // Convert object to array
    public function toArray() {
        return [
            'animal_id' => $this->animalId,
            'client_id' => $this->clientId,
            'name' => $this->name,
            'species' => $this->species,
            'breed' => $this->breed,
            'gender' => $this->gender,
            'birth_date' => $this->birthDate,
            'color' => $this->color,
            'weight' => $this->weight,
            'microchip' => $this->microchip,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }

        // In Animal.php - Complete veterinary assignment methods

    /**
     * Get animals assigned to a specific veterinary
     */
    public function getAnimalsByVeterinary($veterinaryId) {
        $sql = "SELECT a.*, 
                    u.first_name as client_first_name, 
                    u.last_name as client_last_name,
                    u.phone as client_phone,
                    u.email as client_email
                FROM {$this->table} a 
                JOIN clients c ON a.client_id = c.client_id 
                JOIN users u ON c.user_id = u.user_id
                WHERE a.assigned_veterinary = ? 
                AND a.status = 'active' 
                ORDER BY a.name";
        
        return fetchAll($sql, [$veterinaryId]);
    }

    /**
     * Check if animal is assigned to veterinary
     */
    public function isAssignedToVeterinary($animalId, $veterinaryId) {
        $sql = "SELECT animal_id FROM {$this->table} 
                WHERE animal_id = ? AND assigned_veterinary = ? AND status = 'active'";
        $result = fetchOne($sql, [$animalId, $veterinaryId]);
        return $result !== false;
    }

    /**
     * Assign animal to veterinary
     */
    public function assignToVeterinary($animalId, $veterinaryId) {
        $sql = "UPDATE {$this->table} SET assigned_veterinary = ? WHERE animal_id = ?";
        return execute($sql, [$veterinaryId, $animalId]);
    }

    /**
     * Unassign animal from veterinary
     */
    public function unassignFromVeterinary($animalId) {
        $sql = "UPDATE {$this->table} SET assigned_veterinary = NULL WHERE animal_id = ?";
        return execute($sql, [$animalId]);
    }

    /**
     * Get available veterinarians (users with veterinary role)
     */
    public function getAvailableVeterinarians() {
        $sql = "SELECT user_id, username, first_name, last_name, email 
                FROM users 
                WHERE role = 'veterinary' AND is_active = 1 
                ORDER BY first_name, last_name";
        return fetchAll($sql);
    }

    /**
     * Get animals needing assignment (no veterinary assigned)
     */
    public function getUnassignedAnimals() {
        $sql = "SELECT a.*, 
                    u.first_name as client_first_name, 
                    u.last_name as client_last_name
                FROM {$this->table} a 
                JOIN clients c ON a.client_id = c.client_id 
                JOIN users u ON c.user_id = u.user_id
                WHERE a.assigned_veterinary IS NULL 
                AND a.status = 'active' 
                ORDER BY a.name";
        
        return fetchAll($sql);
    }

    /**
     * Get all veterinary assignments with details
     */
    public function getVeterinaryAssignments() {
        $sql = "SELECT a.*, 
                    vet.first_name as vet_first_name,
                    vet.last_name as vet_last_name,
                    vet.email as vet_email,
                    client.first_name as client_first_name,
                    client.last_name as client_last_name,
                    client.phone as client_phone
                FROM {$this->table} a 
                LEFT JOIN users vet ON a.assigned_veterinary = vet.user_id
                LEFT JOIN clients c ON a.client_id = c.client_id
                LEFT JOIN users client ON c.user_id = client.user_id
                WHERE a.assigned_veterinary IS NOT NULL 
                AND a.status = 'active'
                ORDER BY vet.first_name, vet.last_name, a.name";
        
        return fetchAll($sql);
    }

    /**
     * Get assignment statistics
     */

    /**
     * Get animal medication history
     */
    public function getMedicationHistory($animalId) {
        $sql = "SELECT t.*, 
                    u.first_name as vet_first_name,
                    u.last_name as vet_last_name,
                    'treatment' as type
                FROM treatments t 
                JOIN users u ON t.veterinary_id = u.user_id
                WHERE t.animal_id = ? AND t.medication_prescribed IS NOT NULL
                UNION ALL
                SELECT v.*,
                    u.first_name as vet_first_name,
                    u.last_name as vet_last_name,
                    'vaccine' as type
                FROM vaccines v
                JOIN users u ON v.administered_by = u.user_id
                WHERE v.animal_id = ?
                ORDER BY created_at DESC";
        
        return fetchAll($sql, [$animalId, $animalId]);
    }

    /**
     * Get active medications for animal
     */
    public function getActiveMedications($animalId) {
        $sql = "SELECT t.*, 
                    u.first_name as vet_first_name,
                    u.last_name as vet_last_name
                FROM treatments t 
                JOIN users u ON t.veterinary_id = u.user_id
                WHERE t.animal_id = ? 
                AND t.medication_prescribed IS NOT NULL 
                AND t.medication_prescribed != ''
                AND t.status IN ('ongoing', 'follow_up')
                ORDER BY t.treatment_date DESC";
        
        return fetchAll($sql, [$animalId]);
    }

    /**
     * Update animal status
     */
    public function updateAnimalStatus($animalId, $status) {
        return $this->update($animalId, ['status' => $status]);
    }



    /**
     * Get species statistics
     */
    public function getSpeciesStats() {
        $sql = "SELECT species, COUNT(*) as count 
                FROM {$this->table} 
                WHERE status = 'active' 
                GROUP BY species 
                ORDER BY count DESC";
        
        return fetchAll($sql);
    }

  

/**
 * Get veterinary workload statistics with error handling
 */
public function getVeterinaryWorkload() {
    try {
        $sql = "SELECT u.user_id, u.first_name, u.last_name, 
                       COUNT(DISTINCT a.animal_id) as assigned_animals,
                       COUNT(DISTINCT t.treatment_id) as active_treatments
                FROM users u
                LEFT JOIN animals a ON u.user_id = a.assigned_veterinary AND a.status = 'active'
                LEFT JOIN treatments t ON u.user_id = t.veterinary_id AND t.status IN ('ongoing', 'follow_up')
                WHERE u.role = 'veterinary' AND u.is_active = 1
                GROUP BY u.user_id, u.first_name, u.last_name
                ORDER BY assigned_animals DESC";
        
        return fetchAll($sql);
    } catch (Exception $e) {
        logError("Veterinary workload query error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get assignment statistics with error handling
 */
public function getAssignmentStats() {
    try {
        $stats = [];
        
        // Total animals
        $stats['total_animals'] = $this->count(['status' => 'active']);
        
        // Assigned animals
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE assigned_veterinary IS NOT NULL AND status = 'active'";
        $result = fetchOne($sql);
        $stats['assigned_animals'] = $result['count'] ?? 0;
        
        // Unassigned animals
        $stats['unassigned_animals'] = $stats['total_animals'] - $stats['assigned_animals'];
        
        // Veterinarians count
        $sql = "SELECT COUNT(*) as count FROM users WHERE role = 'veterinary' AND is_active = 1";
        $result = fetchOne($sql);
        $stats['total_veterinarians'] = $result['count'] ?? 0;
        
        return $stats;
    } catch (Exception $e) {
        logError("Assignment stats query error: " . $e->getMessage());
        return [
            'total_animals' => 0,
            'assigned_animals' => 0,
            'unassigned_animals' => 0,
            'total_veterinarians' => 0
        ];
    }
}

/**
 * Get all animals with details for admin - with error handling
 */
public function getAllAnimalsWithDetails($filters = []) {
    try {
        $sql = "SELECT a.*, 
                    c.client_id,
                    u.first_name as client_first_name, 
                    u.last_name as client_last_name,
                    u.email as client_email,
                    u.phone as client_phone,
                    vet.first_name as vet_first_name,
                    vet.last_name as vet_last_name
                FROM {$this->table} a 
                JOIN clients c ON a.client_id = c.client_id 
                JOIN users u ON c.user_id = u.user_id
                LEFT JOIN users vet ON a.assigned_veterinary = vet.user_id
                WHERE a.status = 'active'";
        
        $params = [];
        
        // Apply filters
        if (!empty($filters['status'])) {
            $sql .= " AND a.status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['species'])) {
            $sql .= " AND a.species = :species";
            $params['species'] = $filters['species'];
        }
        
        if (!empty($filters['assigned_veterinary'])) {
            if ($filters['assigned_veterinary'] === 'unassigned') {
                $sql .= " AND a.assigned_veterinary IS NULL";
            } else {
                $sql .= " AND a.assigned_veterinary = :veterinary_id";
                $params['veterinary_id'] = $filters['assigned_veterinary'];
            }
        }
        
        $sql .= " ORDER BY a.created_at DESC";
        
        $animals = fetchAll($sql, $params);
        
        // Add treatment and vaccine counts separately to avoid complex joins
        foreach ($animals as &$animal) {
            $animal['treatment_count'] = $this->countTreatments($animal['animal_id']);
            $animal['vaccine_count'] = $this->countVaccines($animal['animal_id']);
        }
        
        return $animals;
    } catch (Exception $e) {
        logError("Get animals with details error: " . $e->getMessage());
        return [];
    }
}

/**
 * Count treatments for an animal
 */
private function countTreatments($animalId) {
    try {
        $sql = "SELECT COUNT(*) as count FROM treatments WHERE animal_id = ?";
        $result = fetchOne($sql, [$animalId]);
        return $result['count'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Count vaccines for an animal
 */
private function countVaccines($animalId) {
    try {
        $sql = "SELECT COUNT(*) as count FROM vaccines WHERE animal_id = ?";
        $result = fetchOne($sql, [$animalId]);
        return $result['count'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Get assignment history with error handling
 */
public function getAssignmentHistory($animalId) {
    try {
        $sql = "SELECT ah.*,
                    u1.first_name as assigned_by_first_name,
                    u1.last_name as assigned_by_last_name,
                    u2.first_name as vet_first_name,
                    u2.last_name as vet_last_name
                FROM animal_assignments_history ah
                LEFT JOIN users u1 ON ah.assigned_by = u1.user_id
                LEFT JOIN users u2 ON ah.veterinary_id = u2.user_id
                WHERE ah.animal_id = ?
                ORDER BY ah.assigned_at DESC";
        
        return fetchAll($sql, [$animalId]);
    } catch (Exception $e) {
        logError("Assignment history query error: " . $e->getMessage());
        return [];
    }
}

/**
 * Log assignment in history with error handling
 */
public function logAssignment($animalId, $veterinaryId, $assignedBy, $action = 'assigned') {
    try {
        $sql = "INSERT INTO animal_assignments_history 
                (animal_id, veterinary_id, assigned_by, action, assigned_at) 
                VALUES (?, ?, ?, ?, NOW())";
        
        return execute($sql, [$animalId, $veterinaryId, $assignedBy, $action]);
    } catch (Exception $e) {
        logError("Assignment logging error: " . $e->getMessage());
        return false;
    }
}
}


?>