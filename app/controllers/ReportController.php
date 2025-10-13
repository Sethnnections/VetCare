<?php
class ReportController extends Controller {
    private $animalModel;
    private $clientModel;
    private $treatmentModel;
    private $billingModel;
    private $vaccineModel;
    private $userModel;
    
    public function __construct() {
        $this->animalModel = new Animal();
        $this->clientModel = new Client();
        $this->treatmentModel = new Treatment();
        $this->billingModel = new Billing();
        $this->vaccineModel = new Vaccine();
        $this->userModel = new User();
    }
    
    // Reports dashboard
    public function index() {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        $this->setTitle('Reports');
        $this->view('reports/index');
    }
    
    // Animals report
    public function animals() {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        $species = $this->get('species');
        $status = $this->get('status');
        
        $animals = $this->animalModel->findAll();
        
        // Apply filters
        if ($species) {
            $animals = array_filter($animals, function($animal) use ($species) {
                return strtolower($animal['species']) === strtolower($species);
            });
        }
        
        if ($status) {
            $animals = array_filter($animals, function($animal) use ($status) {
                return $animal['status'] == $status;
            });
        }
        
        $stats = $this->animalModel->getStats();
        
        $this->setTitle('Animals Report');
        $this->setData('animals', $animals);
        $this->setData('species', $species);
        $this->setData('status', $status);
        $this->setData('stats', $stats);
        $this->view('reports/animals');
    }
    
    // Clients report
    public function clients() {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        $city = $this->get('city');
        $status = $this->get('status');
        
        $clients = $this->clientModel->findAll();
        
        // Apply filters
        if ($city) {
            $clients = array_filter($clients, function($client) use ($city) {
                return strtolower($client['city']) === strtolower($city);
            });
        }
        
        if ($status) {
            $clients = array_filter($clients, function($client) use ($status) {
                return $client['status'] == $status;
            });
        }
        
        $stats = $this->clientModel->getStats();
        
        $this->setTitle('Clients Report');
        $this->setData('clients', $clients);
        $this->setData('city', $city);
        $this->setData('status', $status);
        $this->setData('stats', $stats);
        $this->view('reports/clients');
    }
    
    // Treatments report
    public function treatments() {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        $startDate = $this->get('start_date', date('Y-m-01'));
        $endDate = $this->get('end_date', date('Y-m-t'));
        $veterinaryId = $this->get('veterinary_id');
        $status = $this->get('status');
        
        $treatments = $this->treatmentModel->getTreatmentsByDateRange($startDate, $endDate, $veterinaryId);
        
        // Filter by status if specified
        if ($status) {
            $treatments = array_filter($treatments, function($treatment) use ($status) {
                return $treatment['status'] === $status;
            });
        }
        
        $veterinaries = $this->userModel->getUsersByRole(ROLE_VETERINARY);
        $stats = $this->treatmentModel->getStats();
        
        $this->setTitle('Treatments Report');
        $this->setData('treatments', $treatments);
        $this->setData('startDate', $startDate);
        $this->setData('endDate', $endDate);
        $this->setData('veterinaryId', $veterinaryId);
        $this->setData('status', $status);
        $this->setData('veterinaries', $veterinaries);
        $this->setData('stats', $stats);
        $this->view('reports/treatments');
    }
    
    // Financial report
    public function financial() {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        $startDate = $this->get('start_date', date('Y-m-01'));
        $endDate = $this->get('end_date', date('Y-m-t'));
        
        $revenueData = $this->billingModel->getRevenueByPeriod($startDate, $endDate);
        $billings = $this->billingModel->getBillingsByDateRange($startDate, $endDate);
        $treatmentCosts = $this->treatmentModel->getTreatmentCostByPeriod($startDate, $endDate);
        
        $this->setTitle('Financial Report');
        $this->setData('revenueData', $revenueData);
        $this->setData('billings', $billings);
        $this->setData('treatmentCosts', $treatmentCosts);
        $this->setData('startDate', $startDate);
        $this->setData('endDate', $endDate);
        $this->view('reports/financial');
    }
    
    // Vaccinations report
    public function vaccinations() {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        $startDate = $this->get('start_date', date('Y-m-01'));
        $endDate = $this->get('end_date', date('Y-m-t'));
        $status = $this->get('status');
        
        $vaccinations = $this->vaccineModel->getVaccinesByDateRange($startDate, $endDate);
        
        // Filter by status if specified
        if ($status) {
            $vaccinations = array_filter($vaccinations, function($vaccine) use ($status) {
                return $vaccine['status'] === $status;
            });
        }
        
        $upcomingVaccinations = $this->vaccineModel->getUpcomingVaccinations(30);
        $stats = $this->vaccineModel->getStats();
        
        $this->setTitle('Vaccinations Report');
        $this->setData('vaccinations', $vaccinations);
        $this->setData('upcomingVaccinations', $upcomingVaccinations);
        $this->setData('startDate', $startDate);
        $this->setData('endDate', $endDate);
        $this->setData('status', $status);
        $this->setData('stats', $stats);
        $this->view('reports/vaccinations');
    }
    
