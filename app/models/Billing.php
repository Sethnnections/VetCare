<?php
class Billing extends Model {
    protected $table = 'billings';
    protected $primaryKey = 'billing_id';
    protected $fillable = [
        'animal_id', 'treatment_id', 'billing_date', 'due_date', 'amount', 
        'tax_amount', 'discount', 'total_amount', 'payment_status', 
        'payment_method', 'payment_date', 'notes', 'items', 'deposit_slip',
        'verified_by', 'verified_at'
    ];
    
    public function getTable() {
        return $this->table;
    }
    // Get billing with full details
    public function getBillingWithDetails($billingId) {
        $sql = "SELECT b.*, 
                       a.name as animal_name, a.species, a.breed,
                       CONCAT(u.first_name, ' ', u.last_name) as client_name,
                       u.email as client_email, u.phone as client_phone,
                       t.diagnosis, t.treatment_date, t.treatment_details,
                       vet.first_name as vet_first_name, vet.last_name as vet_last_name,
                       CONCAT(vet.first_name, ' ', vet.last_name) as vet_name,
                       verified.first_name as verified_by_first_name,
                       verified.last_name as verified_by_last_name
                FROM {$this->table} b
                JOIN animals a ON b.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                JOIN users u ON c.user_id = u.user_id
                LEFT JOIN treatments t ON b.treatment_id = t.treatment_id
                LEFT JOIN users vet ON t.veterinary_id = vet.user_id
                LEFT JOIN users verified ON b.verified_by = verified.user_id
                WHERE b.billing_id = ?";
        return fetchOne($sql, [$billingId]);
    }
    
    // Get billing items
    public function getBillingItems($billingId) {
        $sql = "SELECT * FROM billing_items WHERE billing_id = ? ORDER BY item_id";
        return fetchAll($sql, [$billingId]);
    }
    
    // Get billings by client
            // Fix getBillingsByClient method
    public function getBillingsByClient($clientId, $status = null, $page = 1, $perPage = 15) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT b.*, a.name as animal_name, a.species,
                    t.diagnosis, t.treatment_date
                FROM {$this->table} b
                JOIN animals a ON b.animal_id = a.animal_id
                LEFT JOIN treatments t ON b.treatment_id = t.treatment_id
                WHERE a.client_id = ?";
        
        $params = [$clientId];
        
