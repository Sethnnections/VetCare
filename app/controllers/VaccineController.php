<?php
class VaccineController extends Controller {
    private $vaccineModel;
    private $animalModel;
    private $userModel;
    private $reminderModel;
    
    public function __construct() {
        $this->vaccineModel = new Vaccine();
        $this->animalModel = new Animal();
        $this->userModel = new User();
        $this->reminderModel = new Reminder();
    }
    
    // List all vaccines
    public function index() {
        requireLogin();
        
        $page = $this->get('page', 1);
        $search = $this->get('search');
        $status = $this->get('status');
        
        if ($search) {
            $vaccines = $this->vaccineModel->searchVaccines($search);
        } else {
            $vaccines = $this->paginate($this->vaccineModel, $page);
        }
        
        // Filter by status if specified
        if ($status) {
            $vaccines = array_filter($vaccines, function($vaccine) use ($status) {
                return $vaccine['status'] === $status;
            });
        }
        
        $this->setTitle('Vaccinations');
        $this->setData('vaccines', $vaccines);
        $this->setData('search', $search);
        $this->setData('status', $status);
        $this->setData('stats', $this->vaccineModel->getStats());
        $this->view('vaccines/index');
    }
    
    // Show create vaccine form
    public function create() {
        requireLogin();
        
        $animals = $this->animalModel->getActiveAnimals();
        $veterinaries = $this->userModel->getUsersByRole(ROLE_VETERINARY);
        
        $this->setTitle('Add New Vaccination');
        $this->setData('animals', $animals);
        $this->setData('veterinaries', $veterinaries);
        $this->view('vaccines/create');
    }
    
    // Store new vaccine
    public function store() {
        requireLogin();
        
        if (!$this->isPost()) {
            $this->redirect('/vaccines/create');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $vaccineData = $this->input();
            $errors = $this->vaccineModel->validate($vaccineData);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('old', $vaccineData);
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->setData('veterinaries', $this->userModel->getUsersByRole(ROLE_VETERINARY));
                $this->create();
                return;
            }
            
            $vaccineId = $this->vaccineModel->createVaccine($vaccineData);
            
            if ($vaccineId) {
                // Create reminder for next due date if provided
                if (!empty($vaccineData['next_due_date'])) {
                    $vaccine = $this->vaccineModel->getVaccineWithDetails($vaccineId);
                    $this->reminderModel->createVaccinationReminder(
                        $vaccineData['animal_id'],
                        $vaccine
                    );
                }
                
                $this->setFlash('success', 'Vaccination recorded successfully');
                $this->redirect('/vaccines/' . $vaccineId);
            } else {
                $this->setFlash('error', 'Failed to record vaccination');
                $this->setData('old', $vaccineData);
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->setData('veterinaries', $this->userModel->getUsersByRole(ROLE_VETERINARY));
                $this->create();
            }
            
        } catch (Exception $e) {
            logError("Vaccine creation error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while recording vaccination');
            $this->create();
        }
    }
    
    // Show vaccine details
    public function show($id) {
        requireLogin();
        
        $vaccine = $this->vaccineModel->getVaccineWithDetails($id);
        
        if (!$vaccine) {
            $this->setFlash('error', 'Vaccination record not found');
            $this->redirect('/vaccines');
            return;
        }
        
        $this->setTitle('Vaccination: ' . $vaccine['vaccine_name']);
        $this->setData('vaccine', $vaccine);
        $this->view('vaccines/show');
    }
    
    // Show edit vaccine form
    public function edit($id) {
        requireLogin();
        
        $vaccine = $this->vaccineModel->find($id);
        
        if (!$vaccine) {
            $this->setFlash('error', 'Vaccination record not found');
            $this->redirect('/vaccines');
            return;
        }
        
        $animals = $this->animalModel->getActiveAnimals();
        $veterinaries = $this->userModel->getUsersByRole(ROLE_VETERINARY);
        
        $this->setTitle('Edit Vaccination');
        $this->setData('vaccine', $vaccine);
        $this->setData('animals', $animals);
        $this->setData('veterinaries', $veterinaries);
        $this->view('vaccines/edit');
    }
    
