<?php
class Client extends Model {
    protected $table = 'clients';
    protected $primaryKey = 'client_id';
    protected $fillable = [
        'user_id', 'emergency_contact', 'preferred_contact_method', 'notes'
    ];
    
    public function __construct() {
        parent::__construct();
    }
    
    // Business logic methods
    public function getClientByUserId($userId) {
        $sql = "SELECT c.*, u.first_name, u.last_name, u.email, u.phone, u.address, u.profile_picture
                FROM {$this->table} c 
                JOIN users u ON c.user_id = u.user_id 
                WHERE c.user_id = ?";
        return fetchOne($sql, [$userId]);
    }
    
    public function getClientAnimals($clientId) {
        $sql = "SELECT * FROM animals WHERE client_id = ? AND status = 'active' ORDER BY name";
        return fetchAll($sql, [$clientId]);
    }
    
    public function updateClientProfile($clientId, $profileData) {
        // Update client table
        $clientUpdate = [];
        if (isset($profileData['emergency_contact'])) {
            $clientUpdate['emergency_contact'] = $profileData['emergency_contact'];
        }
        if (isset($profileData['preferred_contact_method'])) {
            $clientUpdate['preferred_contact_method'] = $profileData['preferred_contact_method'];
        }
        if (isset($profileData['notes'])) {
            $clientUpdate['notes'] = $profileData['notes'];
        }
        
        if (!empty($clientUpdate)) {
            $this->update($clientId, $clientUpdate);
        }
        
        // Update user table
        $userUpdate = [];
        if (isset($profileData['first_name'])) {
            $userUpdate['first_name'] = $profileData['first_name'];
        }
        if (isset($profileData['last_name'])) {
            $userUpdate['last_name'] = $profileData['last_name'];
        }
        if (isset($profileData['phone'])) {
            $userUpdate['phone'] = $profileData['phone'];
        }
        if (isset($profileData['address'])) {
            $userUpdate['address'] = $profileData['address'];
        }
        
        if (!empty($userUpdate)) {
            $userModel = new User();
            $client = $this->find($clientId);
            if ($client && $client['user_id']) {
                $userModel->update($client['user_id'], $userUpdate);
            }
        }
        
        return true;
    }
    
    public function getClientIdByUserId($userId) {
        $sql = "SELECT client_id FROM {$this->table} WHERE user_id = ?";
        $result = fetchOne($sql, [$userId]);
        return $result ? $result['client_id'] : null;
    }
    
    // Additional methods that might be needed
    public function searchClients($term) {
        $sql = "SELECT c.*, u.first_name, u.last_name, u.email, u.phone 
                FROM {$this->table} c 
                JOIN users u ON c.user_id = u.user_id 
                WHERE u.first_name LIKE :term 
                OR u.last_name LIKE :term 
                OR u.email LIKE :term 
                OR u.phone LIKE :term 
                ORDER BY u.first_name, u.last_name";
        return fetchAll($sql, ['term' => "%{$term}%"]);
    }
    
    public function getStats() {
        $total = $this->count();
        
        // Get clients with active animals
        $sql = "SELECT COUNT(DISTINCT c.client_id) as with_animals 
                FROM clients c 
                JOIN animals a ON c.client_id = a.client_id 
                WHERE a.status = 'active'";
        $withAnimals = fetchOne($sql)['with_animals'] ?? 0;
        
        return [
            'total' => $total,
            'with_animals' => $withAnimals,
            'without_animals' => $total - $withAnimals
        ];
    }
    
    public function getClientTreatments($clientId, $limit = null) {
        $sql = "SELECT t.*, a.name as animal_name 
                FROM treatments t 
                JOIN animals a ON t.animal_id = a.animal_id 
                WHERE a.client_id = ? 
                ORDER BY t.treatment_date DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        return fetchAll($sql, [$clientId]);
    }
    
    public function getClientBillings($clientId) {
        $sql = "SELECT b.*, a.name as animal_name 
                FROM billings b 
                JOIN animals a ON b.animal_id = a.animal_id 
                WHERE a.client_id = ? 
                ORDER BY b.billing_date DESC";
        return fetchAll($sql, [$clientId]);
    }
    
    public function createClient($clientData) {
        return $this->create($clientData);
    }
    
    public function updateClient($clientId, $clientData) {
        return $this->update($clientId, $clientData);
    }
    
    public function deactivateClient($clientId) {
        // Instead of deleting, set the associated user to inactive
        $client = $this->find($clientId);
        if ($client && $client['user_id']) {
            $userModel = new User();
            return $userModel->update($client['user_id'], ['is_active' => 0]);
        }
        return false;
    }
    
    public function activateClient($clientId) {
        // Activate the associated user
        $client = $this->find($clientId);
        if ($client && $client['user_id']) {
            $userModel = new User();
            return $userModel->update($client['user_id'], ['is_active' => 1]);
        }
        return false;
    }
    
    public function validate($data, $id = null) {
        $errors = [];
        
        // Add validation logic here
        if (isset($data['emergency_contact']) && !empty($data['emergency_contact']) && !validatePhone($data['emergency_contact'])) {
            $errors['emergency_contact'] = 'Invalid emergency contact number';
        }
        
        if (isset($data['preferred_contact_method']) && !in_array($data['preferred_contact_method'], ['phone', 'email', 'sms'])) {
            $errors['preferred_contact_method'] = 'Invalid contact method';
        }
        
        return $errors;
    }
}
?>