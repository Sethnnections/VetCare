<?php
class BillingController extends Controller {
    private $billingModel;
    private $animalModel;
    private $treatmentModel;
    private $clientModel;
    private $userModel;
    
    public function __construct() {
        $this->billingModel = new Billing();
        $this->animalModel = new Animal();
        $this->treatmentModel = new Treatment();
        $this->clientModel = new Client();
        $this->userModel = new User();
    }
    
    // List billings with role-based filtering
    public function index() {
        requireLogin();
        
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['user_id'];
        $page = $this->get('page', 1);
        $status = $this->get('status');
        
        // Role-based data filtering
        switch ($userRole) {
            case ROLE_ADMIN:
                $billings = $this->billingModel->getAllBillings($status, $page, 15);
                break;
                
            case ROLE_VETERINARY:
                $billings = $this->billingModel->getBillingsByVeterinary($userId, $status, $page, 15);
                break;
                
            case ROLE_CLIENT:
                $clientId = $this->clientModel->getClientIdByUserId($userId);
                if ($clientId) {
                    $billings = $this->billingModel->getBillingsByClient($clientId, $status, $page, 15);
                } else {
                    $billings = ['data' => [], 'total' => 0, 'total_pages' => 0];
                }
                break;
                
            default:
                $billings = ['data' => [], 'total' => 0, 'total_pages' => 0];
        }
        
        $this->setTitle('Billing & Payments');
        $this->setData('billings', $billings['data']);
        $this->setData('pagination', [
            'current_page' => $page,
            'total_pages' => $billings['total_pages'],
            'total' => $billings['total']
        ]);
        $this->setData('status', $status);
        $this->setData('stats', $this->billingModel->getBillingStats($userRole, $userId));
        $this->setData('overdue_bills', $this->billingModel->getOverdueBills($userRole, $userId));
        
        // Load appropriate view based on role
        $viewPath = $userRole . '/billings/index';
        $this->view($viewPath, 'dashboard');
    }
    
    // Show create billing form (Admin/Veterinary)
    public function create() {
        requireLogin();
        $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
        
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['user_id'];
        
        // Get animals based on role
        if ($userRole === ROLE_VETERINARY) {
            $animals = $this->animalModel->getAnimalsByVeterinary($userId);
        } else {
            $animals = $this->animalModel->getActiveAnimals();
        }
        
        $treatments = $this->treatmentModel->findAll();
        
        $this->setTitle('Create New Bill');
        $this->setData('animals', $animals);
        $this->setData('treatments', $treatments);
        $this->view('billings/create', 'dashboard');
    }
    
    // Store new billing
    public function store() {
        requireLogin();
        $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
        
        if (!$this->isPost()) {
            $this->redirect('/billings/create');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $billingData = $this->input();
            
            // Calculate total if not provided
            if (empty($billingData['total_amount'])) {
                $billingData['total_amount'] = $billingData['amount'] + 
                    ($billingData['tax_amount'] ?? 0) - 
                    ($billingData['discount'] ?? 0);
            }
            
            $errors = $this->billingModel->validate($billingData);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('old', $billingData);
                $this->create();
                return;
            }
            
            $billingId = $this->billingModel->create($billingData);
            
            if ($billingId) {
                logActivity("Billing created: ID {$billingId} for animal {$billingData['animal_id']}", $_SESSION['user_id']);
                $this->setFlash('success', 'Bill created successfully');
                $this->redirect('/billings/' . $billingId);
            } else {
                $this->setFlash('error', 'Failed to create bill');
                $this->setData('old', $billingData);
                $this->create();
            }
            
        } catch (Exception $e) {
            logError("Billing creation error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while creating bill');
            $this->create();
        }
    }
    
    // Show billing details
    public function show($id) {
        requireLogin();
        
        $billing = $this->billingModel->getBillingWithDetails($id);
        
        if (!$billing) {
            $this->setFlash('error', 'Billing record not found');
            $this->redirect('/billings');
            return;
        }
        
        // Check permissions
        if (!$this->canViewBilling($billing)) {
            $this->setFlash('error', 'Access denied');
            $this->redirect('/billings');
            return;
        }
        
        $billingItems = $this->billingModel->getBillingItems($id);
        
        $this->setTitle('Billing Details');
        $this->setData('billing', $billing);
        $this->setData('billing_items', $billingItems);
        
        // Load appropriate view based on role
        $userRole = $_SESSION['role'];
        $viewPath = $userRole . '/billings/show';
        $this->view($viewPath, 'dashboard');
    }
    
