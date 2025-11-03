<?php
class DashboardController extends Controller {
    private $analyticsModel;
    private $userModel;
    private $animalModel;
    private $treatmentModel;
    private $clientModel;
    private $appointmentModel;
    private $vaccineModel;
    
    public function __construct() {
        $this->analyticsModel = new Analytics();
        $this->userModel = new User();
        $this->animalModel = new Animal();
        $this->treatmentModel = new Treatment();
        $this->clientModel = new Client();
        $this->appointmentModel = new Appointment();
        $this->vaccineModel = new Vaccine();
    }
    
    public function index() {
        requireLogin();
        
        $userRole = $_SESSION['role'] ?? '';
        
        switch ($userRole) {
            case ROLE_ADMIN:
                $this->admin();
                break;
            case ROLE_VETERINARY:
                $this->veterinary();
                break;
            case ROLE_CLIENT:
                $this->client();
                break;
            default:
                $this->redirect('/');
                break;
        }
    }
    
 // In the admin() method, add this:
public function admin() {
    requireLogin();
    $this->authorize(ROLE_ADMIN);
    
    // Get comprehensive dashboard data
    $billingModel = new Billing();
    $stats = $this->analyticsModel->getAdminDashboardStats();
    $recentUsers = $this->getRecentUsers(5);
    $recentTreatments = $this->getRecentTreatments(5);
    $upcomingAppointments = $this->appointmentModel->getUpcomingAppointments(7);
    $systemAlerts = $this->getSystemAlerts();

    // Add billing stats
    $this->setData('billing_stats', $billingModel->getDashboardStats(ROLE_ADMIN, $_SESSION['user_id']));
    $this->setData('recent_payments', $billingModel->getRecentPayments(ROLE_ADMIN, $_SESSION['user_id']));
    
    $this->setTitle('Admin Dashboard');
    $this->setData('stats', $stats);
    $this->setData('recentUsers', $recentUsers);
    $this->setData('recentTreatments', $recentTreatments);
    $this->setData('upcomingAppointments', $upcomingAppointments);
    $this->setData('systemAlerts', $systemAlerts);
    $this->setData('current_page', 'dashboard');
    $this->view('admin/dashboard', 'dashboard');
}

    
    public function veterinary() {
        requireLogin();
        $this->authorize(ROLE_VETERINARY);

        $billingModel = new Billing();
        
        $veterinaryId = $_SESSION['user_id'];
        
        // Get comprehensive veterinary dashboard data
        $stats = $this->analyticsModel->getVeterinaryDashboardStats($veterinaryId);
        $myAppointments = $this->appointmentModel->getTodayAppointments($veterinaryId);
        $myAnimals = $this->animalModel->getAnimalsByVeterinary($veterinaryId);
        $followUpTreatments = $this->treatmentModel->getUpcomingFollowUps($veterinaryId);
        $recentTreatments = $this->getVeterinaryRecentTreatments($veterinaryId, 5);
            // Add billing stats
        $this->setData('billing_stats', $billingModel->getDashboardStats(ROLE_VETERINARY, $_SESSION['user_id']));
        $this->setData('recent_payments', $billingModel->getRecentPayments(ROLE_VETERINARY, $_SESSION['user_id']));
    
        
        $this->setTitle('Veterinary Dashboard');
        $this->setData('stats', $stats);
        $this->setData('myAppointments', $myAppointments);
        $this->setData('myAnimals', $myAnimals);
        $this->setData('followUpTreatments', $followUpTreatments);
        $this->setData('recentTreatments', $recentTreatments);
        $this->setData('current_page', 'dashboard');
        $this->view('veterinary/dashboard', 'dashboard');
    }
    
    public function client() {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        $userId = $_SESSION['user_id'];
        $clientModel = new Client();
        $billingModel = new Billing();

        $client = $clientModel->getClientByUserId($userId);
        
        if (!$client) {
            $this->setFlash('info', 'Please complete your profile to continue');
            $this->redirect('/client/profile/create');
            return;
        }
        
        $clientId = $client['client_id'];
        
        // Get comprehensive client dashboard data
        $stats = $this->analyticsModel->getClientDashboardStats($clientId);
        $myAnimals = $this->animalModel->getAnimalsByClient($clientId);
        $upcomingAppointments = $this->getClientAppointments($clientId);
        $recentTreatments = $this->clientModel->getClientTreatments($clientId, 5);
        $vaccinationReminders = $this->getClientVaccinationReminders($clientId);

        $this->setData('billing_stats', $billingModel->getDashboardStats(ROLE_VETERINARY, $_SESSION['user_id']));
        $this->setData('recent_payments', $billingModel->getRecentPayments(ROLE_VETERINARY, $_SESSION['user_id']));
    
        
        $this->setTitle('Client Dashboard');
        $this->setData('stats', $stats);
        $this->setData('myAnimals', $myAnimals);
        $this->setData('upcomingAppointments', $upcomingAppointments);
        $this->setData('recentTreatments', $recentTreatments);
        $this->setData('vaccinationReminders', $vaccinationReminders);
        $this->setData('current_page', 'dashboard');
        $this->view('client/dashboard');
    }
    
