<?php
class VeterinaryController extends Controller {
    private $treatmentModel;
    private $vaccineModel;
    private $animalModel;
    private $userModel;
    private $billingModel;
    
    public function __construct() {
        $this->treatmentModel = new Treatment();
        $this->vaccineModel = new Vaccine();
        $this->animalModel = new Animal();
        $this->userModel = new User();
        $this->billingModel = new Billing();
    }
    
    public function dashboard() {
        requireLogin();
        $this->authorize([ROLE_VETERINARY, ROLE_ADMIN]);
        
        $vet_id = $_SESSION['user_id'];
        
        // Get dashboard statistics
        $stats = [
            'my_patients' => $this->getMyPatientsCount($vet_id),
            'today_treatments' => $this->getTodayTreatmentsCount($vet_id),
            'follow_ups' => $this->getFollowUpsCount($vet_id),
            'vaccinations_due' => $this->getVaccinationsDueCount($vet_id),
            'ongoing_treatments' => $this->getOngoingTreatmentsCount($vet_id),
            'completed_treatments' => $this->getCompletedTreatmentsCount($vet_id),
            'follow_up_required' => $this->getFollowUpRequiredCount($vet_id),
            'total_revenue' => $this->getTotalRevenue($vet_id)
        ];
        
        // Get recent data
        $recentTreatments = $this->getRecentTreatments($vet_id, 5);
        $upcomingFollowUps = $this->getUpcomingFollowUps($vet_id);
        $vaccinationsDue = $this->getVaccinationsDue($vet_id, 7);
        
        $this->setTitle('Veterinary Dashboard');
        $this->setData('stats', $stats);
        $this->setData('recentTreatments', $recentTreatments);
        $this->setData('upcomingFollowUps', $upcomingFollowUps);
        $this->setData('vaccinationsDue', $vaccinationsDue);
        $this->view('veterinary/dashboard');
    }
    
    private function getMyPatientsCount($vet_id) {
        $sql = "SELECT COUNT(DISTINCT animal_id) as count FROM treatments WHERE veterinary_id = ?";
        $result = fetchOne($sql, [$vet_id]);
        return $result['count'] ?? 0;
    }
    
    private function getTodayTreatmentsCount($vet_id) {
        $sql = "SELECT COUNT(*) as count FROM treatments WHERE veterinary_id = ? AND DATE(treatment_date) = CURDATE()";
        $result = fetchOne($sql, [$vet_id]);
        return $result['count'] ?? 0;
    }
    
    private function getFollowUpsCount($vet_id) {
        $sql = "SELECT COUNT(*) as count FROM treatments WHERE veterinary_id = ? AND follow_up_date >= CURDATE() AND status != 'completed'";
        $result = fetchOne($sql, [$vet_id]);
        return $result['count'] ?? 0;
    }
    
    private function getVaccinationsDueCount($vet_id) {
        $sql = "SELECT COUNT(*) as count FROM vaccines WHERE administered_by = ? AND next_due_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND status != 'completed'";
        $result = fetchOne($sql, [$vet_id]);
        return $result['count'] ?? 0;
    }
    
    private function getOngoingTreatmentsCount($vet_id) {
        $sql = "SELECT COUNT(*) as count FROM treatments WHERE veterinary_id = ? AND status = 'ongoing'";
        $result = fetchOne($sql, [$vet_id]);
        return $result['count'] ?? 0;
    }
    
    private function getCompletedTreatmentsCount($vet_id) {
        $sql = "SELECT COUNT(*) as count FROM treatments WHERE veterinary_id = ? AND status = 'completed'";
        $result = fetchOne($sql, [$vet_id]);
        return $result['count'] ?? 0;
    }
    
    private function getFollowUpRequiredCount($vet_id) {
        $sql = "SELECT COUNT(*) as count FROM treatments WHERE veterinary_id = ? AND status = 'follow_up'";
        $result = fetchOne($sql, [$vet_id]);
        return $result['count'] ?? 0;
    }
    
    private function getTotalRevenue($vet_id) {
        $sql = "SELECT SUM(b.total_amount) as total 
                FROM billings b
                JOIN treatments t ON b.treatment_id = t.treatment_id
                WHERE t.veterinary_id = ? AND b.payment_status = 'paid'";
        $result = fetchOne($sql, [$vet_id]);
        return $result['total'] ?? 0;
    }
    
    private function getRecentTreatments($vet_id, $limit = 5) {
        $sql = "SELECT t.*, a.name as animal_name, a.species, a.breed,
                       CONCAT(u.first_name, ' ', u.last_name) as client_name
                FROM treatments t
                JOIN animals a ON t.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                JOIN users u ON c.user_id = u.user_id
                WHERE t.veterinary_id = ?
                ORDER BY t.treatment_date DESC 
                LIMIT ?";
        
        $stmt = $this->treatmentModel->query($sql, [$vet_id, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getUpcomingFollowUps($vet_id) {
        $sql = "SELECT t.*, a.name as animal_name, a.species,
                       CONCAT(u.first_name, ' ', u.last_name) as client_name
                FROM treatments t
                JOIN animals a ON t.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                JOIN users u ON c.user_id = u.user_id
                WHERE t.veterinary_id = ? 
                AND t.follow_up_date >= CURDATE() 
                AND t.status != 'completed'
                ORDER BY t.follow_up_date ASC 
                LIMIT 5";
        
        $stmt = $this->treatmentModel->query($sql, [$vet_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getVaccinationsDue($vet_id, $days = 7) {
        $sql = "SELECT v.*, a.name as animal_name, a.species, a.breed,
                       CONCAT(u.first_name, ' ', u.last_name) as client_name
                FROM vaccines v
                JOIN animals a ON v.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                JOIN users u ON c.user_id = u.user_id
                WHERE v.administered_by = ?
                AND v.next_due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                AND v.status != 'completed'
                ORDER BY v.next_due_date ASC 
                LIMIT 5";
        
        $stmt = $this->vaccineModel->query($sql, [$vet_id, $days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>