    // Show payment form for client
    public function payment($id) {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        $billing = $this->billingModel->getBillingWithDetails($id);
        $clientId = $this->clientModel->getClientIdByUserId($_SESSION['user_id']);
        
        // Check if billing belongs to client
        if (!$billing || !$this->isClientBilling($billing, $clientId)) {
            $this->setFlash('error', 'Billing record not found or access denied');
            $this->redirect('/client/billings');
            return;
        }
        
        if ($billing['payment_status'] === 'paid' || $billing['payment_status'] === 'verified') {
            $this->setFlash('info', 'This bill has already been paid');
            $this->redirect('/client/billings/' . $id);
            return;
        }
        
        $this->setTitle('Make Payment');
        $this->setData('billing', $billing);
        $this->view('client/billings/payment', 'dashboard');
    }
    
    // Process payment with deposit slip upload
    public function processPayment($id) {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        if (!$this->isPost()) {
            $this->redirect('/client/billings/' . $id . '/payment');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $billing = $this->billingModel->find($id);
            $clientId = $this->clientModel->getClientIdByUserId($_SESSION['user_id']);
            
            // Check if billing belongs to client
            if (!$billing || !$this->isClientBilling($billing, $clientId)) {
                $this->setFlash('error', 'Billing record not found or access denied');
                $this->redirect('/client/billings');
                return;
            }
            
            if ($billing['payment_status'] === 'paid' || $billing['payment_status'] === 'verified') {
                $this->setFlash('info', 'This bill has already been paid');
                $this->redirect('/client/billings/' . $id);
                return;
            }
            
            $paymentData = $this->input();
            $errors = $this->validatePayment($paymentData);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('billing', $billing);
                $this->payment($id);
                return;
            }
            
            // Handle deposit slip upload
            if (isset($_FILES['deposit_slip']) && $_FILES['deposit_slip']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->uploadDepositSlip($_FILES['deposit_slip']);
                if ($uploadResult['success']) {
                    $paymentData['deposit_slip'] = $uploadResult['filename'];
                } else {
                    $this->setFlash('error', $uploadResult['error']);
                    $this->setData('billing', $billing);
                    $this->payment($id);
                    return;
                }
            } else {
                $this->setFlash('error', 'Please upload a deposit slip');
                $this->setData('billing', $billing);
                $this->payment($id);
                return;
            }
            
            // Update payment status
            $updated = $this->billingModel->updatePayment($id, $paymentData);
            
            if ($updated) {
                logActivity("Payment processed for billing ID: {$id} by client ID: {$clientId}", $_SESSION['user_id']);
                $this->setFlash('success', 'Payment submitted successfully. Please wait for verification.');
                $this->redirect('/client/billings/' . $id);
            } else {
                $this->setFlash('error', 'Failed to process payment');
                $this->setData('billing', $billing);
                $this->payment($id);
            }
            
        } catch (Exception $e) {
            logError("Payment processing error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while processing payment');
            $this->payment($id);
        }
    }
    
    // Download deposit slip
    public function downloadSlip($id) {
        requireLogin();
        
        $billing = $this->billingModel->find($id);
        
        if (!$billing || empty($billing['deposit_slip'])) {
            $this->setFlash('error', 'Deposit slip not found');
            $this->redirect('/billings/' . $id);
            return;
        }
        
        // Check permissions
        if (!$this->canViewBilling($billing)) {
            $this->setFlash('error', 'Access denied');
            $this->redirect('/billings');
            return;
        }
        
        $filepath = ROOT_PATH . '/uploads/deposit-slips/' . $billing['deposit_slip'];
        
        if (!file_exists($filepath)) {
            $this->setFlash('error', 'Deposit slip file not found');
            $this->redirect('/billings/' . $id);
            return;
        }
        
        // Set headers for download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="deposit_slip_' . $id . '_' . $billing['deposit_slip'] . '"');
        header('Content-Length: ' . filesize($filepath));
        
        readfile($filepath);
        exit;
    }
    
    // Generate invoice PDF
    public function generateInvoice($id) {
        requireLogin();
        
        $billing = $this->billingModel->getBillingWithDetails($id);
        
        if (!$billing) {
            $this->setFlash('error', 'Billing record not found');
            $this->redirect('/billings');
            return;
        }
        
        // Check permissions
        if (!$this->canViewBilling($billing)) {
            $this->setFlash('error', 'Access denied');
            $this->redirect('/billings');
            return;
        }
        
        $billingItems = $this->billingModel->getBillingItems($id);
        
        // Simple HTML invoice for now - you can integrate a PDF library like TCPDF or Dompdf later
        $html = $this->generateInvoiceHTML($billing, $billingItems);
        
        header('Content-Type: text/html');
        echo $html;
        exit;
    }
    