    // Helper methods
    private function getRecentUsers($limit = 5) {
        $sql = "SELECT u.*, 
                       CONCAT(u.first_name, ' ', u.last_name) as full_name,
                       u.role,
                       u.created_at
                FROM users u 
                WHERE u.is_active = 1 
                ORDER BY u.created_at DESC 
                LIMIT ?";
        return fetchAll($sql, [$limit]);
    }
    
    private function getRecentTreatments($limit = 5) {
        $sql = "SELECT t.*, 
                       a.name as animal_name,
                       CONCAT(u.first_name, ' ', u.last_name) as client_name,
                       CONCAT(vet.first_name, ' ', vet.last_name) as vet_name
                FROM treatments t
                JOIN animals a ON t.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                JOIN users u ON c.user_id = u.user_id
                JOIN users vet ON t.veterinary_id = vet.user_id
                ORDER BY t.treatment_date DESC 
                LIMIT ?";
        return fetchAll($sql, [$limit]);
    }
    
    private function getVeterinaryRecentTreatments($veterinaryId, $limit = 5) {
        $sql = "SELECT t.*, 
                       a.name as animal_name,
                       CONCAT(u.first_name, ' ', u.last_name) as client_name
                FROM treatments t
                JOIN animals a ON t.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                JOIN users u ON c.user_id = u.user_id
                WHERE t.veterinary_id = ?
                ORDER BY t.treatment_date DESC 
                LIMIT ?";
        return fetchAll($sql, [$veterinaryId, $limit]);
    }
    
    private function getClientAppointments($clientId) {
        $sql = "SELECT a.*, 
                       an.name as animal_name,
                       CONCAT(u.first_name, ' ', u.last_name) as vet_name
                FROM appointments a
                JOIN animals an ON a.animal_id = an.animal_id
                LEFT JOIN users u ON a.veterinary_id = u.user_id
                WHERE a.client_id = ? AND a.appointment_date >= CURDATE()
                ORDER BY a.appointment_date ASC, a.appointment_time ASC
                LIMIT 5";
        return fetchAll($sql, [$clientId]);
    }
    
    private function getClientVaccinationReminders($clientId) {
        $sql = "SELECT v.*, 
                       a.name as animal_name,
                       v.next_due_date,
                       DATEDIFF(v.next_due_date, CURDATE()) as days_until_due
                FROM vaccines v
                JOIN animals a ON v.animal_id = a.animal_id
                WHERE a.client_id = ? AND v.next_due_date >= CURDATE()
                ORDER BY v.next_due_date ASC
                LIMIT 5";
        return fetchAll($sql, [$clientId]);
    }
    
    private function getSystemAlerts() {
        $alerts = [];
        
        // Check for overdue treatments
        $sql = "SELECT COUNT(*) as count FROM treatments 
                WHERE follow_up_date < CURDATE() AND status != 'completed'";
        $result = fetchOne($sql);
        if ($result['count'] > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$result['count']} treatments require follow-up",
                'link' => '/treatments/follow-ups'
            ];
        }
        
        // Check for pending billings
        $sql = "SELECT COUNT(*) as count FROM billings 
                WHERE payment_status = 'pending' AND due_date < CURDATE()";
        $result = fetchOne($sql);
        if ($result['count'] > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "{$result['count']} overdue payments",
                'link' => '/billings'
            ];
        }
        
        // Check for upcoming vaccinations
        $sql = "SELECT COUNT(*) as count FROM vaccines 
                WHERE next_due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
        $result = fetchOne($sql);
        if ($result['count'] > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$result['count']} vaccinations due this week",
                'link' => '/vaccinations/upcoming'
            ];
        }
        
        return $alerts;
    }

    // In DashboardController.php

}
?>