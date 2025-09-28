<?php
// app/controllers/AdminController.php

class AdminController extends Controller {
    private $userModel;
    private $clientModel;
    private $animalModel;
    private $treatmentModel;
    private $medicineModel;
    
    public function __construct() {
        $this->authorize(ROLE_ADMIN);
        
        $this->userModel = new User();
        $this->clientModel = new Client();
        $this->animalModel = new Animal();
        $this->treatmentModel = new Treatment();
        $this->medicineModel = new Medicine();
    }
    
    public function dashboard() {
        $this->setTitle('Admin Dashboard');
        
        // Get statistics
        $stats = [
            'users' => $this->userModel->getStats(),
            'clients' => $this->clientModel->getStats(),
            'animals' => $this->animalModel->getStats(),
            'treatments' => $this->treatmentModel->getStats()
        ];
        
        // Get recent activities
        $recentTreatments = $this->treatmentModel->getTreatmentsByDateRange(
            date('Y-m-d', strtotime('-7 days')), 
            date('Y-m-d')
        );
        
        $this->setData('stats', $stats);
        $this->setData('recentTreatments', $recentTreatments);
        $this->view('admin/dashboard');
    }
    
    public function users() {
        $this->setTitle('Manage Users');
        
        $page = $this->get('page', 1);
        $search = $this->get('search');
        
        if ($search) {
            $users = $this->userModel->searchUsers($search);
            $pagination = null;
        } else {
            $result = $this->userModel->paginate($page);
            $users = $result['data'];
            $pagination = $result['pagination'];
        }
        
        $this->setData('users', $users);
        $this->setData('pagination', $pagination);
        $this->setData('search', $search);
        $this->view('admin/users');
    }
    
    public function clients() {
        $this->setTitle('Manage Clients');
        
        $page = $this->get('page', 1);
        $search = $this->get('search');
        
        if ($search) {
            $clients = $this->clientModel->searchClients($search);
            $pagination = null;
        } else {
            $result = $this->clientModel->paginate($page);
            $clients = $result['data'];
            $pagination = $result['pagination'];
        }
        
        $this->setData('clients', $clients);
        $this->setData('pagination', $pagination);
        $this->setData('search', $search);
        $this->view('admin/clients');
    }
    
    public function medicines() {
        $this->setTitle('Manage Medicines');
        
        $page = $this->get('page', 1);
        $search = $this->get('search');
        $type = $this->get('type');
        
        $conditions = [];
        if ($type) {
            $conditions['type'] = $type;
        }
        
        if ($search) {
            $medicines = $this->medicineModel->searchMedicines($search);
            $pagination = null;
        } else {
            $result = $this->medicineModel->paginate($page, RECORDS_PER_PAGE, $conditions, 'name ASC');
            $medicines = $result['data'];
            $pagination = $result['pagination'];
        }
        
        $this->setData('medicines', $medicines);
        $this->setData('pagination', $pagination);
        $this->setData('search', $search);
        $this->setData('type', $type);
        $this->setData('medicineTypes', [
            MEDICINE_ANTIBIOTIC => 'Antibiotic',
            MEDICINE_VACCINE => 'Vaccine',
            MEDICINE_SUPPLEMENT => 'Supplement',
            MEDICINE_ANESTHETIC => 'Anesthetic'
        ]);
        $this->view('admin/medicines');
    }
    
    public function reports() {
        $this->setTitle('Reports');
        
        // Default date range (current month)
        $startDate = $this->get('start_date', date('Y-m-01'));
        $endDate = $this->get('end_date', date('Y-m-t'));
        
        // Get treatment statistics
        $treatmentStats = $this->treatmentModel->getTreatmentCostByPeriod($startDate, $endDate);
        
        // Get billing statistics
        $billingStats = $this->getBillingStats($startDate, $endDate);
        
        // Get client statistics
        $clientStats = $this->clientModel->getStats();
        
        $this->setData('startDate', $startDate);
        $this->setData('endDate', $endDate);
        $this->setData('treatmentStats', $treatmentStats);
        $this->setData('billingStats', $billingStats);
        $this->setData('clientStats', $clientStats);
        $this->view('admin/reports');
    }
    
    private function getBillingStats($startDate, $endDate) {
        $sql = "SELECT 
                    COUNT(*) as total_bills,
                    SUM(total_amount) as total_revenue,
                    AVG(total_amount) as average_bill,
                    payment_status,
                    COUNT(*) as status_count
                FROM billings 
                WHERE billing_date BETWEEN :start_date AND :end_date
                GROUP BY payment_status";
        
        return fetchAll($sql, ['start_date' => $startDate, 'end_date' => $endDate]);
    }
}
?>