    // Veterinary performance report
    public function veterinaryPerformance() {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        $startDate = $this->get('start_date', date('Y-m-01'));
        $endDate = $this->get('end_date', date('Y-m-t'));
        
        $veterinaries = $this->userModel->getUsersByRole(ROLE_VETERINARY);
        $performanceData = [];
        
        foreach ($veterinaries as $vet) {
            $treatments = $this->treatmentModel->getTreatmentsByVeterinary($vet['user_id']);
            $periodTreatments = array_filter($treatments, function($treatment) use ($startDate, $endDate) {
                return $treatment['treatment_date'] >= $startDate && $treatment['treatment_date'] <= $endDate;
            });
            
            $completed = array_filter($periodTreatments, function($treatment) {
                return $treatment['status'] === STATUS_COMPLETED;
            });
            
            $performanceData[] = [
                'veterinary' => $vet,
                'total_treatments' => count($periodTreatments),
                'completed_treatments' => count($completed),
                'completion_rate' => count($periodTreatments) > 0 ? 
                    round((count($completed) / count($periodTreatments)) * 100, 2) : 0
            ];
        }
        
        $this->setTitle('Veterinary Performance Report');
        $this->setData('performanceData', $performanceData);
        $this->setData('startDate', $startDate);
        $this->setData('endDate', $endDate);
        $this->view('reports/veterinary-performance');
    }
    
    // Generate PDF report
    public function generatePdf($reportType) {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        $params = $this->input();
        
        // In a real application, you would generate PDF here
        // For now, we'll just show a success message
        
        $this->setFlash('success', "PDF report for {$reportType} would be generated here");
        $this->redirect('/reports');
    }
    
    // Export report data (CSV/Excel)
    public function export($reportType) {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        $params = $this->input();
        $format = $this->get('format', 'csv');
        
        // In a real application, you would generate CSV/Excel here
        // For now, we'll just show a success message
        
        $this->setFlash('success', "{$format} export for {$reportType} would be generated here");
        $this->redirect('/reports');
    }
    
    // Get report data (AJAX)
    public function data($reportType) {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $params = $this->input();
        $data = [];
        
        switch ($reportType) {
            case 'monthly_revenue':
                $data = $this->getMonthlyRevenueData();
                break;
                
            case 'treatment_stats':
                $data = $this->getTreatmentStatsData();
                break;
                
            case 'animal_stats':
                $data = $this->getAnimalStatsData();
                break;
        }
        
        $this->json($data);
    }
    
    private function getMonthlyRevenueData() {
        $months = [];
        $revenue = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} months"));
            $startDate = date('Y-m-01', strtotime($month));
            $endDate = date('Y-m-t', strtotime($month));
            
            $revenueData = $this->billingModel->getRevenueByPeriod($startDate, $endDate);
            
            $months[] = date('M Y', strtotime($month));
            $revenue[] = $revenueData['paid_revenue'] ?? 0;
        }
        
        return [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Monthly Revenue',
                    'data' => $revenue,
                    'backgroundColor' => '#4361ee'
                ]
            ]
        ];
    }
    
    private function getTreatmentStatsData() {
        $stats = $this->treatmentModel->getStats();
        
        return [
            'labels' => ['Ongoing', 'Completed', 'Follow-up Required'],
            'datasets' => [
                [
                    'label' => 'Treatments',
                    'data' => [
                        $stats['ongoing'] ?? 0,
                        $stats['completed'] ?? 0,
                        $stats['follow_up'] ?? 0
                    ],
                    'backgroundColor' => ['#ffc107', '#28a745', '#17a2b8']
                ]
            ]
        ];
    }
    
    private function getAnimalStatsData() {
        $stats = $this->animalModel->getStats();
        
        $species = [];
        $counts = [];
        
        foreach ($stats['by_species'] as $speciesData) {
            $species[] = $speciesData['species'];
            $counts[] = $speciesData['count'];
        }
        
        return [
            'labels' => $species,
            'datasets' => [
                [
                    'label' => 'Animals by Species',
                    'data' => $counts,
                    'backgroundColor' => ['#4361ee', '#3a0ca3', '#7209b7', '#f72585', '#4cc9f0']
                ]
            ]
        ];
    }
}
?>