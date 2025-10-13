<?php
class DashboardController extends Controller {
    private $animalModel;
    private $clientModel;
    private $treatmentModel;
    private $billingModel;
    private $vaccineModel;
    private $reminderModel;
    
    public function __construct() {
        $this->animalModel = new Animal();
        $this->clientModel = new Client();
        $this->treatmentModel = new Treatment();
        $this->billingModel = new Billing();
        $this->vaccineModel = new Vaccine();
        $this->reminderModel = new Reminder();
    }
    
    // Main dashboard - redirects based on role
    public function index() {
        requireLogin();
        
        $user = getCurrentUser();
        
        switch ($user['role']) {
            case ROLE_ADMIN:
                $this->adminDashboard();
                break;
            case ROLE_VETERINARY:
                $this->veterinaryDashboard();
                break;
            case ROLE_CLIENT:
                $this->clientDashboard();
                break;
            default:
                $this->redirect('/auth/logout');
                break;
        }
    }
    
    // Admin dashboard
    private function adminDashboard() {
        $stats = [
            'clients' => $this->clientModel->getStats(),
            'animals' => $this->animalModel->getStats(),
            'treatments' => $this->treatmentModel->getStats(),
            'billings' => $this->billingModel->getStats(),
        ];
        
        // Recent activities
        $recentTreatments = $this->treatmentModel->getTreatmentsByDateRange(
            date('Y-m-d', strtotime('-7 days')),
            date('Y-m-d')
        );
        
        $upcomingVaccinations = $this->vaccineModel->getUpcomingVaccinations(7);
        $pendingBillings = $this->billingModel->getPendingBillings();
        $overdueReminders = $this->reminderModel->getOverdueReminders();
        
        $this->setTitle('Admin Dashboard');
        $this->setData('stats', $stats);
        $this->setData('recentTreatments', array_slice($recentTreatments, 0, 10));
        $this->setData('upcomingVaccinations', array_slice($upcomingVaccinations, 0, 10));
        $this->setData('pendingBillings', array_slice($pendingBillings, 0, 10));
        $this->setData('overdueReminders', array_slice($overdueReminders, 0, 10));
        $this->view('dashboard/admin');
    }
    
    // Veterinary dashboard
    private function veterinaryDashboard() {
        $user = getCurrentUser();
        $veterinaryId = $user['user_id'];
        
        $stats = [
            'my_treatments' => $this->treatmentModel->count(['veterinary_id' => $veterinaryId]),
            'ongoing_treatments' => $this->treatmentModel->count([
                'veterinary_id' => $veterinaryId,
                'status' => TREATMENT_ONGOING
            ]),
            'follow_ups' => $this->treatmentModel->count([
                'veterinary_id' => $veterinaryId,
                'status' => TREATMENT_FOLLOW_UP
            ]),
        ];
        
        // My recent treatments
        $myTreatments = $this->treatmentModel->getTreatmentsByVeterinary($veterinaryId);
        $upcomingFollowUps = $this->treatmentModel->getUpcomingFollowUps($veterinaryId);
        $myReminders = $this->reminderModel->getRemindersByUser($veterinaryId);
        
        $this->setTitle('Veterinary Dashboard');
        $this->setData('stats', $stats);
        $this->setData('myTreatments', array_slice($myTreatments, 0, 10));
        $this->setData('upcomingFollowUps', array_slice($upcomingFollowUps, 0, 10));
        $this->setData('myReminders', array_slice($myReminders, 0, 10));
        $this->view('dashboard/veterinary');
    }
    
    // Client dashboard
    private function clientDashboard() {
        $user = getCurrentUser();
        $clientId = $this->getClientIdFromUser($user);
        
        if (!$clientId) {
            $this->setFlash('error', 'Client profile not found');
            $this->redirect('/auth/logout');
            return;
        }
        
        $myAnimals = $this->animalModel->getAnimalsByClient($clientId);
        $recentTreatments = $this->clientModel->getClientTreatments($clientId, 5);
        $upcomingVaccinations = $this->vaccineModel->getUpcomingVaccinations(30);
        $myReminders = $this->clientModel->getClientReminders($clientId);
        $pendingBillings = $this->clientModel->getPendingBilling($clientId);
        
        $this->setTitle('My Dashboard');
        $this->setData('myAnimals', $myAnimals);
        $this->setData('recentTreatments', $recentTreatments);
        $this->setData('upcomingVaccinations', $upcomingVaccinations);
        $this->setData('myReminders', $myReminders);
        $this->setData('pendingBillings', $pendingBillings);
        $this->view('dashboard/client');
    }
    
