<?php
// app/models/Billing.php

class Billing extends Model {
    protected $table = 'billings';
    protected $primaryKey = 'billing_id';
    protected $fillable = [
        'animal_id', 'treatment_id', 'billing_date', 'due_date', 
        'amount', 'tax_amount', 'discount', 'total_amount', 
        'payment_status', 'payment_method', 'payment_date', 
        'notes', 'items'
    ];
    
    // Billing properties
    private $billingId;
    private $animalId;
    private $treatmentId;
    private $billingDate;
    private $dueDate;
    private $amount;
    private $taxAmount;
    private $discount;
    private $totalAmount;
    private $paymentStatus;
    private $paymentMethod;
    private $paymentDate;
    private $notes;
    private $items;
    private $createdAt;
    private $updatedAt;
    
    // Getters
    public function getBillingId() { return $this->billingId; }
    public function getAnimalId() { return $this->animalId; }
    public function getTreatmentId() { return $this->treatmentId; }
    public function getBillingDate() { return $this->billingDate; }
    public function getDueDate() { return $this->dueDate; }
    public function getAmount() { return $this->amount; }
    public function getTaxAmount() { return $this->taxAmount; }
    public function getDiscount() { return $this->discount; }
    public function getTotalAmount() { return $this->totalAmount; }
    public function getPaymentStatus() { return $this->paymentStatus; }
    public function getPaymentMethod() { return $this->paymentMethod; }
    public function getPaymentDate() { return $this->paymentDate; }
    public function getNotes() { return $this->notes; }
    public function getItems() { return $this->items; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
    
    // Setters
    public function setBillingId($billingId) { $this->billingId = $billingId; }
    public function setAnimalId($animalId) { $this->animalId = (int)$animalId; }
    public function setTreatmentId($treatmentId) { $this->treatmentId = (int)$treatmentId; }
    public function setBillingDate($billingDate) { $this->billingDate = $billingDate; }
    public function setDueDate($dueDate) { $this->dueDate = $dueDate; }
    public function setAmount($amount) { $this->amount = (float)$amount; }
    public function setTaxAmount($taxAmount) { $this->taxAmount = (float)$taxAmount; }
    public function setDiscount($discount) { $this->discount = (float)$discount; }
    public function setTotalAmount($totalAmount) { $this->totalAmount = (float)$totalAmount; }
    public function setPaymentStatus($paymentStatus) { $this->paymentStatus = sanitize($paymentStatus); }
    public function setPaymentMethod($paymentMethod) { $this->paymentMethod = sanitize($paymentMethod); }
    public function setPaymentDate($paymentDate) { $this->paymentDate = $paymentDate; }
    public function setNotes($notes) { $this->notes = sanitize($notes); }
    public function setItems($items) { $this->items = $items; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
    public function setUpdatedAt($updatedAt) { $this->updatedAt = $updatedAt; }
    
    // Business logic methods
    public function getBillingWithDetails($billingId) {
        $sql = "SELECT b.*, 
                       a.name as animal_name, a.species, a.breed,
                       c.name as client_name, c.phone as client_phone, c.email as client_email,
                       t.diagnosis, t.treatment_details
                FROM {$this->table} b
                JOIN animals a ON b.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                LEFT JOIN treatments t ON b.treatment_id = t.treatment_id
                WHERE b.billing_id = :billing_id";
        return fetchOne($sql, ['billing_id' => $billingId]);
    }
    
    public function getBillingsByAnimal($animalId) {
        $sql = "SELECT b.*, t.diagnosis
                FROM {$this->table} b
                LEFT JOIN treatments t ON b.treatment_id = t.treatment_id
                WHERE b.animal_id = :animal_id
                ORDER BY b.billing_date DESC";
        return fetchAll($sql, ['animal_id' => $animalId]);
    }
    
    public function getBillingsByClient($clientId) {
        $sql = "SELECT b.*, 
                       a.name as animal_name, a.species,
                       t.diagnosis
                FROM {$this->table} b
                JOIN animals a ON b.animal_id = a.animal_id
                LEFT JOIN treatments t ON b.treatment_id = t.treatment_id
                WHERE a.client_id = :client_id
                ORDER BY b.billing_date DESC";
        return fetchAll($sql, ['client_id' => $clientId]);
    }
    
    public function getPendingBillings() {
        $sql = "SELECT b.*, 
                       a.name as animal_name, a.species,
                       c.name as client_name, c.phone as client_phone
                FROM {$this->table} b
                JOIN animals a ON b.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                WHERE b.payment_status = 'pending'
                ORDER BY b.due_date ASC";
        return fetchAll($sql);
    }
    
    public function getOverdueBillings() {
        $sql = "SELECT b.*, 
                       a.name as animal_name, a.species,
                       c.name as client_name, c.phone as client_phone
                FROM {$this->table} b
                JOIN animals a ON b.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                WHERE b.payment_status = 'pending'
                AND b.due_date < CURDATE()
                ORDER BY b.due_date ASC";
        return fetchAll($sql);
    }
    
    public function markAsPaid($billingId, $paymentMethod = null, $paymentDate = null) {
        $updateData = [
            'payment_status' => 'paid',
            'payment_date' => $paymentDate ?? getCurrentDate()
        ];
        
        if ($paymentMethod) {
            $updateData['payment_method'] = $paymentMethod;
        }
        
        return $this->update($billingId, $updateData);
    }
    
    public function createBilling($billingData) {
        // Set default values
        $billingData['billing_date'] = $billingData['billing_date'] ?? getCurrentDate();
        $billingData['payment_status'] = $billingData['payment_status'] ?? 'pending';
        
        // Calculate total amount if not provided
        if (!isset($billingData['total_amount'])) {
            $amount = $billingData['amount'] ?? 0;
            $taxAmount = $billingData['tax_amount'] ?? 0;
            $discount = $billingData['discount'] ?? 0;
            $billingData['total_amount'] = $amount + $taxAmount - $discount;
        }
        
        return $this->create($billingData);
    }
    
    public function getRevenueByPeriod($startDate, $endDate) {
        $sql = "SELECT 
                    SUM(total_amount) as total_revenue,
                    COUNT(*) as billing_count,
                    SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END) as paid_revenue,
                    SUM(CASE WHEN payment_status = 'pending' THEN total_amount ELSE 0 END) as pending_revenue
                FROM {$this->table}
                WHERE billing_date BETWEEN :start_date AND :end_date";
        
        return fetchOne($sql, ['start_date' => $startDate, 'end_date' => $endDate]);
    }
    
    public function getClientOutstandingBalance($clientId) {
        $sql = "SELECT SUM(b.total_amount) as outstanding_balance
                FROM {$this->table} b
                JOIN animals a ON b.animal_id = a.animal_id
                WHERE a.client_id = :client_id 
                AND b.payment_status = 'pending'";
        
        $result = fetchOne($sql, ['client_id' => $clientId]);
        return $result['outstanding_balance'] ?? 0;
    }
    
    // Validation
    public function validate($data, $id = null) {
        $errors = [];
        
        // Required fields
        $required = ['animal_id', 'billing_date', 'amount'];
        $errors = array_merge($errors, validateRequired($required, $data));
        
        // Validate animal exists
        if (!empty($data['animal_id'])) {
            $animalModel = new Animal();
            if (!$animalModel->exists($data['animal_id'])) {
                $errors['animal_id'] = 'Invalid animal selected';
            }
        }
        
        // Validate treatment exists if provided
        if (!empty($data['treatment_id'])) {
            $treatmentModel = new Treatment();
            if (!$treatmentModel->exists($data['treatment_id'])) {
                $errors['treatment_id'] = 'Invalid treatment selected';
            }
        }
        
        // Validate billing date
        if (!empty($data['billing_date'])) {
            $billingDate = strtotime($data['billing_date']);
            if (!$billingDate) {
                $errors['billing_date'] = 'Invalid billing date';
            }
        }
        
        // Validate due date
        if (!empty($data['due_date'])) {
            $dueDate = strtotime($data['due_date']);
            if (!$dueDate) {
                $errors['due_date'] = 'Invalid due date';
            }
        }
        
        // Validate amounts
        if (!empty($data['amount']) && (!is_numeric($data['amount']) || $data['amount'] < 0)) {
            $errors['amount'] = 'Amount must be a positive number';
        }
        
        if (!empty($data['tax_amount']) && (!is_numeric($data['tax_amount']) || $data['tax_amount'] < 0)) {
            $errors['tax_amount'] = 'Tax amount must be a positive number';
        }
        
        if (!empty($data['discount']) && (!is_numeric($data['discount']) || $data['discount'] < 0)) {
            $errors['discount'] = 'Discount must be a positive number';
        }
        
        // Validate payment status
        if (!empty($data['payment_status'])) {
            $allowedStatuses = ['pending', 'paid', 'cancelled'];
            if (!in_array($data['payment_status'], $allowedStatuses)) {
                $errors['payment_status'] = 'Invalid payment status';
            }
        }
        
        return $errors;
    }
    
    // Search billings
    public function searchBillings($term) {
        $sql = "SELECT b.*, 
                       a.name as animal_name, a.species,
                       c.name as client_name
                FROM {$this->table} b
                JOIN animals a ON b.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                WHERE b.notes LIKE :term
                OR a.name LIKE :term
                OR c.name LIKE :term
                ORDER BY b.billing_date DESC";
        
        return fetchAll($sql, ['term' => "%{$term}%"]);
    }
    
    // Get billing statistics
    public function getStats() {
        $stats = [
            'total' => $this->count(),
            'pending' => $this->count(['payment_status' => 'pending']),
            'paid' => $this->count(['payment_status' => 'paid']),
            'cancelled' => $this->count(['payment_status' => 'cancelled'])
        ];
        
        // Get total revenue
        $sql = "SELECT SUM(total_amount) as total_revenue FROM {$this->table} WHERE payment_status = 'paid'";
        $revenue = fetchOne($sql);
        $stats['total_revenue'] = $revenue['total_revenue'] ?? 0;
        
        // Get pending revenue
        $sql = "SELECT SUM(total_amount) as pending_revenue FROM {$this->table} WHERE payment_status = 'pending'";
        $pending = fetchOne($sql);
        $stats['pending_revenue'] = $pending['pending_revenue'] ?? 0;
        
        // Get revenue this month
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');
        $sql = "SELECT SUM(total_amount) as monthly_revenue FROM {$this->table} 
                WHERE payment_status = 'paid' AND billing_date BETWEEN :start AND :end";
        $monthly = fetchOne($sql, ['start' => $startOfMonth, 'end' => $endOfMonth]);
        $stats['monthly_revenue'] = $monthly['monthly_revenue'] ?? 0;
        
        return $stats;
    }
    
    // Load billing data into object properties
    public function load($billingData) {
        if (is_array($billingData)) {
            $this->setBillingId($billingData['billing_id'] ?? null);
            $this->setAnimalId($billingData['animal_id'] ?? null);
            $this->setTreatmentId($billingData['treatment_id'] ?? null);
            $this->setBillingDate($billingData['billing_date'] ?? null);
            $this->setDueDate($billingData['due_date'] ?? null);
            $this->setAmount($billingData['amount'] ?? 0);
            $this->setTaxAmount($billingData['tax_amount'] ?? 0);
            $this->setDiscount($billingData['discount'] ?? 0);
            $this->setTotalAmount($billingData['total_amount'] ?? 0);
            $this->setPaymentStatus($billingData['payment_status'] ?? 'pending');
            $this->setPaymentMethod($billingData['payment_method'] ?? '');
            $this->setPaymentDate($billingData['payment_date'] ?? null);
            $this->setNotes($billingData['notes'] ?? '');
            $this->setItems($billingData['items'] ?? '');
            $this->setCreatedAt($billingData['created_at'] ?? null);
            $this->setUpdatedAt($billingData['updated_at'] ?? null);
        }
        return $this;
    }
    
    // Convert object to array
    public function toArray() {
        return [
            'billing_id' => $this->billingId,
            'animal_id' => $this->animalId,
            'treatment_id' => $this->treatmentId,
            'billing_date' => $this->billingDate,
            'due_date' => $this->dueDate,
            'amount' => $this->amount,
            'tax_amount' => $this->taxAmount,
            'discount' => $this->discount,
            'total_amount' => $this->totalAmount,
            'payment_status' => $this->paymentStatus,
            'payment_method' => $this->paymentMethod,
            'payment_date' => $this->paymentDate,
            'notes' => $this->notes,
            'items' => $this->items,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
    
    public function isPaid() {
        return $this->paymentStatus === 'paid';
    }
    
    public function isOverdue() {
        return !$this->isPaid() && $this->dueDate && strtotime($this->dueDate) < time();
    }
    
    public function getStatusBadge() {
        switch ($this->paymentStatus) {
            case 'paid':
                return '<span class="badge bg-success">Paid</span>';
            case 'pending':
                if ($this->isOverdue()) {
                    return '<span class="badge bg-danger">Overdue</span>';
                }
                return '<span class="badge bg-warning">Pending</span>';
            case 'cancelled':
                return '<span class="badge bg-secondary">Cancelled</span>';
            default:
                return '<span class="badge bg-secondary">Unknown</span>';
        }
    }
    
    public function calculateTotal() {
        $this->totalAmount = $this->amount + $this->taxAmount - $this->discount;
        return $this->totalAmount;
    }
}
?>