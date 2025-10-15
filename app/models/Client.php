<?php
class Client extends Model {
    protected $table = 'clients';
    protected $primaryKey = 'client_id';
    protected $fillable = [
        'user_id', 'emergency_contact', 'preferred_contact_method', 'notes'
    ];
    
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
}
?>