    // Update vaccine
    public function update($id) {
        requireLogin();
        
        if (!$this->isPost()) {
            $this->redirect('/vaccines/' . $id . '/edit');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $vaccineData = $this->input();
            $errors = $this->vaccineModel->validate($vaccineData, $id);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('vaccine', array_merge(['vaccine_id' => $id], $vaccineData));
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->setData('veterinaries', $this->userModel->getUsersByRole(ROLE_VETERINARY));
                $this->edit($id);
                return;
            }
            
            $updated = $this->vaccineModel->update($id, $vaccineData);
            
            if ($updated) {
                $this->setFlash('success', 'Vaccination updated successfully');
                $this->redirect('/vaccines/' . $id);
            } else {
                $this->setFlash('error', 'Failed to update vaccination');
                $this->setData('vaccine', array_merge(['vaccine_id' => $id], $vaccineData));
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->setData('veterinaries', $this->userModel->getUsersByRole(ROLE_VETERINARY));
                $this->edit($id);
            }
            
        } catch (Exception $e) {
            logError("Vaccine update error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while updating vaccination');
            $this->edit($id);
        }
    }
    
    // Mark vaccine as completed
    public function complete($id) {
        requireLogin();
        
        if (!$this->isPost()) {
            $this->redirect('/vaccines/' . $id);
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $completed = $this->vaccineModel->markAsCompleted($id);
            
            if ($completed) {
                $this->setFlash('success', 'Vaccination marked as completed');
            } else {
                $this->setFlash('error', 'Failed to complete vaccination');
            }
            
            $this->redirect('/vaccines/' . $id);
            
        } catch (Exception $e) {
            logError("Vaccine completion error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while completing vaccination');
            $this->redirect('/vaccines/' . $id);
        }
    }
    
    // Get upcoming vaccinations
    public function upcoming() {
        requireLogin();
        
        $days = $this->get('days', 30);
        $upcomingVaccinations = $this->vaccineModel->getUpcomingVaccinations($days);
        $overdueVaccinations = $this->vaccineModel->getOverdueVaccinations();
        
        $this->setTitle('Upcoming Vaccinations');
        $this->setData('upcomingVaccinations', $upcomingVaccinations);
        $this->setData('overdueVaccinations', $overdueVaccinations);
        $this->setData('days', $days);
        $this->view('vaccines/upcoming');
    }
    
    // Get vaccines by animal
    public function byAnimal($animalId) {
        requireLogin();
        
        $vaccines = $this->vaccineModel->getVaccinesByAnimal($animalId);
        $animal = $this->animalModel->getAnimalWithClient($animalId);
        
        if (!$animal) {
            $this->setFlash('error', 'Animal not found');
            $this->redirect('/vaccines');
            return;
        }
        
        $this->setTitle('Vaccinations - ' . $animal['name']);
        $this->setData('vaccines', $vaccines);
        $this->setData('animal', $animal);
        $this->view('vaccines/animal-vaccines');
    }
    
    // AJAX vaccine search
    public function search() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $term = $this->get('term');
        $vaccines = [];
        
        if (!empty($term)) {
            $vaccines = $this->vaccineModel->searchVaccines($term);
        }
        
        $this->json($vaccines);
    }
    
    // Get vaccine statistics (AJAX)
    public function stats() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $stats = $this->vaccineModel->getStats();
        $this->json($stats);
    }
    
    // Get vaccination schedule report
    public function scheduleReport() {
        requireLogin();
        
        $startDate = $this->get('start_date', date('Y-m-d'));
        $endDate = $this->get('end_date', date('Y-m-d', strtotime('+30 days')));
        
        $vaccinations = $this->vaccineModel->getVaccinesByDateRange($startDate, $endDate);
        
        $this->setTitle('Vaccination Schedule Report');
        $this->setData('vaccinations', $vaccinations);
        $this->setData('startDate', $startDate);
        $this->setData('endDate', $endDate);
        $this->view('vaccines/schedule-report');
    }
}
?>