    // Helper methods
    private function canViewBilling($billing) {
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['user_id'];
        
        switch ($userRole) {
            case ROLE_ADMIN:
                return true;
                
            case ROLE_VETERINARY:
                $treatment = $this->treatmentModel->find($billing['treatment_id']);
                return $treatment && $treatment['veterinary_id'] == $userId;
                
            case ROLE_CLIENT:
                $clientId = $this->clientModel->getClientIdByUserId($userId);
                $animal = $this->animalModel->find($billing['animal_id']);
                return $animal && $animal['client_id'] == $clientId;
                
            default:
                return false;
        }
    }
    
    private function isClientBilling($billing, $clientId) {
        $animal = $this->animalModel->find($billing['animal_id']);
        return $animal && $animal['client_id'] == $clientId;
    }
    
    private function validatePayment($data) {
        $errors = [];
        
        if (empty($data['payment_method'])) {
            $errors['payment_method'] = 'Payment method is required';
        }
        
        if (!isset($_FILES['deposit_slip']) || $_FILES['deposit_slip']['error'] !== UPLOAD_ERR_OK) {
            $errors['deposit_slip'] = 'Deposit slip is required';
        }
        
        return $errors;
    }
    
    private function uploadDepositSlip($file) {
        $uploadDir = ROOT_PATH . '/uploads/deposit-slips/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        $fileType = mime_content_type($file['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            return ['success' => false, 'error' => 'Only JPEG, PNG, and PDF files are allowed'];
        }
        
