<?php
class BillingController extends Controller {
    private $billingModel;
    private $animalModel;
    private $treatmentModel;
    private $clientModel;
    
    public function __construct() {
        $this->billingModel = new Billing();
        $this->animalModel = new Animal();
        $this->treatmentModel = new Treatment();
        $this->clientModel = new Client();
    }
    
    // List all billings
    public function index() {
        requireLogin();
        
        $page = $this->get('page', 1);
        $search = $this->get('search');
        $status = $this->get('status');
        
        if ($search) {
            $billings = $this->billingModel->searchBillings($search);
        } else {
            $billings = $this->paginate($this->billingModel, $page);
        }
        
        // Filter by status if specified
        if ($status) {
            $billings = array_filter($billings, function($billing) use ($status) {
                return $billing['payment_status'] === $status;
            });
        }
        
        $this->setTitle('Billings');
        $this->setData('billings', $billings);
        $this->setData('search', $search);
        $this->setData('status', $status);
        $this->setData('stats', $this->billingModel->getStats());
        $this->view('billings/index');
    }
    
    // Show create billing form
    public function create() {
        requireLogin();
        
        $animals = $this->animalModel->getActiveAnimals();
        $treatments = $this->treatmentModel->findAll();
        
        $this->setTitle('Create Invoice');
        $this->setData('animals', $animals);
        $this->setData('treatments', $treatments);
        $this->view('billings/create');
    }
    
    // Store new billing
    public function store() {
        requireLogin();
        
        if (!$this->isPost()) {
            $this->redirect('/billings/create');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $billingData = $this->input();
            $errors = $this->billingModel->validate($billingData);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('old', $billingData);
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->setData('treatments', $this->treatmentModel->findAll());
                $this->create();
                return;
            }
            
            $billingId = $this->billingModel->createBilling($billingData);
            
            if ($billingId) {
                $this->setFlash('success', 'Invoice created successfully');
                $this->redirect('/billings/' . $billingId);
            } else {
                $this->setFlash('error', 'Failed to create invoice');
                $this->setData('old', $billingData);
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->setData('treatments', $this->treatmentModel->findAll());
                $this->create();
            }
            
        } catch (Exception $e) {
            logError("Billing creation error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while creating invoice');
            $this->create();
        }
    }
    
    // Show billing details
    public function show($id) {
        requireLogin();
        
        $billing = $this->billingModel->getBillingWithDetails($id);
        
        if (!$billing) {
            $this->setFlash('error', 'Invoice not found');
            $this->redirect('/billings');
            return;
        }
        
        $this->setTitle('Invoice #' . $billing['billing_id']);
        $this->setData('billing', $billing);
        $this->view('billings/show');
    }
    
    // Show edit billing form
    public function edit($id) {
        requireLogin();
        
        $billing = $this->billingModel->find($id);
        
        if (!$billing) {
            $this->setFlash('error', 'Invoice not found');
            $this->redirect('/billings');
            return;
        }
        
        $animals = $this->animalModel->getActiveAnimals();
        $treatments = $this->treatmentModel->findAll();
        
        $this->setTitle('Edit Invoice #' . $billing['billing_id']);
        $this->setData('billing', $billing);
        $this->setData('animals', $animals);
        $this->setData('treatments', $treatments);
        $this->view('billings/edit');
    }
    
    // Update billing
    public function update($id) {
        requireLogin();
        
        if (!$this->isPost()) {
            $this->redirect('/billings/' . $id . '/edit');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $billingData = $this->input();
            $errors = $this->billingModel->validate($billingData, $id);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('billing', array_merge(['billing_id' => $id], $billingData));
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->setData('treatments', $this->treatmentModel->findAll());
                $this->edit($id);
                return;
            }
            
            $updated = $this->billingModel->update($id, $billingData);
            
            if ($updated) {
                $this->setFlash('success', 'Invoice updated successfully');
                $this->redirect('/billings/' . $id);
            } else {
                $this->setFlash('error', 'Failed to update invoice');
                $this->setData('billing', array_merge(['billing_id' => $id], $billingData));
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->setData('treatments', $this->treatmentModel->findAll());
                $this->edit($id);
            }
            
        } catch (Exception $e) {
            logError("Billing update error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while updating invoice');
            $this->edit($id);
        }
    }
    
    // Mark billing as paid
    public function markPaid($id) {
        requireLogin();
        
        if (!$this->isPost()) {
            $this->redirect('/billings/' . $id);
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $paymentMethod = $this->get('payment_method');
            $paymentDate = $this->get('payment_date');
            
            $paid = $this->billingModel->markAsPaid($id, $paymentMethod, $paymentDate);
            
            if ($paid) {
                $this->setFlash('success', 'Invoice marked as paid');
            } else {
                $this->setFlash('error', 'Failed to mark invoice as paid');
            }
            
            $this->redirect('/billings/' . $id);
            
        } catch (Exception $e) {
            logError("Billing mark paid error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while updating invoice');
            $this->redirect('/billings/' . $id);
        }
    }
    
    // Get pending billings
    public function pending() {
        requireLogin();
        
        $pendingBillings = $this->billingModel->getPendingBillings();
        $overdueBillings = $this->billingModel->getOverdueBillings();
        
        $this->setTitle('Pending Invoices');
        $this->setData('pendingBillings', $pendingBillings);
        $this->setData('overdueBillings', $overdueBillings);
        $this->view('billings/pending');
    }
    
    // Get billings by client
    public function byClient($clientId) {
        requireLogin();
        
        $billings = $this->billingModel->getBillingsByClient($clientId);
        $client = $this->clientModel->find($clientId);
        
        if (!$client) {
            $this->setFlash('error', 'Client not found');
            $this->redirect('/billings');
            return;
        }
        
        $this->setTitle('Invoices - ' . $client['name']);
        $this->setData('billings', $billings);
        $this->setData('client', $client);
        $this->view('billings/client-billings');
    }
    
    // Get revenue report
    public function revenueReport() {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        $startDate = $this->get('start_date', date('Y-m-01'));
        $endDate = $this->get('end_date', date('Y-m-t'));
        
        $revenueData = $this->billingModel->getRevenueByPeriod($startDate, $endDate);
        $recentBillings = $this->billingModel->getBillingsByDateRange($startDate, $endDate);
        
        $this->setTitle('Revenue Report');
        $this->setData('revenueData', $revenueData);
        $this->setData('recentBillings', $recentBillings);
        $this->setData('startDate', $startDate);
        $this->setData('endDate', $endDate);
        $this->view('billings/revenue-report');
    }
    
    // AJAX billing search
    public function search() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $term = $this->get('term');
        $billings = [];
        
        if (!empty($term)) {
            $billings = $this->billingModel->searchBillings($term);
        }
        
        $this->json($billings);
    }
    
    // Get billing statistics (AJAX)
    public function stats() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $stats = $this->billingModel->getStats();
        $this->json($stats);
    }
    
    // Generate invoice PDF
    public function generatePdf($id) {
        requireLogin();
        
        $billing = $this->billingModel->getBillingWithDetails($id);
        
        if (!$billing) {
            $this->setFlash('error', 'Invoice not found');
            $this->redirect('/billings');
            return;
        }
        
        // In a real application, you would generate PDF here
        // For now, we'll just show a message
        $this->setFlash('info', 'PDF generation would be implemented here');
        $this->redirect('/billings/' . $id);
    }
}
?>