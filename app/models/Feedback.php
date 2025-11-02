<?php
class Feedback extends Model {
    protected $table = 'feedbacks';
    protected $primaryKey = 'feedback_id';
    protected $fillable = [
        'client_id', 'veterinary_id', 'animal_id', 'treatment_id',
        'rating', 'comments', 'status', 'admin_notes', 'response'
    ];
    
    public function __construct() {
        parent::__construct();
    }
    
    // Business logic methods
    public function getFeedbackWithDetails($feedbackId) {
        $sql = "SELECT f.*, 
                       c.client_id, 
                       u1.first_name as client_first_name, 
                       u1.last_name as client_last_name,
                       CONCAT(u1.first_name, ' ', u1.last_name) as client_full_name,
                       u2.first_name as vet_first_name, 
                       u2.last_name as vet_last_name,
                       CONCAT(u2.first_name, ' ', u2.last_name) as vet_full_name,
                       a.name as animal_name, 
                       a.species,
                       t.diagnosis
                FROM {$this->table} f
                JOIN clients c ON f.client_id = c.client_id
                JOIN users u1 ON c.user_id = u1.user_id
                LEFT JOIN users u2 ON f.veterinary_id = u2.user_id
                LEFT JOIN animals a ON f.animal_id = a.animal_id
                LEFT JOIN treatments t ON f.treatment_id = t.treatment_id
                WHERE f.feedback_id = :feedback_id";
        return fetchOne($sql, ['feedback_id' => $feedbackId]);
    }
    
    public function getFeedbackByClient($clientId, $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT f.*, 
                       u.first_name as vet_first_name, 
                       u.last_name as vet_last_name,
                       CONCAT(u.first_name, ' ', u.last_name) as vet_full_name,
                       a.name as animal_name
                FROM {$this->table} f
                LEFT JOIN users u ON f.veterinary_id = u.user_id
                LEFT JOIN animals a ON f.animal_id = a.animal_id
                WHERE f.client_id = :client_id
                ORDER BY f.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        return fetchAll($sql, [
            'client_id' => $clientId,
            'limit' => $perPage,
            'offset' => $offset
        ]);
    }
    
    public function getFeedbackByVeterinary($veterinaryId, $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT f.*, 
                       c.client_id, 
                       u.first_name as client_first_name, 
                       u.last_name as client_last_name,
                       CONCAT(u.first_name, ' ', u.last_name) as client_full_name,
                       a.name as animal_name
                FROM {$this->table} f
                JOIN clients c ON f.client_id = c.client_id
                JOIN users u ON c.user_id = u.user_id
                LEFT JOIN animals a ON f.animal_id = a.animal_id
                WHERE f.veterinary_id = :veterinary_id
                ORDER BY f.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        return fetchAll($sql, [
            'veterinary_id' => $veterinaryId,
            'limit' => $perPage,
            'offset' => $offset
        ]);
    }
    
