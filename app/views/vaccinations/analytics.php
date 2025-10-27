// Create vaccinations/analytics.php
public function analytics() {
    requireLogin();
    $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
    
    $userRole = $_SESSION['role'];
    $userId = $_SESSION['user_id'];
    
    $stats = $this->vaccineModel->getVaccinationStats($userRole, $userId);
    $monthlyData = $this->vaccineModel->getMonthlyVaccinationData();
    $commonVaccines = $this->vaccineModel->getMostCommonVaccines();
    $speciesDistribution = $this->vaccineModel->getVaccinationBySpecies();
    
    $this->setTitle('Vaccination Analytics');
    $this->setData('stats', $stats);
    $this->setData('monthlyData', $monthlyData);
    $this->setData('commonVaccines', $commonVaccines);
    $this->setData('speciesDistribution', $speciesDistribution);
    
    $this->view('vaccinations/analytics', 'dashboard');
}