    // Get client ID from user (for client role)
    private function getClientIdFromUser($user) {
        // This would typically involve matching user email with client email
        // For now, we'll use a simple approach
        $client = $this->clientModel->findBy('email', $user['email']);
        return $client ? $client['client_id'] : null;
    }
    
    // Get dashboard statistics (AJAX)
    public function stats() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $user = getCurrentUser();
        $stats = [];
        
        switch ($user['role']) {
            case ROLE_ADMIN:
                $stats = [
                    'clients' => $this->clientModel->getStats(),
                    'animals' => $this->animalModel->getStats(),
                    'treatments' => $this->treatmentModel->getStats(),
                    'billings' => $this->billingModel->getStats(),
                ];
                break;
                
            case ROLE_VETERINARY:
                $veterinaryId = $user['user_id'];
                $stats = [
                    'my_treatments' => $this->treatmentModel->count(['veterinary_id' => $veterinaryId]),
                    'ongoing_treatments' => $this->treatmentModel->count([
                        'veterinary_id' => $veterinaryId,
                        'status' => TREATMENT_ONGOING
                    ]),
                    'follow_ups' => $this->treatmentModel->count([
                        'veterinary_id' => $veterinaryId,
                        'status' => TREATMENT_FOLLOW_UP
                    ]),
                ];
                break;
                
            case ROLE_CLIENT:
                $clientId = $this->getClientIdFromUser($user);
                if ($clientId) {
                    $stats = [
                        'animals' => count($this->animalModel->getAnimalsByClient($clientId)),
                        'pending_billings' => $this->clientModel->getPendingBilling($clientId),
                        'active_treatments' => count(array_filter(
                            $this->clientModel->getClientTreatments($clientId),
                            function($treatment) {
                                return $treatment['status'] != STATUS_COMPLETED;
                            }
                        )),
                    ];
                }
                break;
        }
        
        $this->json($stats);
    }
    
    // Get recent activities (AJAX)
    public function activities() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $user = getCurrentUser();
        $activities = [];
        
        switch ($user['role']) {
            case ROLE_ADMIN:
                $activities['recent_treatments'] = array_slice(
                    $this->treatmentModel->getTreatmentsByDateRange(
                        date('Y-m-d', strtotime('-7 days')),
                        date('Y-m-d')
                    ), 0, 5
                );
                break;
                
            case ROLE_VETERINARY:
                $veterinaryId = $user['user_id'];
                $activities['my_treatments'] = array_slice(
                    $this->treatmentModel->getTreatmentsByVeterinary($veterinaryId),
                    0, 5
                );
                break;
                
            case ROLE_CLIENT:
                $clientId = $this->getClientIdFromUser($user);
                if ($clientId) {
                    $activities['recent_treatments'] = array_slice(
                        $this->clientModel->getClientTreatments($clientId),
                        0, 5
                    );
                }
                break;
        }
        
        $this->json($activities);
    }
    
    // Quick search across the system
    public function quickSearch() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $term = $this->get('q');
        $results = [];
        
        if (!empty($term)) {
            // Search clients
            $clients = $this->clientModel->searchClients($term);
            foreach ($clients as $client) {
                $results[] = [
                    'type' => 'client',
                    'title' => $client['name'],
                    'description' => $client['phone'] . ' • ' . $client['email'],
                    'url' => '/clients/' . $client['client_id']
                ];
            }
            
            // Search animals
            $animals = $this->animalModel->searchAnimals($term);
            foreach ($animals as $animal) {
                $results[] = [
                    'type' => 'animal',
                    'title' => $animal['name'],
                    'description' => $animal['species'] . ' • ' . $animal['breed'],
                    'url' => '/animals/' . $animal['animal_id']
                ];
            }
            
            // Search treatments
            $treatments = $this->treatmentModel->searchTreatments($term);
            foreach ($treatments as $treatment) {
                $results[] = [
                    'type' => 'treatment',
                    'title' => $treatment['diagnosis'],
                    'description' => $treatment['animal_name'] . ' • ' . date('M j, Y', strtotime($treatment['treatment_date'])),
                    'url' => '/treatments/' . $treatment['treatment_id']
                ];
            }
        }
        
        $this->json(['results' => array_slice($results, 0, 10)]);
    }
}
?>