        if ($status) {
            $sql .= " AND b.payment_status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY b.billing_date DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $data = fetchAll($sql, $params);
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total 
                    FROM {$this->table} b
                    JOIN animals a ON b.animal_id = a.animal_id
                    WHERE a.client_id = ?";
        $countParams = [$clientId];
        
        if ($status) {
            $countSql .= " AND b.payment_status = ?";
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

    // Fix getOverdueBills method for clients
    public function getOverdueBills($userRole = null, $userId = null, $clientId = null) {
        $sql = "SELECT b.*, a.name as animal_name,
                    CONCAT(u.first_name, ' ', u.last_name) as client_name,
                    u.phone as client_phone
                FROM {$this->table} b
                JOIN animals a ON b.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                JOIN users u ON c.user_id = u.user_id
                WHERE b.payment_status = 'pending' 
                AND b.due_date < CURDATE()";
        
        $params = [];
        
        // Add client filter if client role
        if ($userRole === ROLE_CLIENT && $clientId) {
            $sql .= " AND a.client_id = ?";
            $params[] = $clientId;
        }
        
        // Add veterinary filter if veterinary role
        if ($userRole === ROLE_VETERINARY) {
            $sql .= " AND EXISTS (
                SELECT 1 FROM treatments t 
                WHERE t.treatment_id = b.treatment_id 
                AND t.veterinary_id = ?
            )";
            $params[] = $userId;
        }
        
        $sql .= " ORDER BY b.due_date ASC";
        
        return fetchAll($sql, $params);
    }

    // Fix getBillingStats method for clients
    public function getBillingStats($userRole, $userId, $clientId = null) {
        $stats = [];
        
        switch ($userRole) {
            case ROLE_ADMIN:
                $sql = "SELECT 
                        COUNT(*) as total_bills,
                        SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_bills,
                        SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending_bills,
                        SUM(CASE WHEN payment_status = 'verified' THEN 1 ELSE 0 END) as verified_bills,
                        COALESCE(SUM(total_amount), 0) as total_amount,
                        COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END), 0) as paid_amount,
                        COALESCE(SUM(CASE WHEN payment_status = 'verified' THEN total_amount ELSE 0 END), 0) as verified_amount,
                        COALESCE(SUM(CASE WHEN payment_status = 'pending' THEN total_amount ELSE 0 END), 0) as pending_amount
                    FROM {$this->table}";
                $stats = fetchOne($sql);
                break;
                
            case ROLE_VETERINARY:
                $sql = "SELECT 
                        COUNT(*) as total_bills,
                        SUM(CASE WHEN b.payment_status = 'paid' THEN 1 ELSE 0 END) as paid_bills,
                        SUM(CASE WHEN b.payment_status = 'pending' THEN 1 ELSE 0 END) as pending_bills,
                        SUM(CASE WHEN b.payment_status = 'verified' THEN 1 ELSE 0 END) as verified_bills,
                        COALESCE(SUM(b.total_amount), 0) as total_amount,
                        COALESCE(SUM(CASE WHEN b.payment_status = 'paid' THEN b.total_amount ELSE 0 END), 0) as paid_amount,
                        COALESCE(SUM(CASE WHEN b.payment_status = 'verified' THEN b.total_amount ELSE 0 END), 0) as verified_amount,
                        COALESCE(SUM(CASE WHEN b.payment_status = 'pending' THEN b.total_amount ELSE 0 END), 0) as pending_amount
                    FROM {$this->table} b
                    JOIN treatments t ON b.treatment_id = t.treatment_id
                    WHERE t.veterinary_id = ?";
                $stats = fetchOne($sql, [$userId]);
                break;
                
            case ROLE_CLIENT:
                if ($clientId) {
                    $sql = "SELECT 
                            COUNT(*) as total_bills,
                            SUM(CASE WHEN b.payment_status = 'paid' THEN 1 ELSE 0 END) as paid_bills,
                            SUM(CASE WHEN b.payment_status = 'pending' THEN 1 ELSE 0 END) as pending_bills,
                            SUM(CASE WHEN b.payment_status = 'verified' THEN 1 ELSE 0 END) as verified_bills,
                            COALESCE(SUM(b.total_amount), 0) as total_amount,
                            COALESCE(SUM(CASE WHEN b.payment_status = 'paid' THEN b.total_amount ELSE 0 END), 0) as paid_amount,
                            COALESCE(SUM(CASE WHEN b.payment_status = 'verified' THEN b.total_amount ELSE 0 END), 0) as verified_amount,
                            COALESCE(SUM(CASE WHEN b.payment_status = 'pending' THEN b.total_amount ELSE 0 END), 0) as pending_amount
                        FROM {$this->table} b
                        JOIN animals a ON b.animal_id = a.animal_id
                        WHERE a.client_id = ?";
                    $stats = fetchOne($sql, [$clientId]);
                }
                break;
        }
        
