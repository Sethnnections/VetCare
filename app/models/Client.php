<?php
// app/models/Client.php

class Client extends Model {
    protected $table = 'clients';
    protected $primaryKey = 'client_id';
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'city', 'status', 'notes'
    ];
    
    // Client properties
    private $clientId;
    private $name;
    private $email;
    private $phone;
    private $address;
    private $city;
    private $status;
    private $notes;
    private $createdAt;
    private $updatedAt;
    
    // Getters
    public function getClientId() {
        return $this->clientId;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function getPhone() {
        return $this->phone;
    }
    
    public function getAddress() {
        return $this->address;
    }
    
    public function getCity() {
        return $this->city;
    }
    
    public function getStatus() {
        return $this->status;
    }
    
    public function getNotes() {
        return $this->notes;
    }
    
    public function getCreatedAt() {
        return $this->createdAt;
    }
    
    public function getUpdatedAt() {
        return $this->updatedAt;
    }
    
    // Setters
    public function setClientId($clientId) {
        $this->clientId = $clientId;
    }
    
    public function setName($name) {
        $this->name = sanitize($name);
    }
    
    public function setEmail($email) {
        $this->email = strtolower(sanitize($email));
    }
    
    public function setPhone($phone) {
        $this->phone = sanitize($phone);
    }
    
    public function setAddress($address) {
        $this->address = sanitize($address);
    }
    
    public function setCity($city) {
        $this->city = sanitize($city);
    }
    
    public function setStatus($status) {
        $this->status = (int)$status;
    }
    
    public function setNotes($notes) {
        $this->notes = sanitize($notes);
    }
    
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }
    
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
    }
    
    // Business logic methods
    public function createClient($clientData) {
        $clientData['status'] = $clientData['status'] ?? STATUS_ACTIVE;
        return $this->create($clientData);
    }
    
    public function updateClient($clientId, $clientData) {
        return $this->update($clientId, $clientData);
    }
    
    public function getActiveClients() {
        return $this->findAllBy('status', STATUS_ACTIVE);
    }
    
    public function findAllBy($column, $value) {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value ORDER BY name";
        return fetchAll($sql, ['value' => $value]);
    }
    
    public function deactivateClient($clientId) {
        return $this->update($clientId, ['status' => STATUS_INACTIVE]);
    }
    
    public function activateClient($clientId) {
        return $this->update($clientId, ['status' => STATUS_ACTIVE]);
    }
    
    public function getClientAnimals($clientId) {
        $sql = "SELECT * FROM animals WHERE client_id = :client_id ORDER BY name";
        return fetchAll($sql, ['client_id' => $clientId]);
    }
    
    public function getClientTreatments($clientId) {
        $sql = "SELECT t.*, a.name as animal_name, a.species 
                FROM treatments t 
                JOIN animals a ON t.animal_id = a.animal_id 
                WHERE a.client_id = :client_id 
                ORDER BY t.treatment_date DESC";
        return fetchAll($sql, ['client_id' => $clientId]);
    }
    
    public function getClientBillings($clientId) {
        $sql = "SELECT b.*, a.name as animal_name 
                FROM billings b 
                JOIN animals a ON b.animal_id = a.animal_id 
                WHERE a.client_id = :client_id 
                ORDER BY b.billing_date DESC";
        return fetchAll($sql, ['client_id' => $clientId]);
    }
    
    public function getTotalBilling($clientId) {
        $sql = "SELECT SUM(b.amount) as total 
                FROM billings b 
                JOIN animals a ON b.animal_id = a.animal_id 
                WHERE a.client_id = :client_id AND b.payment_status = 'paid'";
        $result = fetchOne($sql, ['client_id' => $clientId]);
        return $result['total'] ?? 0;
    }
    
    public function getPendingBilling($clientId) {
        $sql = "SELECT SUM(b.amount) as total 
                FROM billings b 
                JOIN animals a ON b.animal_id = a.animal_id 
                WHERE a.client_id = :client_id AND b.payment_status = 'pending'";
        $result = fetchOne($sql, ['client_id' => $clientId]);
        return $result['total'] ?? 0;
    }
    
    public function getClientReminders($clientId) {
        $sql = "SELECT r.*, a.name as animal_name 
                FROM reminders r 
                JOIN animals a ON r.animal_id = a.animal_id 
                WHERE a.client_id = :client_id AND r.status = 'pending'
                ORDER BY r.reminder_date ASC";
        return fetchAll($sql, ['client_id' => $clientId]);
    }
    
    // Validation
    public function validate($data, $id = null) {
        $errors = [];
        
        // Required fields
        $required = ['name', 'phone'];
        $errors = array_merge($errors, validateRequired($required, $data));
        
        // Email validation (optional)
        if (!empty($data['email']) && !validateEmail($data['email'])) {
            $errors['email'] = 'Invalid email format';
        }
        
        // Check unique email if provided
        if (!empty($data['email'])) {
            $existingClient = $this->findBy('email', $data['email']);
            if ($existingClient && (!$id || $existingClient['client_id'] != $id)) {
                $errors['email'] = 'Email already exists';
            }
        }
        
        // Phone validation
        if (!empty($data['phone'])) {
            if (!validatePhone($data['phone'])) {
                $errors['phone'] = 'Invalid phone number format';
            } else {
                // Check unique phone
                $existingClient = $this->findBy('phone', $data['phone']);
                if ($existingClient && (!$id || $existingClient['client_id'] != $id)) {
                    $errors['phone'] = 'Phone number already exists';
                }
            }
        }
        
        return $errors;
    }
    
    // Search clients
    public function searchClients($term) {
        return $this->search($term, ['name', 'email', 'phone', 'city']);
    }
    
    // Get client statistics
    public function getStats() {
        $stats = [
            'total' => $this->count(),
            'active' => $this->count(['status' => STATUS_ACTIVE]),
            'inactive' => $this->count(['status' => STATUS_INACTIVE])
        ];
        
        // Get clients by city
        $sql = "SELECT city, COUNT(*) as count FROM {$this->table} 
                WHERE status = :status GROUP BY city ORDER BY count DESC";
        $cityCounts = fetchAll($sql, ['status' => STATUS_ACTIVE]);
        $stats['by_city'] = $cityCounts;
        
        return $stats;
    }
    
    // Load client data into object properties
    public function load($clientData) {
        if (is_array($clientData)) {
            $this->setClientId($clientData['client_id'] ?? null);
            $this->setName($clientData['name'] ?? '');
            $this->setEmail($clientData['email'] ?? '');
            $this->setPhone($clientData['phone'] ?? '');
            $this->setAddress($clientData['address'] ?? '');
            $this->setCity($clientData['city'] ?? '');
            $this->setStatus($clientData['status'] ?? STATUS_ACTIVE);
            $this->setNotes($clientData['notes'] ?? '');
            $this->setCreatedAt($clientData['created_at'] ?? null);
            $this->setUpdatedAt($clientData['updated_at'] ?? null);
        }
        return $this;
    }
    
    // Convert object to array
    public function toArray() {
        return [
            'client_id' => $this->clientId,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
    
    // Get display name with contact info
    public function getDisplayName() {
        return $this->name . ' - ' . $this->phone;
    }
    
    // Check if client is active
    public function isActive() {
        return $this->status == STATUS_ACTIVE;
    }
    
    // Get client's contact information
    public function getContactInfo() {
        $contact = [];
        
        if ($this->phone) {
            $contact[] = 'Phone: ' . $this->phone;
        }
        
        if ($this->email) {
            $contact[] = 'Email: ' . $this->email;
        }
        
        if ($this->address) {
            $address = $this->address;
            if ($this->city) {
                $address .= ', ' . $this->city;
            }
            $contact[] = 'Address: ' . $address;
        }
        
        return implode(' | ', $contact);
    }
}
?>