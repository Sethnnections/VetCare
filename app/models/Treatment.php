<?php
class Treatment extends Model {
    protected $table = 'treatments';
    protected $primaryKey = 'treatment_id';
    protected $fillable = [
        'animal_id', 'veterinary_id', 'diagnosis', 'treatment_details', 
        'medication_prescribed', 'treatment_date', 'follow_up_date', 
        'status', 'notes', 'cost'
    ];
    
    // Get all treatments with details (for admin)
    public function getAllTreatmentsWithDetails($page = 1, $perPage = 15, $search = null, $status = null) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM treatment_details_view WHERE 1=1";
        $params = [];
        
        if ($search) {
            $sql .= " AND (diagnosis LIKE ? OR animal_name LIKE ? OR client_full_name LIKE ?)";
            $searchTerm = "%{$search}%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        }
        
        if ($status) {
            $sql .= " AND treatment_status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY treatment_date DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $data = fetchAll($sql, $params);
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM treatment_details_view WHERE 1=1";
        $countParams = [];
        
        if ($search) {
            $countSql .= " AND (diagnosis LIKE ? OR animal_name LIKE ? OR client_full_name LIKE ?)";
            $searchTerm = "%{$search}%";
            $countParams = array_merge($countParams, [$searchTerm, $searchTerm, $searchTerm]);
        }
        
        if ($status) {
            $countSql .= " AND treatment_status = ?";
            $countParams[] = $status;
        }
        
        $totalResult = fetchOne($countSql, $countParams);
        $total = $totalResult['total'] ?? 0;
        
        return [
            'data' => $data,
            'total' => $total,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    // Get treatments by veterinary
    public function getTreatmentsByVeterinary($veterinaryId, $page = 1, $perPage = 15, $search = null, $status = null) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM treatment_details_view WHERE veterinary_id = ?";
        $params = [$veterinaryId];
        
        if ($search) {
            $sql .= " AND (diagnosis LIKE ? OR animal_name LIKE ? OR client_full_name LIKE ?)";
            $searchTerm = "%{$search}%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        }
        
        if ($status) {
            $sql .= " AND treatment_status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY treatment_date DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $data = fetchAll($sql, $params);
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM treatment_details_view WHERE veterinary_id = ?";
        $countParams = [$veterinaryId];
        
        if ($search) {
            $countSql .= " AND (diagnosis LIKE ? OR animal_name LIKE ? OR client_full_name LIKE ?)";
            $searchTerm = "%{$search}%";
            $countParams = array_merge($countParams, [$searchTerm, $searchTerm, $searchTerm]);
        }
        
        if ($status) {
            $countSql .= " AND treatment_status = ?";
            $countParams[] = $status;
        }
        
        $totalResult = fetchOne($countSql, $countParams);
        $total = $totalResult['total'] ?? 0;
        
        return [
            'data' => $data,
            'total' => $total,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    // Get treatments by client
    public function getTreatmentsByClient($clientId, $page = 1, $perPage = 15, $search = null, $status = null) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT tdv.* FROM treatment_details_view tdv 
                JOIN animals a ON tdv.animal_id = a.animal_id 
                WHERE a.client_id = ?";
        $params = [$clientId];
        
        if ($search) {
            $sql .= " AND (tdv.diagnosis LIKE ? OR tdv.animal_name LIKE ?)";
            $searchTerm = "%{$search}%";
            $params = array_merge($params, [$searchTerm, $searchTerm]);
        }
        
        if ($status) {
            $sql .= " AND tdv.treatment_status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY tdv.treatment_date DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $data = fetchAll($sql, $params);
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM treatment_details_view tdv 
                     JOIN animals a ON tdv.animal_id = a.animal_id 
                     WHERE a.client_id = ?";
        $countParams = [$clientId];
        
        if ($search) {
            $countSql .= " AND (tdv.diagnosis LIKE ? OR tdv.animal_name LIKE ?)";
            $searchTerm = "%{$search}%";
            $countParams = array_merge($countParams, [$searchTerm, $searchTerm]);
        }
        
        if ($status) {
            $countSql .= " AND tdv.treatment_status = ?";
            $countParams[] = $status;
        }
        
        $totalResult = fetchOne($countSql, $countParams);
        $total = $totalResult['total'] ?? 0;
        
        return [
            'data' => $data,
            'total' => $total,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    // Get treatment with full details
    public function getTreatmentWithDetails($treatmentId) {
        $sql = "SELECT * FROM treatment_details_view WHERE treatment_id = ?";
        return fetchOne($sql, [$treatmentId]);
    }
    
    // Create new treatment
    public function createTreatment($data) {
        return $this->create($data);
    }
    
 
    // Mark treatment as completed
    public function markAsCompleted($id) {
        return $this->update($id, ['status' => 'completed', 'follow_up_date' => null]);
    }
    
    // Schedule follow-up
    public function scheduleFollowUp($id, $followUpDate, $notes = null) {
        $data = [
            'status' => 'follow_up',
            'follow_up_date' => $followUpDate
        ];
        
        if ($notes) {
            $data['notes'] = $notes;
        }
        
        return $this->update($id, $data);
    }
    
    // Get upcoming follow-ups
    public function getUpcomingFollowUps($veterinaryId = null) {
        $sql = "SELECT * FROM treatment_progress_view 
                WHERE follow_up_date >= CURDATE() 
                AND treatment_status != 'completed'";
        
        $params = [];
        
        if ($veterinaryId) {
            $sql .= " AND veterinary_id = ?";
            $params[] = $veterinaryId;
        }
        
        $sql .= " ORDER BY follow_up_date ASC";
        
        return fetchAll($sql, $params);
    }
    
    // Get overdue follow-ups
    public function getOverdueFollowUps($veterinaryId = null) {
        $sql = "SELECT * FROM treatment_progress_view 
                WHERE follow_up_date < CURDATE() 
                AND treatment_status != 'completed'";
        
        $params = [];
        
        if ($veterinaryId) {
            $sql .= " AND veterinary_id = ?";
            $params[] = $veterinaryId;
        }
        
        $sql .= " ORDER BY follow_up_date ASC";
        
        return fetchAll($sql, $params);
    }

    // Get animals for treatment creation based on role
    public function getAnimalsForTreatment($userRole, $userId) {
        if ($userRole === ROLE_VETERINARY) {
            $sql = "SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) as client_name 
                    FROM animals a 
                    JOIN clients c ON a.client_id = c.client_id 
                    JOIN users u ON c.user_id = u.user_id 
                    WHERE a.assigned_veterinary = ? AND a.status = 'active' 
                    ORDER BY a.name";
            return fetchAll($sql, [$userId]);
        } else {
            $sql = "SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) as client_name 
                    FROM animals a 
                    JOIN clients c ON a.client_id = c.client_id 
                    JOIN users u ON c.user_id = u.user_id 
                    WHERE a.status = 'active' 
                    ORDER BY a.name";
            return fetchAll($sql);
        }
    }

    // Get treatment by ID with full details
    public function getTreatmentById($treatmentId) {
        $sql = "SELECT * FROM treatment_details_view WHERE treatment_id = ?";
        return fetchOne($sql, [$treatmentId]);
    }

    // Update treatment
    public function updateTreatment($id, $data) {
        return $this->update($id, $data);
    }

    // Get follow-ups for veterinary
    public function getVeterinaryFollowUps($veterinaryId = null) {
        $sql = "SELECT * FROM treatment_progress_view 
                WHERE follow_up_date IS NOT NULL 
                AND treatment_status != 'completed'";
        
        $params = [];
        
        if ($veterinaryId) {
            $sql .= " AND veterinary_id = ?";
            $params[] = $veterinaryId;
        }
        
        $sql .= " ORDER BY follow_up_date ASC";
        
        return fetchAll($sql, $params);
    }
    
    // Get treatment statistics
    public function getTreatmentStats($userRole, $userId = null) {
        $stats = [];
        
        switch ($userRole) {
            case ROLE_ADMIN:
                // Admin sees all stats
                $sql = "SELECT 
                        COUNT(*) as total_treatments,
                        SUM(CASE WHEN status = 'ongoing' THEN 1 ELSE 0 END) as ongoing_treatments,
                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_treatments,
                        SUM(CASE WHEN status = 'follow_up' THEN 1 ELSE 0 END) as follow_up_treatments,
                        AVG(cost) as avg_cost,
                        SUM(cost) as total_revenue
                    FROM treatments";
                $stats = fetchOne($sql);
                break;
                
            case ROLE_VETERINARY:
                // Veterinary sees their own stats
                $sql = "SELECT 
                        COUNT(*) as total_treatments,
                        SUM(CASE WHEN status = 'ongoing' THEN 1 ELSE 0 END) as ongoing_treatments,
                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_treatments,
                        SUM(CASE WHEN status = 'follow_up' THEN 1 ELSE 0 END) as follow_up_treatments,
                        AVG(cost) as avg_cost,
                        SUM(cost) as total_revenue
                    FROM treatments 
                    WHERE veterinary_id = ?";
                $stats = fetchOne($sql, [$userId]);
                break;
                
            case ROLE_CLIENT:
                // Client sees their animal stats
                $clientId = (new Client())->getClientIdByUserId($userId);
                if ($clientId) {
                    $sql = "SELECT 
                            COUNT(*) as total_treatments,
                            SUM(CASE WHEN t.status = 'ongoing' THEN 1 ELSE 0 END) as ongoing_treatments,
                            SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) as completed_treatments,
                            SUM(CASE WHEN t.status = 'follow_up' THEN 1 ELSE 0 END) as follow_up_treatments,
                            AVG(t.cost) as avg_cost,
                            SUM(t.cost) as total_cost
                        FROM treatments t
                        JOIN animals a ON t.animal_id = a.animal_id
                        WHERE a.client_id = ?";
                    $stats = fetchOne($sql, [$clientId]);
                }
                break;
        }
        
        return $stats ?: [
            'total_treatments' => 0,
            'ongoing_treatments' => 0,
            'completed_treatments' => 0,
            'follow_up_treatments' => 0,
            'avg_cost' => 0,
            'total_revenue' => 0,
            'total_cost' => 0
        ];
    }
    
    // Search treatments
    public function searchTreatments($term) {
        $sql = "SELECT * FROM treatment_details_view 
                WHERE diagnosis LIKE ? 
                OR animal_name LIKE ? 
                OR client_full_name LIKE ?
                OR vet_full_name LIKE ?
                ORDER BY treatment_date DESC 
                LIMIT 50";
        
        $searchTerm = "%{$term}%";
        return fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }
    
    // Validation
    public function validate($data, $id = null) {
        $errors = [];
        
        // Required fields
        $required = ['animal_id', 'veterinary_id', 'diagnosis', 'treatment_details', 'treatment_date'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[$field] = 'This field is required';
            }
        }
        
        // Validate animal exists
        if (!empty($data['animal_id'])) {
            $animalModel = new Animal();
            if (!$animalModel->exists($data['animal_id'])) {
                $errors['animal_id'] = 'Invalid animal selected';
            }
        }
        
        // Validate veterinary exists and is veterinary role
        if (!empty($data['veterinary_id'])) {
            $userModel = new User();
            $veterinary = $userModel->find($data['veterinary_id']);
            if (!$veterinary || $veterinary['role'] != ROLE_VETERINARY) {
                $errors['veterinary_id'] = 'Invalid veterinary selected';
            }
        }
        
        // Validate dates
        if (!empty($data['treatment_date']) && !strtotime($data['treatment_date'])) {
            $errors['treatment_date'] = 'Invalid treatment date';
        }
        
        if (!empty($data['follow_up_date']) && !strtotime($data['follow_up_date'])) {
            $errors['follow_up_date'] = 'Invalid follow-up date';
        }
        
        // Validate cost
        if (!empty($data['cost']) && (!is_numeric($data['cost']) || $data['cost'] < 0)) {
            $errors['cost'] = 'Cost must be a positive number';
        }
        
        return $errors;
    }

    
}
?>