        return $stats ?: [
            'total_bills' => 0, 'paid_bills' => 0, 'pending_bills' => 0, 'verified_bills' => 0,
            'total_amount' => 0, 'paid_amount' => 0, 'verified_amount' => 0, 'pending_amount' => 0
        ];
    }
    
    // Get billings by veterinary
    public function getBillingsByVeterinary($veterinaryId, $status = null, $page = 1, $perPage = 15) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT b.*, a.name as animal_name, a.species,
                       CONCAT(u.first_name, ' ', u.last_name) as client_name,
                       t.diagnosis, t.treatment_date
                FROM {$this->table} b
                JOIN treatments t ON b.treatment_id = t.treatment_id
                JOIN animals a ON b.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                JOIN users u ON c.user_id = u.user_id
                WHERE t.veterinary_id = ?";
        
        $params = [$veterinaryId];
        
        if ($status) {
            $sql .= " AND b.payment_status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY b.billing_date DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $data = fetchAll($sql, $params);
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total 
                    FROM {$this->table} b
                    JOIN treatments t ON b.treatment_id = t.treatment_id
                    WHERE t.veterinary_id = ?";
        $countParams = [$veterinaryId];
        
        if ($status) {
            $countSql .= " AND b.payment_status = ?";
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
    
    // Get all billings for admin
    public function getAllBillings($status = null, $page = 1, $perPage = 15) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT b.*, a.name as animal_name, a.species,
                       CONCAT(u.first_name, ' ', u.last_name) as client_name,
                       t.diagnosis, t.treatment_date
                FROM {$this->table} b
                JOIN animals a ON b.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                JOIN users u ON c.user_id = u.user_id
                LEFT JOIN treatments t ON b.treatment_id = t.treatment_id
                WHERE 1=1";
        
        $params = [];
        
        if ($status) {
            $sql .= " AND b.payment_status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY b.billing_date DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $data = fetchAll($sql, $params);
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $countParams = [];
        
        if ($status) {
            $countSql .= " AND payment_status = ?";
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
    
    // Update payment status with deposit slip
    public function updatePayment($billingId, $paymentData) {
        $data = [
            'payment_status' => 'paid',
            'payment_method' => $paymentData['payment_method'],
            'payment_date' => date('Y-m-d'),
            'deposit_slip' => $paymentData['deposit_slip'] ?? null,
            'notes' => $paymentData['notes'] ?? null
        ];
        
        return $this->update($billingId, $data);
    }
    
    
    // Create billing with items
    public function createBillingWithItems($billingData, $items = []) {
        try {
            $this->db->beginTransaction();
            
            // Create billing
            $billingId = $this->create($billingData);
            
            // Create billing items
            if (!empty($items)) {
                foreach ($items as $item) {
                    $item['billing_id'] = $billingId;
                    $this->createBillingItem($item);
                }
            }
            
            $this->db->commit();
            return $billingId;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    // Create billing item
    public function createBillingItem($itemData) {
        $sql = "INSERT INTO billing_items (billing_id, description, quantity, unit_price, total_price) 
                VALUES (?, ?, ?, ?, ?)";
        return execute($sql, [
            $itemData['billing_id'],
            $itemData['description'],
            $itemData['quantity'] ?? 1,
            $itemData['unit_price'],
            $itemData['total_price']
        ]);
    }
    
  
 
    
    // Validation
    public function validate($data, $id = null) {
        $errors = [];
        
        $required = ['animal_id', 'billing_date', 'amount'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[$field] = 'This field is required';
            }
        }
        
        if (!empty($data['amount']) && (!is_numeric($data['amount']) || $data['amount'] <= 0)) {
            $errors['amount'] = 'Amount must be a positive number';
        }
        
        if (!empty($data['billing_date']) && !strtotime($data['billing_date'])) {
            $errors['billing_date'] = 'Invalid billing date';
        }
        
        if (!empty($data['due_date']) && !strtotime($data['due_date'])) {
            $errors['due_date'] = 'Invalid due date';
        }
        
        return $errors;
    }

        // Get pending payments for veterinary
    public function getPendingPaymentsForVeterinary($veterinaryId) {
        $sql = "SELECT b.*, a.name as animal_name,
                    CONCAT(u.first_name, ' ', u.last_name) as client_name,
                    u.phone as client_phone
                FROM {$this->table} b
                JOIN animals a ON b.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                JOIN users u ON c.user_id = u.user_id
                WHERE b.payment_status = 'paid'
                AND b.deposit_slip IS NOT NULL
                AND EXISTS (
                    SELECT 1 FROM treatments t 
                    WHERE t.treatment_id = b.treatment_id 
                    AND t.veterinary_id = ?
                )
                ORDER BY b.payment_date DESC";
        
        return fetchAll($sql, [$veterinaryId]);
    }

    // Get payments needing verification for admin
    public function getPaymentsNeedingVerification() {
        $sql = "SELECT b.*, a.name as animal_name,
                    CONCAT(u.first_name, ' ', u.last_name) as client_name,
                    u.phone as client_phone,
                    CONCAT(vet.first_name, ' ', vet.last_name) as vet_name
                FROM {$this->table} b
                JOIN animals a ON b.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                JOIN users u ON c.user_id = u.user_id
                LEFT JOIN treatments t ON b.treatment_id = t.treatment_id
                LEFT JOIN users vet ON t.veterinary_id = vet.user_id
                WHERE b.payment_status = 'paid'
                AND b.deposit_slip IS NOT NULL
                AND b.verified_by IS NULL
                ORDER BY b.payment_date DESC";
        
        return fetchAll($sql);
    }

    // Verify payment
    public function verifyPayment($billingId, $verifiedBy) {
        $data = [
            'verified_by' => $verifiedBy,
            'verified_at' => date('Y-m-d H:i:s'),
            'payment_status' => 'verified'
        ];
        
        return $this->update($billingId, $data);
    }

    // Reject payment
    public function rejectPayment($billingId, $rejectedBy, $notes = null) {
        $data = [
            'verified_by' => $rejectedBy,
            'verified_at' => date('Y-m-d H:i:s'),
            'payment_status' => 'pending',
            'notes' => $notes ?: 'Payment rejected - please verify deposit slip'
        ];
        
        return $this->update($billingId, $data);
    }


// Calculate sum of a column with conditions
private function calculateSum($column, $conditions = []) {
    $sql = "SELECT COALESCE(SUM({$column}), 0) as total FROM {$this->table} WHERE 1=1";
    $params = [];
    
    foreach ($conditions as $field => $value) {
        $sql .= " AND {$field} = ?";
        $params[] = $value;
    }
    
    $result = fetchOne($sql, $params);
    return $result['total'] ?? 0;
}

// Get recent payments for dashboard
public function getRecentPayments($userRole, $userId, $limit = 5) {
    switch ($userRole) {
        case ROLE_ADMIN:
            $sql = "SELECT b.*, a.name as animal_name, 
                           CONCAT(u.first_name, ' ', u.last_name) as client_name
                    FROM {$this->table} b
                    JOIN animals a ON b.animal_id = a.animal_id
                    JOIN clients c ON a.client_id = c.client_id
                    JOIN users u ON c.user_id = u.user_id
                    WHERE b.payment_status IN ('paid', 'verified')
                    ORDER BY b.payment_date DESC LIMIT ?";
            return fetchAll($sql, [$limit]);
            
        case ROLE_VETERINARY:
            $sql = "SELECT b.*, a.name as animal_name, 
                           CONCAT(u.first_name, ' ', u.last_name) as client_name
                    FROM {$this->table} b
                    JOIN treatments t ON b.treatment_id = t.treatment_id
                    JOIN animals a ON b.animal_id = a.animal_id
                    JOIN clients c ON a.client_id = c.client_id
                    JOIN users u ON c.user_id = u.user_id
                    WHERE t.veterinary_id = ? 
                    AND b.payment_status IN ('paid', 'verified')
                    ORDER BY b.payment_date DESC LIMIT ?";
            return fetchAll($sql, [$userId, $limit]);
            
        case ROLE_CLIENT:
            $clientId = (new Client())->getClientIdByUserId($userId);
            if ($clientId) {
                $sql = "SELECT b.*, a.name as animal_name
                        FROM {$this->table} b
                        JOIN animals a ON b.animal_id = a.animal_id
                        WHERE a.client_id = ? 
                        AND b.payment_status IN ('paid', 'verified')
                        ORDER BY b.payment_date DESC LIMIT ?";
                return fetchAll($sql, [$clientId, $limit]);
            }
            break;
    }
    
    return [];
}


public function getDashboardStats($userRole, $userId) {
    $stats = [];
    
    switch ($userRole) {
        case ROLE_ADMIN:
            $stats['pending_verification'] = $this->count([
                'payment_status' => 'paid', 
                'verified_by' => null
            ]);
            $stats['total_revenue'] = $this->calculateSum('total_amount', ['payment_status' => 'verified']);
            $stats['pending_revenue'] = $this->calculateSum('total_amount', ['payment_status' => 'paid']);
            break;
            
        case ROLE_VETERINARY:
            $sql = "SELECT COUNT(*) as pending_verification, 
                           COALESCE(SUM(b.total_amount), 0) as pending_revenue
                    FROM {$this->table} b
                    JOIN treatments t ON b.treatment_id = t.treatment_id
                    WHERE b.payment_status = 'paid' 
                    AND b.verified_by IS NULL
                    AND t.veterinary_id = ?";
            $result = fetchOne($sql, [$userId]);
            $stats['pending_verification'] = $result['pending_verification'] ?? 0;
            $stats['pending_revenue'] = $result['pending_revenue'] ?? 0;
            break;
            
        case ROLE_CLIENT:
            $clientId = (new Client())->getClientIdByUserId($userId);
            if ($clientId) {
                $sql = "SELECT COUNT(*) as pending_bills,
                               COALESCE(SUM(total_amount), 0) as total_pending
                        FROM {$this->table} b
                        JOIN animals a ON b.animal_id = a.animal_id
                        WHERE a.client_id = ? AND b.payment_status = 'pending'";
                $result = fetchOne($sql, [$clientId]);
                $stats['pending_bills'] = $result['pending_bills'] ?? 0;
                $stats['total_pending'] = $result['total_pending'] ?? 0;
            }
            break;
    }
    
    return $stats;
}
}
?>