    public function getAllFeedback($filters = [], $page = 1, $perPage = 15) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT f.*, 
                       c.client_id, 
                       u1.first_name as client_first_name, 
                       u1.last_name as client_last_name,
                       CONCAT(u1.first_name, ' ', u1.last_name) as client_full_name,
                       u2.first_name as vet_first_name, 
                       u2.last_name as vet_last_name,
                       CONCAT(u2.first_name, ' ', u2.last_name) as vet_full_name,
                       a.name as animal_name
                FROM {$this->table} f
                JOIN clients c ON f.client_id = c.client_id
                JOIN users u1 ON c.user_id = u1.user_id
                LEFT JOIN users u2 ON f.veterinary_id = u2.user_id
                LEFT JOIN animals a ON f.animal_id = a.animal_id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND f.status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['rating'])) {
            $sql .= " AND f.rating = :rating";
            $params['rating'] = $filters['rating'];
        }
        
        if (!empty($filters['veterinary_id'])) {
            $sql .= " AND f.veterinary_id = :veterinary_id";
            $params['veterinary_id'] = $filters['veterinary_id'];
        }
        
        $sql .= " ORDER BY f.created_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $perPage;
        $params['offset'] = $offset;
        
        return fetchAll($sql, $params);
    }
    
    public function submitFeedback($feedbackData) {
        $feedbackData['status'] = 'submitted';
        return $this->create($feedbackData);
    }
    
    public function updateFeedbackStatus($feedbackId, $status, $adminNotes = null) {
        $updateData = ['status' => $status];
        
        if ($adminNotes) {
            $updateData['admin_notes'] = $adminNotes;
        }
        
        return $this->update($feedbackId, $updateData);
    }
    
    public function addResponse($feedbackId, $response, $respondedBy) {
        $updateData = [
            'response' => $response,
            'status' => 'responded'
        ];
        
        // If admin is responding, add to admin_notes
        if ($respondedBy == 'admin') {
            $updateData['admin_notes'] = $response;
        }
        
        return $this->update($feedbackId, $updateData);
    }
    
    public function getFeedbackStats($userRole = null, $userId = null) {
        $stats = [];
        
        switch ($userRole) {
            case ROLE_ADMIN:
                $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) as submitted,
                        SUM(CASE WHEN status = 'reviewed' THEN 1 ELSE 0 END) as reviewed,
                        SUM(CASE WHEN status = 'responded' THEN 1 ELSE 0 END) as responded,
                        AVG(rating) as avg_rating
                    FROM {$this->table}";
                $stats = fetchOne($sql);
                break;
                
            case ROLE_VETERINARY:
                $sql = "SELECT 
                        COUNT(*) as total,
                        AVG(rating) as avg_rating,
                        SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                        SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                        SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                        SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                        SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                    FROM {$this->table} 
                    WHERE veterinary_id = :veterinary_id";
                $stats = fetchOne($sql, ['veterinary_id' => $userId]);
                break;
                
            case ROLE_CLIENT:
                $clientId = (new Client())->getClientIdByUserId($userId);
                if ($clientId) {
                    $sql = "SELECT 
                            COUNT(*) as total,
                            AVG(rating) as avg_rating
                        FROM {$this->table} 
                        WHERE client_id = :client_id";
                    $stats = fetchOne($sql, ['client_id' => $clientId]);
                }
                break;
        }
        
        return $stats ?: [
            'total' => 0,
            'submitted' => 0,
            'reviewed' => 0,
            'responded' => 0,
            'avg_rating' => 0,
            'five_star' => 0,
            'four_star' => 0,
            'three_star' => 0,
            'two_star' => 0,
            'one_star' => 0
        ];
    }
    
    // Validation
    public function validate($data, $id = null) {
        $errors = [];
        
        // Required fields
        $required = ['client_id', 'rating'];
        $errors = array_merge($errors, validateRequired($required, $data));
        
        // Validate rating range
        if (!empty($data['rating']) && ($data['rating'] < 1 || $data['rating'] > 5)) {
            $errors['rating'] = 'Rating must be between 1 and 5';
        }
        
        // Validate client exists
        if (!empty($data['client_id'])) {
            $clientModel = new Client();
            if (!$clientModel->exists($data['client_id'])) {
                $errors['client_id'] = 'Invalid client';
            }
        }
        
        // Validate veterinary exists if provided
        if (!empty($data['veterinary_id'])) {
            $userModel = new User();
            $veterinary = $userModel->find($data['veterinary_id']);
            if (!$veterinary || $veterinary['role'] != ROLE_VETERINARY) {
                $errors['veterinary_id'] = 'Invalid veterinary';
            }
        }
        
        // Validate animal exists if provided
        if (!empty($data['animal_id'])) {
            $animalModel = new Animal();
            if (!$animalModel->exists($data['animal_id'])) {
                $errors['animal_id'] = 'Invalid animal';
            }
        }
        
        return $errors;
    }
    
    public function getStarRating($rating) {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $stars .= '<i class="fas fa-star text-warning"></i>';
            } else {
                $stars .= '<i class="far fa-star text-muted"></i>';
            }
        }
        return $stars;
    }
}
?>