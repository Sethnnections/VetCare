<?php
// app/models/Reminder.php

class Reminder extends Model {
    protected $table = 'reminders';
    protected $primaryKey = 'reminder_id';
    protected $fillable = [
        'animal_id', 'reminder_type', 'reminder_date', 'due_date',
        'title', 'description', 'status', 'priority', 'assigned_to',
        'notes', 'related_type', 'related_id'
    ];
    
    // Reminder properties
    private $reminderId;
    private $animalId;
    private $reminderType;
    private $reminderDate;
    private $dueDate;
    private $title;
    private $description;
    private $status;
    private $priority;
    private $assignedTo;
    private $notes;
    private $relatedType;
    private $relatedId;
    private $createdAt;
    private $updatedAt;
    
    // Getters
    public function getReminderId() { return $this->reminderId; }
    public function getAnimalId() { return $this->animalId; }
    public function getReminderType() { return $this->reminderType; }
    public function getReminderDate() { return $this->reminderDate; }
    public function getDueDate() { return $this->dueDate; }
    public function getTitle() { return $this->title; }
    public function getDescription() { return $this->description; }
    public function getStatus() { return $this->status; }
    public function getPriority() { return $this->priority; }
    public function getAssignedTo() { return $this->assignedTo; }
    public function getNotes() { return $this->notes; }
    public function getRelatedType() { return $this->relatedType; }
    public function getRelatedId() { return $this->relatedId; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
    
    // Setters
    public function setReminderId($reminderId) { $this->reminderId = $reminderId; }
    public function setAnimalId($animalId) { $this->animalId = (int)$animalId; }
    public function setReminderType($reminderType) { $this->reminderType = sanitize($reminderType); }
    public function setReminderDate($reminderDate) { $this->reminderDate = $reminderDate; }
    public function setDueDate($dueDate) { $this->dueDate = $dueDate; }
    public function setTitle($title) { $this->title = sanitize($title); }
    public function setDescription($description) { $this->description = sanitize($description); }
    public function setStatus($status) { $this->status = sanitize($status); }
    public function setPriority($priority) { $this->priority = sanitize($priority); }
    public function setAssignedTo($assignedTo) { $this->assignedTo = (int)$assignedTo; }
    public function setNotes($notes) { $this->notes = sanitize($notes); }
    public function setRelatedType($relatedType) { $this->relatedType = sanitize($relatedType); }
    public function setRelatedId($relatedId) { $this->relatedId = (int)$relatedId; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
    public function setUpdatedAt($updatedAt) { $this->updatedAt = $updatedAt; }
    
    // Business logic methods
    public function getReminderWithDetails($reminderId) {
        $sql = "SELECT r.*, 
                       a.name as animal_name, a.species, a.breed,
                       c.name as client_name, c.phone as client_phone,
                       u.name as assigned_name
                FROM {$this->table} r
                JOIN animals a ON r.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                LEFT JOIN users u ON r.assigned_to = u.user_id
                WHERE r.reminder_id = :reminder_id";
        return fetchOne($sql, ['reminder_id' => $reminderId]);
    }
    
    public function getRemindersByAnimal($animalId) {
        $sql = "SELECT r.*, u.name as assigned_name
                FROM {$this->table} r
                LEFT JOIN users u ON r.assigned_to = u.user_id
                WHERE r.animal_id = :animal_id
                ORDER BY r.due_date ASC, r.priority DESC";
        return fetchAll($sql, ['animal_id' => $animalId]);
    }
    
    public function getRemindersByClient($clientId) {
        $sql = "SELECT r.*, 
                       a.name as animal_name, a.species,
                       u.name as assigned_name
                FROM {$this->table} r
                JOIN animals a ON r.animal_id = a.animal_id
                LEFT JOIN users u ON r.assigned_to = u.user_id
                WHERE a.client_id = :client_id
                ORDER BY r.due_date ASC, r.priority DESC";
        return fetchAll($sql, ['client_id' => $clientId]);
    }
    
    public function getUpcomingReminders($days = 7) {
        $sql = "SELECT r.*, 
                       a.name as animal_name, a.species, a.breed,
                       c.name as client_name, c.phone as client_phone,
                       u.name as assigned_name
                FROM {$this->table} r
                JOIN animals a ON r.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                LEFT JOIN users u ON r.assigned_to = u.user_id
                WHERE r.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
                AND r.status = 'pending'
                ORDER BY r.due_date ASC, r.priority DESC";
        return fetchAll($sql, ['days' => $days]);
    }
    
    public function getOverdueReminders() {
        $sql = "SELECT r.*, 
                       a.name as animal_name, a.species, a.breed,
                       c.name as client_name, c.phone as client_phone,
                       u.name as assigned_name
                FROM {$this->table} r
                JOIN animals a ON r.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                LEFT JOIN users u ON r.assigned_to = u.user_id
                WHERE r.due_date < CURDATE()
                AND r.status = 'pending'
                ORDER BY r.due_date ASC, r.priority DESC";
        return fetchAll($sql);
    }
    
    public function getRemindersByUser($userId) {
        $sql = "SELECT r.*, 
                       a.name as animal_name, a.species,
                       c.name as client_name, c.phone as client_phone
                FROM {$this->table} r
                JOIN animals a ON r.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                WHERE r.assigned_to = :user_id
                AND r.status = 'pending'
                ORDER BY r.due_date ASC, r.priority DESC";
        return fetchAll($sql, ['user_id' => $userId]);
    }
    
    public function markAsCompleted($reminderId) {
        return $this->update($reminderId, ['status' => 'completed']);
    }
    
    public function markAsCancelled($reminderId) {
        return $this->update($reminderId, ['status' => 'cancelled']);
    }
    
    public function createReminder($reminderData) {
        // Set default values
        $reminderData['reminder_date'] = $reminderData['reminder_date'] ?? getCurrentDate();
        $reminderData['status'] = $reminderData['status'] ?? 'pending';
        $reminderData['priority'] = $reminderData['priority'] ?? 'medium';
        
        return $this->create($reminderData);
    }
    
    public function createVaccinationReminder($animalId, $vaccineData) {
        $reminderData = [
            'animal_id' => $animalId,
            'reminder_type' => 'vaccination',
            'title' => 'Vaccination Due: ' . $vaccineData['vaccine_name'],
            'description' => $vaccineData['vaccine_name'] . ' vaccination is due for ' . $vaccineData['animal_name'],
            'due_date' => $vaccineData['next_due_date'],
            'related_type' => 'vaccine',
            'related_id' => $vaccineData['vaccine_id'],
            'priority' => 'high'
        ];
        
        return $this->createReminder($reminderData);
    }
    
    public function createTreatmentFollowUpReminder($treatmentId, $followUpDate) {
        $treatmentModel = new Treatment();
        $treatment = $treatmentModel->getTreatmentWithDetails($treatmentId);
        
        if ($treatment) {
            $reminderData = [
                'animal_id' => $treatment['animal_id'],
                'reminder_type' => 'treatment_followup',
                'title' => 'Treatment Follow-up: ' . $treatment['diagnosis'],
                'description' => 'Follow-up required for treatment: ' . $treatment['diagnosis'],
                'due_date' => $followUpDate,
                'related_type' => 'treatment',
                'related_id' => $treatmentId,
                'priority' => 'medium'
            ];
            
            return $this->createReminder($reminderData);
        }
        
        return false;
    }
    
    // Validation
    public function validate($data, $id = null) {
        $errors = [];
        
        // Required fields
        $required = ['animal_id', 'reminder_type', 'title', 'due_date'];
        $errors = array_merge($errors, validateRequired($required, $data));
        
        // Validate animal exists
        if (!empty($data['animal_id'])) {
            $animalModel = new Animal();
            if (!$animalModel->exists($data['animal_id'])) {
                $errors['animal_id'] = 'Invalid animal selected';
            }
        }
        
        // Validate assigned user exists if provided
        if (!empty($data['assigned_to'])) {
            $userModel = new User();
            if (!$userModel->exists($data['assigned_to'])) {
                $errors['assigned_to'] = 'Invalid user selected';
            }
        }
        
        // Validate due date
        if (!empty($data['due_date'])) {
            $dueDate = strtotime($data['due_date']);
            if (!$dueDate) {
                $errors['due_date'] = 'Invalid due date';
            }
        }
        
        // Validate reminder type
        if (!empty($data['reminder_type'])) {
            $allowedTypes = ['vaccination', 'treatment_followup', 'appointment', 'billing', 'general'];
            if (!in_array($data['reminder_type'], $allowedTypes)) {
                $errors['reminder_type'] = 'Invalid reminder type';
            }
        }
        
        // Validate priority
        if (!empty($data['priority'])) {
            $allowedPriorities = ['low', 'medium', 'high', 'urgent'];
            if (!in_array($data['priority'], $allowedPriorities)) {
                $errors['priority'] = 'Invalid priority level';
            }
        }
        
        // Validate status
        if (!empty($data['status'])) {
            $allowedStatuses = ['pending', 'completed', 'cancelled'];
            if (!in_array($data['status'], $allowedStatuses)) {
                $errors['status'] = 'Invalid status';
            }
        }
        
        return $errors;
    }
    
    // Search reminders
    public function searchReminders($term) {
        $sql = "SELECT r.*, 
                       a.name as animal_name, a.species,
                       c.name as client_name,
                       u.name as assigned_name
                FROM {$this->table} r
                JOIN animals a ON r.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                LEFT JOIN users u ON r.assigned_to = u.user_id
                WHERE r.title LIKE :term
                OR r.description LIKE :term
                OR a.name LIKE :term
                OR c.name LIKE :term
                ORDER BY r.due_date ASC";
        
        return fetchAll($sql, ['term' => "%{$term}%"]);
    }
    
    // Get reminder statistics
    public function getStats() {
        $stats = [
            'total' => $this->count(),
            'pending' => $this->count(['status' => 'pending']),
            'completed' => $this->count(['status' => 'completed']),
            'cancelled' => $this->count(['status' => 'cancelled'])
        ];
        
        // Get overdue reminders
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE due_date < CURDATE() AND status = 'pending'";
        $overdue = fetchOne($sql);
        $stats['overdue'] = $overdue['count'] ?? 0;
        
        // Get reminders by type
        $sql = "SELECT reminder_type, COUNT(*) as count FROM {$this->table} 
                GROUP BY reminder_type ORDER BY count DESC";
        $stats['by_type'] = fetchAll($sql);
        
        // Get reminders by priority
        $sql = "SELECT priority, COUNT(*) as count FROM {$this->table} 
                WHERE status = 'pending' GROUP BY priority ORDER BY count DESC";
        $stats['by_priority'] = fetchAll($sql);
        
        return $stats;
    }
    
    // Load reminder data into object properties
    public function load($reminderData) {
        if (is_array($reminderData)) {
            $this->setReminderId($reminderData['reminder_id'] ?? null);
            $this->setAnimalId($reminderData['animal_id'] ?? null);
            $this->setReminderType($reminderData['reminder_type'] ?? '');
            $this->setReminderDate($reminderData['reminder_date'] ?? null);
            $this->setDueDate($reminderData['due_date'] ?? null);
            $this->setTitle($reminderData['title'] ?? '');
            $this->setDescription($reminderData['description'] ?? '');
            $this->setStatus($reminderData['status'] ?? 'pending');
            $this->setPriority($reminderData['priority'] ?? 'medium');
            $this->setAssignedTo($reminderData['assigned_to'] ?? null);
            $this->setNotes($reminderData['notes'] ?? '');
            $this->setRelatedType($reminderData['related_type'] ?? '');
            $this->setRelatedId($reminderData['related_id'] ?? null);
            $this->setCreatedAt($reminderData['created_at'] ?? null);
            $this->setUpdatedAt($reminderData['updated_at'] ?? null);
        }
        return $this;
    }
    
    // Convert object to array
    public function toArray() {
        return [
            'reminder_id' => $this->reminderId,
            'animal_id' => $this->animalId,
            'reminder_type' => $this->reminderType,
            'reminder_date' => $this->reminderDate,
            'due_date' => $this->dueDate,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'assigned_to' => $this->assignedTo,
            'notes' => $this->notes,
            'related_type' => $this->relatedType,
            'related_id' => $this->relatedId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
    
    public function isCompleted() {
        return $this->status === 'completed';
    }
    
    public function isOverdue() {
        return !$this->isCompleted() && $this->dueDate && strtotime($this->dueDate) < time();
    }
    
    public function getStatusBadge() {
        switch ($this->status) {
            case 'completed':
                return '<span class="badge bg-success">Completed</span>';
            case 'cancelled':
                return '<span class="badge bg-secondary">Cancelled</span>';
            case 'pending':
                if ($this->isOverdue()) {
                    return '<span class="badge bg-danger">Overdue</span>';
                }
                return '<span class="badge bg-warning">Pending</span>';
            default:
                return '<span class="badge bg-secondary">Unknown</span>';
        }
    }
    
    public function getPriorityBadge() {
        switch ($this->priority) {
            case 'urgent':
                return '<span class="badge bg-danger">Urgent</span>';
            case 'high':
                return '<span class="badge bg-warning">High</span>';
            case 'medium':
                return '<span class="badge bg-info">Medium</span>';
            case 'low':
                return '<span class="badge bg-secondary">Low</span>';
            default:
                return '<span class="badge bg-secondary">Unknown</span>';
        }
    }
}
?>