<?php
class Analytics extends Model {
    protected $table = 'analytics'; // Base table for fallback
    
    /**
     * Get user growth analytics
     */
    public function getUserGrowthAnalytics() {
        $sql = "SELECT * FROM user_analytics_view";
        return fetchAll($sql);
    }
    
    /**
     * Get treatment distribution analytics
     */
    public function getTreatmentDistribution() {
        $sql = "SELECT 
                    status as treatment_status,
                    COUNT(*) as count,
                    ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM treatments)), 2) as percentage
                FROM treatments 
                GROUP BY status";
        return fetchAll($sql);
    }
    
    /**
     * Get revenue analytics
     */
    public function getRevenueAnalytics() {
        $sql = "SELECT * FROM revenue_analytics_view";
        return fetchAll($sql);
    }
    
    /**
     * Get species distribution
     */
    public function getSpeciesDistribution() {
        $sql = "SELECT * FROM species_analytics_view";
        return fetchAll($sql);
    }
    
    /**
     * Get veterinary performance
     */
    public function getVeterinaryPerformance() {
        $sql = "SELECT * FROM veterinary_performance_view";
        return fetchAll($sql);
    }
    
    /**
     * Get client activity
     */
    public function getClientActivity($limit = 10) {
        $sql = "SELECT * FROM client_activity_view ORDER BY total_spent DESC LIMIT ?";
        return fetchAll($sql, [$limit]);
    }
    
    /**
     * Get dashboard stats for admin
     */
    public function getAdminDashboardStats() {
        $stats = [];
        
        // Total counts
        $stats['total_users'] = $this->getTotalUsers();
        $stats['total_animals'] = $this->getTotalAnimals();
        $stats['total_treatments'] = $this->getTotalTreatments();
        $stats['active_treatments'] = $this->getActiveTreatments();
        $stats['total_revenue'] = $this->getTotalRevenue();
        
        // Growth metrics
        $stats['user_growth'] = $this->getUserGrowthRate();
        $stats['revenue_growth'] = $this->getRevenueGrowthRate();
        
        return $stats;
    }
    
    /**
     * Get dashboard stats for veterinary
     */
    public function getVeterinaryDashboardStats($veterinaryId) {
        $stats = [];
        
        $sql = "SELECT 
                    COUNT(DISTINCT a.animal_id) as my_patients,
                    COUNT(t.treatment_id) as total_treatments,
                    COUNT(CASE WHEN t.status = 'ongoing' THEN 1 END) as ongoing_treatments,
                    COUNT(CASE WHEN t.status = 'follow_up' THEN 1 END) as follow_up_treatments,
                    SUM(t.cost) as total_revenue,
                    COUNT(CASE WHEN t.treatment_date = CURDATE() THEN 1 END) as today_treatments
                FROM users u
                LEFT JOIN treatments t ON u.user_id = t.veterinary_id
                LEFT JOIN animals a ON t.animal_id = a.animal_id
                WHERE u.user_id = ?";
        
        $result = fetchOne($sql, [$veterinaryId]);
        
        if ($result) {
            $stats = $result;
        }
        
        return $stats;
    }
    
    /**
     * Get dashboard stats for client
     */
    public function getClientDashboardStats($clientId) {
        $stats = [];
        
        $sql = "SELECT 
                    COUNT(DISTINCT a.animal_id) as my_animals,
                    COUNT(t.treatment_id) as total_treatments,
                    COUNT(CASE WHEN t.status = 'ongoing' THEN 1 END) as ongoing_treatments,
                    COUNT(v.vaccine_id) as total_vaccinations,
                    SUM(b.total_amount) as total_spent,
                    MAX(t.treatment_date) as last_visit
                FROM clients c
                LEFT JOIN animals a ON c.client_id = a.client_id
                LEFT JOIN treatments t ON a.animal_id = t.animal_id
                LEFT JOIN vaccines v ON a.animal_id = v.animal_id
                LEFT JOIN billings b ON a.animal_id = b.animal_id
                WHERE c.client_id = ?";
        
        $result = fetchOne($sql, [$clientId]);
        
        if ($result) {
            $stats = $result;
        }
        
        return $stats;
    }
    
    // Helper methods
    private function getTotalUsers() {
        $sql = "SELECT COUNT(*) as count FROM users WHERE is_active = 1";
        $result = fetchOne($sql);
        return $result['count'] ?? 0;
    }
    
    private function getTotalAnimals() {
        $sql = "SELECT COUNT(*) as count FROM animals WHERE status = 'active'";
        $result = fetchOne($sql);
        return $result['count'] ?? 0;
    }
    
    private function getTotalTreatments() {
        $sql = "SELECT COUNT(*) as count FROM treatments";
        $result = fetchOne($sql);
        return $result['count'] ?? 0;
    }
    
    private function getActiveTreatments() {
        $sql = "SELECT COUNT(*) as count FROM treatments WHERE status IN ('ongoing', 'follow_up')";
        $result = fetchOne($sql);
        return $result['count'] ?? 0;
    }
    
    private function getTotalRevenue() {
        $sql = "SELECT SUM(total_amount) as total FROM billings WHERE payment_status = 'paid'";
        $result = fetchOne($sql);
        return $result['total'] ?? 0;
    }
    
    private function getUserGrowthRate() {
        $sql = "SELECT 
                    (COUNT(*) - LAG(COUNT(*)) OVER (ORDER BY month)) / LAG(COUNT(*)) OVER (ORDER BY month) * 100 as growth_rate
                FROM user_analytics_view 
                ORDER BY month DESC 
                LIMIT 1";
        $result = fetchOne($sql);
        return $result['growth_rate'] ?? 0;
    }
    
    private function getRevenueGrowthRate() {
        $sql = "SELECT 
                    (total_revenue - LAG(total_revenue) OVER (ORDER BY month)) / LAG(total_revenue) OVER (ORDER BY month) * 100 as growth_rate
                FROM revenue_analytics_view 
                ORDER BY month DESC 
                LIMIT 1";
        $result = fetchOne($sql);
        return $result['growth_rate'] ?? 0;
    }
}
?>