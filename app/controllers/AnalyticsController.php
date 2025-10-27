<?php
class AnalyticsController extends Controller {
    private $analyticsModel;
    
    public function __construct() {
        $this->analyticsModel = new Analytics();
    }
    
    /**
     * Get analytics data for admin dashboard
     */
    public function getAdminAnalytics() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        try {
            $data = [
                'user_growth' => $this->analyticsModel->getUserGrowthAnalytics(),
                'treatment_distribution' => $this->analyticsModel->getTreatmentDistribution(),
                'revenue_analytics' => $this->analyticsModel->getRevenueAnalytics(),
                'species_distribution' => $this->analyticsModel->getSpeciesDistribution(),
                'veterinary_performance' => $this->analyticsModel->getVeterinaryPerformance(),
                'top_clients' => $this->analyticsModel->getClientActivity(5)
            ];
            
            $this->json(['success' => true, 'data' => $data]);
            
        } catch (Exception $e) {
            logError("Analytics error: " . $e->getMessage());
            $this->json(['error' => 'Failed to load analytics data'], 500);
        }
    }
    
    /**
     * Get analytics data for veterinary dashboard
     */
    public function getVeterinaryAnalytics() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        try {
            $veterinaryId = $_SESSION['user_id'];
            
            $data = [
                'my_stats' => $this->analyticsModel->getVeterinaryDashboardStats($veterinaryId),
                'treatment_trends' => $this->getVeterinaryTreatmentTrends($veterinaryId),
                'patient_distribution' => $this->getVeterinaryPatientDistribution($veterinaryId)
            ];
            
            $this->json(['success' => true, 'data' => $data]);
            
        } catch (Exception $e) {
            logError("Veterinary analytics error: " . $e->getMessage());
            $this->json(['error' => 'Failed to load analytics data'], 500);
        }
    }
    
    /**
     * Get analytics data for client dashboard
     */
    public function getClientAnalytics() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        try {
            $clientId = $this->getClientIdFromSession();
            
            if (!$clientId) {
                $this->json(['error' => 'Client not found'], 404);
                return;
            }
            
            $data = [
                'my_stats' => $this->analyticsModel->getClientDashboardStats($clientId),
                'treatment_history' => $this->getClientTreatmentHistory($clientId),
                'animal_health' => $this->getClientAnimalHealth($clientId)
            ];
            
            $this->json(['success' => true, 'data' => $data]);
            
        } catch (Exception $e) {
            logError("Client analytics error: " . $e->getMessage());
            $this->json(['error' => 'Failed to load analytics data'], 500);
        }
    }
    
    /**
     * Get veterinary treatment trends
     */
    private function getVeterinaryTreatmentTrends($veterinaryId) {
        $sql = "SELECT 
                    DATE_FORMAT(treatment_date, '%Y-%m') as month,
                    COUNT(*) as treatment_count,
                    SUM(cost) as monthly_revenue
                FROM treatments 
                WHERE veterinary_id = ? AND treatment_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(treatment_date, '%Y-%m')
                ORDER BY month";
        
        return fetchAll($sql, [$veterinaryId]);
    }
    
    /**
     * Get veterinary patient distribution
     */
    private function getVeterinaryPatientDistribution($veterinaryId) {
        $sql = "SELECT 
                    a.species,
                    COUNT(DISTINCT a.animal_id) as patient_count
                FROM treatments t
                JOIN animals a ON t.animal_id = a.animal_id
                WHERE t.veterinary_id = ?
                GROUP BY a.species
                ORDER BY patient_count DESC";
        
        return fetchAll($sql, [$veterinaryId]);
    }
    
    /**
     * Get client treatment history
     */
    private function getClientTreatmentHistory($clientId) {
        $sql = "SELECT 
                    DATE_FORMAT(t.treatment_date, '%Y-%m') as month,
                    COUNT(*) as treatment_count,
                    SUM(t.cost) as monthly_cost
                FROM treatments t
                JOIN animals a ON t.animal_id = a.animal_id
                WHERE a.client_id = ? AND t.treatment_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(t.treatment_date, '%Y-%m')
                ORDER BY month";
        
        return fetchAll($sql, [$clientId]);
    }
    
    /**
     * Get client animal health overview
     */
    private function getClientAnimalHealth($clientId) {
        $sql = "SELECT 
                    a.name as animal_name,
                    a.species,
                    COUNT(t.treatment_id) as total_treatments,
                    COUNT(CASE WHEN t.status = 'ongoing' THEN 1 END) as ongoing_treatments,
                    MAX(t.treatment_date) as last_treatment
                FROM animals a
                LEFT JOIN treatments t ON a.animal_id = t.animal_id
                WHERE a.client_id = ?
                GROUP BY a.animal_id, a.name, a.species
                ORDER BY last_treatment DESC";
        
        return fetchAll($sql, [$clientId]);
    }
    
    /**
     * Get client ID from session
     */
    private function getClientIdFromSession() {
        $userId = $_SESSION['user_id'];
        $clientModel = new Client();
        return $clientModel->getClientIdByUserId($userId);
    }
}
?>