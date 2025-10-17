<?php

class AdminController extends Controller {
    private $userModel;
    private $clientModel;
    private $animalModel;
    private $treatmentModel;
    
    public function __construct() {
        $this->authorize(ROLE_ADMIN);
        
        $this->userModel = new User();
        $this->clientModel = new Client();
        $this->animalModel = new Animal();
        $this->treatmentModel = new Treatment();
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

        public function animalAssignments() {
        requireLogin();
        $this->authorize([ROLE_ADMIN]);
        
        $unassignedAnimals = $this->animalModel->getUnassignedAnimals();
        $veterinarians = $this->animalModel->getAvailableVeterinarians();
        $currentAssignments = $this->animalModel->getVeterinaryAssignments();
        
        $this->setTitle('Animal Assignment Management');
        $this->setData('unassignedAnimals', $unassignedAnimals);
        $this->setData('veterinarians', $veterinarians);
        $this->setData('currentAssignments', $currentAssignments);
        $this->view('admin/animal-assignments');
    }
    
    /**
     * Assign animal to veterinary
     */
    public function assignAnimal() {
        requireLogin();
        $this->authorize([ROLE_ADMIN]);
        
        if (!$this->isPost()) {
            $this->redirect('/admin/animal-assignments');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $animalId = $this->input('animal_id');
            $veterinaryId = $this->input('veterinary_id');
            
            if (empty($animalId) || empty($veterinaryId)) {
                $this->setFlash('error', 'Please select both animal and veterinary');
                $this->redirect('/admin/animal-assignments');
                return;
            }
            
            $assigned = $this->animalModel->assignToVeterinary($animalId, $veterinaryId);
            
            if ($assigned) {
                // Log the assignment
                $animal = $this->animalModel->find($animalId);
                $veterinary = $this->userModel->find($veterinaryId);
                
                $vetName = !empty($veterinary['first_name']) ? 
                    $veterinary['first_name'] . ' ' . $veterinary['last_name'] : 
                    $veterinary['username'];
                
                logActivity("Animal '{$animal['name']}' assigned to veterinary '{$vetName}' by admin");
                $this->setFlash('success', 'Animal assigned to veterinary successfully');
            } else {
                $this->setFlash('error', 'Failed to assign animal to veterinary');
            }
            
            $this->redirect('/admin/animal-assignments');
            
        } catch (Exception $e) {
            logError("Animal assignment error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while assigning animal');
            $this->redirect('/admin/animal-assignments');
        }
    }
    
    /**
     * Unassign animal from veterinary
     */
    public function unassignAnimal($animalId) {
        requireLogin();
        $this->authorize([ROLE_ADMIN]);
        
        if (!$this->isPost()) {
            $this->redirect('/admin/animal-assignments');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $unassigned = $this->animalModel->unassignFromVeterinary($animalId);
            
            if ($unassigned) {
                $animal = $this->animalModel->find($animalId);
                logActivity("Animal '{$animal['name']}' unassigned from veterinary by admin");
                $this->setFlash('success', 'Animal unassigned successfully');
            } else {
                $this->setFlash('error', 'Failed to unassign animal');
            }
            
            $this->redirect('/admin/animal-assignments');
            
        } catch (Exception $e) {
            logError("Animal unassignment error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while unassigning animal');
            $this->redirect('/admin/animal-assignments');
        }
    }
}
?>