        // Validate file size (5MB max)
        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'error' => 'File size must be less than 5MB'];
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'deposit_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'filename' => $filename];
        } else {
            return ['success' => false, 'error' => 'Failed to upload file'];
        }
    }
    
    private function generateInvoiceHTML($billing, $items) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Invoice #<?php echo $billing['billing_id']; ?></title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .details { margin-bottom: 20px; }
                .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                .table th { background-color: #f2f2f2; }
                .total { text-align: right; font-weight: bold; }
                .footer { margin-top: 30px; text-align: center; color: #666; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>VETERINARY INVOICE</h1>
                <p>Invoice #: <?php echo $billing['billing_id']; ?></p>
            </div>
            
            <div class="details">
                <p><strong>Client:</strong> <?php echo htmlspecialchars($billing['client_name']); ?></p>
                <p><strong>Animal:</strong> <?php echo htmlspecialchars($billing['animal_name']); ?> (<?php echo htmlspecialchars($billing['species']); ?>)</p>
                <p><strong>Invoice Date:</strong> <?php echo formatDate($billing['billing_date'], 'F j, Y'); ?></p>
                <p><strong>Due Date:</strong> <?php echo $billing['due_date'] ? formatDate($billing['due_date'], 'F j, Y') : 'N/A'; ?></p>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Veterinary Services</td>
                        <td>MWK <?php echo number_format($billing['amount'], 2); ?></td>
                    </tr>
                    <?php if ($billing['tax_amount'] > 0): ?>
                    <tr>
                        <td>Tax</td>
                        <td>MWK <?php echo number_format($billing['tax_amount'], 2); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($billing['discount'] > 0): ?>
                    <tr>
                        <td>Discount</td>
                        <td>- MWK <?php echo number_format($billing['discount'], 2); ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="total">Total Amount:</td>
                        <td><strong>MWK <?php echo number_format($billing['total_amount'], 2); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            
            <?php if ($billing['notes']): ?>
            <div class="notes">
                <p><strong>Notes:</strong> <?php echo htmlspecialchars($billing['notes']); ?></p>
            </div>
            <?php endif; ?>
            
            <div class="footer">
                <p>Thank you for your business!</p>
                <p>Generated on: <?php echo date('F j, Y \a\t g:i A'); ?></p>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    // AJAX methods
    public function stats() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['user_id'];
        
        $stats = $this->billingModel->getBillingStats($userRole, $userId);
        $this->json(['success' => true, 'stats' => $stats]);
    }
    
    // Get treatments by animal (AJAX)

    public function veterinaryPayments() {
        requireLogin();
        $this->authorize([ROLE_VETERINARY]);
        
        $veterinaryId = $_SESSION['user_id'];
        $pendingPayments = $this->billingModel->getPendingPaymentsForVeterinary($veterinaryId);
        
        $this->setTitle('Client Payments');
        $this->setData('payments', $pendingPayments);
        $this->view('veterinary/payments/index', 'dashboard');
    }

    // Veterinary payment details
    // Add these methods to your BillingController class

// Show billing details for veterinary
public function veterinaryPaymentDetails($id) {
    requireLogin();
    $this->authorize([ROLE_VETERINARY]);
    
    $billing = $this->billingModel->getBillingWithDetails($id);
    $veterinaryId = $_SESSION['user_id'];
    
    // Check if this payment belongs to veterinary's treatments
    if (!$billing || !$this->isVeterinaryBilling($billing, $veterinaryId)) {
        $this->setFlash('error', 'Payment record not found or access denied');
        $this->redirect('/veterinary/payments');
        return;
    }
    
    $billingItems = $this->billingModel->getBillingItems($id);
    
    $this->setTitle('Payment Details');
    $this->setData('billing', $billing);
    $this->setData('billing_items', $billingItems);
    $this->view('veterinary/payments/show', 'dashboard');
}

// Verify payment (Admin)
public function verifyPayment($id) {
    requireLogin();
    $this->authorize([ROLE_ADMIN]);
    
    if (!$this->isPost()) {
        $this->redirect('/admin/payments/pending');
        return;
    }
    
    try {
        $this->validateCsrf();
        
        $verified = $this->billingModel->verifyPayment($id, $_SESSION['user_id']);
        
        if ($verified) {
            logActivity("Payment verified for billing ID: {$id} by admin ID: {$_SESSION['user_id']}", $_SESSION['user_id']);
            $this->setFlash('success', 'Payment verified successfully');
        } else {
            $this->setFlash('error', 'Failed to verify payment');
        }
        
        $this->redirect('/admin/payments/pending');
        
    } catch (Exception $e) {
        logError("Payment verification error: " . $e->getMessage());
        $this->setFlash('error', 'An error occurred while verifying payment');
        $this->redirect('/admin/payments/pending');
    }
}

// Helper method to check if billing belongs to veterinary
private function isVeterinaryBilling($billing, $veterinaryId) {
    if (!$billing['treatment_id']) return false;
    
    $treatment = $this->treatmentModel->find($billing['treatment_id']);
    return $treatment && $treatment['veterinary_id'] == $veterinaryId;
}

// AJAX method to get treatments by animal
public function getTreatmentsByAnimal($animalId) {
    if (!$this->isAjax()) {
        $this->json(['error' => 'Invalid request'], 400);
        return;
    }
    
    $treatments = $this->treatmentModel->getTreatmentsByAnimal($animalId);
    $this->json(['success' => true, 'treatments' => $treatments]);
}









    // Admin pending payments
    public function pendingPayments() {
        requireLogin();
        $this->authorize([ROLE_ADMIN]);
        
        $pendingPayments = $this->billingModel->getPaymentsNeedingVerification();
        
        $this->setTitle('Pending Payment Verification');
        $this->setData('payments', $pendingPayments);
        $this->view('admin/payments/pending', 'dashboard');
    }

    // Reject payment
    public function rejectPayment($id) {
        requireLogin();
        $this->authorize([ROLE_ADMIN]);
        
        if (!$this->isPost()) {
            $this->redirect('/admin/payments/pending');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $data = $this->input();
            $rejected = $this->billingModel->rejectPayment($id, $_SESSION['user_id'], $data['rejection_notes'] ?? null);
            
            if ($rejected) {
                logActivity("Payment rejected for billing ID: {$id} by admin ID: {$_SESSION['user_id']}", $_SESSION['user_id']);
                $this->setFlash('success', 'Payment rejected successfully');
            } else {
                $this->setFlash('error', 'Failed to reject payment');
            }
            
            $this->redirect('/admin/payments/pending');
            
        } catch (Exception $e) {
            logError("Payment rejection error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while rejecting payment');
            $this->redirect('/admin/payments/pending');
        }
    }

 
    // Replace the verifiedPayments method with this:
public function verifiedPayments() {
    requireLogin();
    $this->authorize([ROLE_ADMIN]);
    
    $sql = "SELECT b.*, a.name as animal_name,
                CONCAT(u.first_name, ' ', u.last_name) as client_name,
                CONCAT(verifier.first_name, ' ', verifier.last_name) as verified_by_name
            FROM billings b
            JOIN animals a ON b.animal_id = a.animal_id
            JOIN clients c ON a.client_id = c.client_id
            JOIN users u ON c.user_id = u.user_id
            JOIN users verifier ON b.verified_by = verifier.user_id
            WHERE b.payment_status = 'verified'
            ORDER BY b.verified_at DESC";
    
    $verifiedPayments = fetchAll($sql);
    
    $this->setTitle('Verified Payments');
    $this->setData('payments', $verifiedPayments);
    $this->view('admin/payments/verified', 'dashboard');
}
}
?>