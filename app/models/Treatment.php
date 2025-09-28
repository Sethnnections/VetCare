<?php
// app/models/Treatment.php

class Treatment extends Model {
    protected $table = 'treatments';
    protected $primaryKey = 'treatment_id';
    protected $fillable = [
        'animal_id', 'veterinary_id', 'diagnosis', 'treatment_details', 
        'medication_prescribed', 'treatment_date', 'follow_up_date', 
        'status', 'notes', 'cost'
    ];
    
    // Treatment properties
    private $treatmentId;
    private $animalId;
    private $veterinaryId;
    private $diagnosis;
    private $treatmentDetails;
    private $medicationPrescribed;
    private $treatmentDate;
    private $followUpDate;
    private $status;
    private $notes;
    private $cost;
    private $createdAt;
    private $updatedAt;
    
    // Getters
    public function getTreatmentId() { return $this->treatmentId; }
    public function getAnimalId() { return $this->animalId; }
    public function getVeterinaryId() { return $this->veterinaryId; }
    public function getDiagnosis() { return $this->diagnosis; }
    public function getTreatmentDetails() { return $this->treatmentDetails; }
    public function getMedicationPrescribed() { return $this->medicationPrescribed; }
    public function getTreatmentDate() { return $this->treatmentDate; }
    public function getFollowUpDate() { return $this->followUpDate; }
    public function getStatus() { return $this->status; }
    public function getNotes() { return $this->notes; }
    public function getCost() { return $this->cost; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
    
    // Setters
    public function setTreatmentId($treatmentId) { $this->treatmentId = $treatmentId; }
    public function setAnimalId($animalId) { $this->animalId = (int)$animalId; }
    public function setVeterinaryId($veterinaryId) { $this->veterinaryId = (int)$veterinaryId; }
    public function setDiagnosis($diagnosis) { $this->diagnosis = sanitize($diagnosis); }
    public function setTreatmentDetails($treatmentDetails) { $this->treatmentDetails = sanitize($treatmentDetails); }
    public function setMedicationPrescribed($medicationPrescribed) { $this->medicationPrescribed = sanitize($medicationPrescribed); }
    public function setTreatmentDate($treatmentDate) { $this->treatmentDate = $treatmentDate; }
    public function setFollowUpDate($followUpDate) { $this->followUpDate = $followUpDate; }
    public function setStatus($status) { $this->status = sanitize($status); }
    public function setNotes($notes) { $this->notes = sanitize($notes); }
    public function setCost($cost) { $this->cost = (float)$cost; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
    public function setUpdatedAt($updatedAt) { $this->updatedAt = $updatedAt; }
    
    // Business logic methods
    public function getTreatmentWithDetails($treatmentId) {
        $sql = "SELECT t.*, 
                       a.name as animal_name, a.species, a.breed,
                       c.name as client_name, c.phone as client_phone,
                       u.name as veterinary_name
                FROM {$this->table} t
                JOIN animals a ON t.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                JOIN users u ON t.veterinary_id = u.user_id
                WHERE t.treatment_id = :treatment_id";
        return fetchOne($sql, ['treatment_id' => $treatmentId]);
    }
    
    public function getTreatmentsByAnimal($animalId) {
        $sql = "SELECT t.*, u.name as veterinary_name
                FROM {$this->table} t
                JOIN users u ON t.veterinary_id = u.user_id
                WHERE t.animal_id = :animal_id
                ORDER BY t.treatment_date DESC";
        return fetchAll($sql, ['animal_id' => $animalId]);
    }
    
    public function getTreatmentsByVeterinary($veterinaryId) {
        $sql = "SELECT t.*, 
                       a.name as animal_name, a.species,
                       c.name as client_name
                FROM {$this->table} t
                JOIN animals a ON t.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                WHERE t.veterinary_id = :veterinary_id
                ORDER BY t.treatment_date DESC";
        return fetchAll($sql, ['veterinary_id' => $veterinaryId]);
    }
    
    public function getUpcomingFollowUps($veterinaryId = null) {
        $sql = "SELECT t.*, 
                       a.name as animal_name, a.species,
                       c.name as client_name, c.phone as client_phone
                FROM {$this->table} t
                JOIN animals a ON t.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                WHERE t.follow_up_date IS NOT NULL 
                AND t.follow_up_date >= CURDATE()
                AND t.status != :completed_status";
        
        $params = ['completed_status' => STATUS_COMPLETED];
        
        if ($veterinaryId) {
            $sql .= " AND t.veterinary_id = :veterinary_id";
            $params['veterinary_id'] = $veterinaryId;
        }
        
        $sql .= " ORDER BY t.follow_up_date ASC";
        
        return fetchAll($sql, $params);
    }
    
    public function getOverdueFollowUps($veterinaryId = null) {
        $sql = "SELECT t.*, 
                       a.name as animal_name, a.species,
                       c.name as client_name, c.phone as client_phone
                FROM {$this->table} t
                JOIN animals a ON t.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                WHERE t.follow_up_date IS NOT NULL 
                AND t.follow_up_date < CURDATE()
                AND t.status != :completed_status";
        
        $params = ['completed_status' => STATUS_COMPLETED];
        
        if ($veterinaryId) {
            $sql .= " AND t.veterinary_id = :veterinary_id";
            $params['veterinary_id'] = $veterinaryId;
        }
        
        $sql .= " ORDER BY t.follow_up_date ASC";
        
        return fetchAll($sql, $params);
    }
    
    public function markAsCompleted($treatmentId) {
        return $this->update($treatmentId, ['status' => STATUS_COMPLETED]);
    }
    
    public function markAsFollowUp($treatmentId) {
        return $this->update($treatmentId, ['status' => TREATMENT_FOLLOW_UP]);
    }
    
    public function createTreatment($treatmentData) {
        // Set default values
        $treatmentData['treatment_date'] = $treatmentData['treatment_date'] ?? getCurrentDate();
        $treatmentData['status'] = $treatmentData['status'] ?? TREATMENT_ONGOING;
        
        return $this->create($treatmentData);
    }
    
    public function getTreatmentHistory($animalId, $limit = 10) {
        $sql = "SELECT t.*, u.name as veterinary_name
                FROM {$this->table} t
                JOIN users u ON t.veterinary_id = u.user_id
                WHERE t.animal_id = :animal_id
                ORDER BY t.treatment_date DESC
                LIMIT :limit";
        
        $stmt = $this->query($sql, ['animal_id' => $animalId]);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function getTreatmentsByDateRange($startDate, $endDate, $veterinaryId = null) {
        $sql = "SELECT t.*, 
                       a.name as animal_name, a.species,
                       c.name as client_name,
                       u.name as veterinary_name
                FROM {$this->table} t
                JOIN animals a ON t.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                JOIN users u ON t.veterinary_id = u.user_id
                WHERE t.treatment_date BETWEEN :start_date AND :end_date";
        
        $params = ['start_date' => $startDate, 'end_date' => $endDate];
        
        if ($veterinaryId) {
            $sql .= " AND t.veterinary_id = :veterinary_id";
            $params['veterinary_id'] = $veterinaryId;
        }
        
        $sql .= " ORDER BY t.treatment_date DESC";
        
        return fetchAll($sql, $params);
    }
    
    public function getTreatmentCostByPeriod($startDate, $endDate) {
        $sql = "SELECT SUM(cost) as total_cost, COUNT(*) as treatment_count
                FROM {$this->table}
                WHERE treatment_date BETWEEN :start_date AND :end_date
                AND cost > 0";
        
        return fetchOne($sql, ['start_date' => $startDate, 'end_date' => $endDate]);
    }
    
    // Validation
    public function validate($data, $id = null) {
        $errors = [];
        
        // Required fields
        $required = ['animal_id', 'veterinary_id', 'diagnosis', 'treatment_details'];
        $errors = array_merge($errors, validateRequired($required, $data));
        
        // Validate animal exists
        if (!empty($data['animal_id'])) {
            $animalModel = new Animal();
            if (!$animalModel->exists($data['animal_id'])) {
                $errors['animal_id'] = 'Invalid animal selected';
            }
        }
        
        // Validate veterinary exists
        if (!empty($data['veterinary_id'])) {
            $userModel = new User();
            $vet = $userModel->find($data['veterinary_id']);
            if (!$vet || $vet['role'] !== ROLE_VETERINARY) {
                $errors['veterinary_id'] = 'Invalid veterinary selected';
            }
        }
        
        // Validate treatment date
        if (!empty($data['treatment_date'])) {
            $treatmentDate = strtotime($data['treatment_date']);
            if (!$treatmentDate) {
                $errors['treatment_date'] = 'Invalid treatment date';
            }
        }
        
        // Validate follow-up date
        if (!empty($data['follow_up_date'])) {
            $followUpDate = strtotime($data['follow_up_date']);
            if (!$followUpDate) {
                $errors['follow_up_date'] = 'Invalid follow-up date';
            } elseif (isset($data['treatment_date']) && $followUpDate <= strtotime($data['treatment_date'])) {
                $errors['follow_up_date'] = 'Follow-up date must be after treatment date';
            }
        }
        
        // Validate cost
        if (!empty($data['cost']) && (!is_numeric($data['cost']) || $data['cost'] < 0)) {
            $errors['cost'] = 'Cost must be a positive number';
        }
        
        // Validate status
        if (!empty($data['status'])) {
            $allowedStatuses = [TREATMENT_ONGOING, TREATMENT_COMPLETED, TREATMENT_FOLLOW_UP];
            if (!in_array($data['status'], $allowedStatuses)) {
                $errors['status'] = 'Invalid status selected';
            }
        }
        
        return $errors;
    }
    
    // Search treatments
    public function searchTreatments($term) {
        $sql = "SELECT t.*, 
                       a.name as animal_name, a.species,
                       c.name as client_name,
                       u.name as veterinary_name
                FROM {$this->table} t
                JOIN animals a ON t.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                JOIN users u ON t.veterinary_id = u.user_id
                WHERE t.diagnosis LIKE :term
                OR t.treatment_details LIKE :term
                OR t.medication_prescribed LIKE :term
                OR a.name LIKE :term
                OR c.name LIKE :term
                ORDER BY t.treatment_date DESC";
        
        return fetchAll($sql, ['term' => "%{$term}%"]);
    }
    
    // Get treatment statistics
    public function getStats() {
        $stats = [
            'total' => $this->count(),
            'ongoing' => $this->count(['status' => TREATMENT_ONGOING]),
            'completed' => $this->count(['status' => STATUS_COMPLETED]),
            'follow_up' => $this->count(['status' => TREATMENT_FOLLOW_UP])
        ];
        
        // Get treatments this month
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE treatment_date BETWEEN :start AND :end";
        $thisMonth = fetchOne($sql, ['start' => $startOfMonth, 'end' => $endOfMonth]);
        $stats['this_month'] = $thisMonth['count'] ?? 0;
        
        // Get most common diagnoses
        $sql = "SELECT diagnosis, COUNT(*) as count FROM {$this->table} 
                GROUP BY diagnosis ORDER BY count DESC LIMIT 5";
        $stats['common_diagnoses'] = fetchAll($sql);
        
        return $stats;
    }
    
    // Load treatment data into object properties
    public function load($treatmentData) {
        if (is_array($treatmentData)) {
            $this->setTreatmentId($treatmentData['treatment_id'] ?? null);
            $this->setAnimalId($treatmentData['animal_id'] ?? null);
            $this->setVeterinaryId($treatmentData['veterinary_id'] ?? null);
            $this->setDiagnosis($treatmentData['diagnosis'] ?? '');
            $this->setTreatmentDetails($treatmentData['treatment_details'] ?? '');
            $this->setMedicationPrescribed($treatmentData['medication_prescribed'] ?? '');
            $this->setTreatmentDate($treatmentData['treatment_date'] ?? null);
            $this->setFollowUpDate($treatmentData['follow_up_date'] ?? null);
            $this->setStatus($treatmentData['status'] ?? TREATMENT_ONGOING);
            $this->setNotes($treatmentData['notes'] ?? '');
            $this->setCost($treatmentData['cost'] ?? 0);
            $this->setCreatedAt($treatmentData['created_at'] ?? null);
            $this->setUpdatedAt($treatmentData['updated_at'] ?? null);
        }
        return $this;
    }
    
    // Convert object to array
    public function toArray() {
        return [
            'treatment_id' => $this->treatmentId,
            'animal_id' => $this->animalId,
            'veterinary_id' => $this->veterinaryId,
            'diagnosis' => $this->diagnosis,
            'treatment_details' => $this->treatmentDetails,
            'medication_prescribed' => $this->medicationPrescribed,
            'treatment_date' => $this->treatmentDate,
            'follow_up_date' => $this->followUpDate,
            'status' => $this->status,
            'notes' => $this->notes,
            'cost' => $this->cost,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
    
    public function isCompleted() {
        return $this->status === STATUS_COMPLETED;
    }
    
    public function needsFollowUp() {
        return $this->followUpDate && strtotime($this->followUpDate) <= time() && !$this->isCompleted();
    }
    
    public function getStatusBadge() {
        switch ($this->status) {
            case TREATMENT_ONGOING:
                return '<span class="badge bg-warning">Ongoing</span>';
            case STATUS_COMPLETED:
                return '<span class="badge bg-success">Completed</span>';
            case TREATMENT_FOLLOW_UP:
                return '<span class="badge bg-info">Follow-up Required</span>';
            default:
                return '<span class="badge bg-secondary">Unknown</span>';
        }
    }
}
?>