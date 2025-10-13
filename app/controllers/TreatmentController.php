<?php
class TreatmentController extends Controller {
    private $treatmentModel;
    private $animalModel;
    private $userModel;
    
    public function __construct() {
        $this->treatmentModel = new Treatment();
        $this->animalModel = new Animal();
        $this->userModel = new User();
    }
    
    // List all treatments
    public function index() {
        requireLogin();
        
        $page = $this->get('page', 1);
        $search = $this->get('search');
        $status = $this->get('status');
        
        if ($search) {
            $treatments = $this->treatmentModel->searchTreatments($search);
        } else {
            $treatments = $this->paginate($this->treatmentModel, $page);
        }
        
        // Filter by status if specified
        if ($status) {
            $treatments = array_filter($treatments, function($treatment) use ($status) {
                return $treatment['status'] === $status;
            });
        }
        
        $this->setTitle('Treatments');
        $this->setData('treatments', $treatments);
        $this->setData('search', $search);
        $this->setData('status', $status);
        $this->setData('stats', $this->treatmentModel->getStats());
        $this->view('treatments/index');
    }
    
    // Show create treatment form
    public function create() {
        requireLogin();
        
        $animals = $this->animalModel->getActiveAnimals();
        $veterinaries = $this->userModel->getUsersByRole(ROLE_VETERINARY);
        
        $this->setTitle('Add New Treatment');
        $this->setData('animals', $animals);
        $this->setData('veterinaries', $veterinaries);
        $this->view('treatments/create');
    }
    
    // Store new treatment
    public function store() {
        requireLogin();
        
        if (!$this->isPost()) {
            $this->redirect('/treatments/create');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $treatmentData = $this->input();
            $errors = $this->treatmentModel->validate($treatmentData);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('old', $treatmentData);
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->setData('veterinaries', $this->userModel->getUsersByRole(ROLE_VETERINARY));
                $this->create();
                return;
            }
            
            $treatmentId = $this->treatmentModel->createTreatment($treatmentData);
            
            if ($treatmentId) {
                $this->setFlash('success', 'Treatment added successfully');
                $this->redirect('/treatments/' . $treatmentId);
            } else {
                $this->setFlash('error', 'Failed to add treatment');
                $this->setData('old', $treatmentData);
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->setData('veterinaries', $this->userModel->getUsersByRole(ROLE_VETERINARY));
                $this->create();
            }
            
        } catch (Exception $e) {
            logError("Treatment creation error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while adding treatment');
            $this->create();
        }
    }
    
    // Show treatment details
    public function show($id) {
        requireLogin();
        
        $treatment = $this->treatmentModel->getTreatmentWithDetails($id);
        
        if (!$treatment) {
            $this->setFlash('error', 'Treatment not found');
            $this->redirect('/treatments');
            return;
        }
        
        $this->setTitle('Treatment: ' . $treatment['diagnosis']);
        $this->setData('treatment', $treatment);
        $this->view('treatments/show');
    }
    
    // Show edit treatment form
    public function edit($id) {
        requireLogin();
        
        $treatment = $this->treatmentModel->find($id);
        
        if (!$treatment) {
            $this->setFlash('error', 'Treatment not found');
            $this->redirect('/treatments');
            return;
        }
        
        $animals = $this->animalModel->getActiveAnimals();
        $veterinaries = $this->userModel->getUsersByRole(ROLE_VETERINARY);
        
        $this->setTitle('Edit Treatment');
        $this->setData('treatment', $treatment);
        $this->setData('animals', $animals);
        $this->setData('veterinaries', $veterinaries);
        $this->view('treatments/edit');
    }
    
    // Update treatment
    public function update($id) {
        requireLogin();
        
        if (!$this->isPost()) {
            $this->redirect('/treatments/' . $id . '/edit');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $treatmentData = $this->input();
            $errors = $this->treatmentModel->validate($treatmentData, $id);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('treatment', array_merge(['treatment_id' => $id], $treatmentData));
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->setData('veterinaries', $this->userModel->getUsersByRole(ROLE_VETERINARY));
                $this->edit($id);
                return;
            }
            
            $updated = $this->treatmentModel->update($id, $treatmentData);
            
            if ($updated) {
                $this->setFlash('success', 'Treatment updated successfully');
                $this->redirect('/treatments/' . $id);
            } else {
                $this->setFlash('error', 'Failed to update treatment');
                $this->setData('treatment', array_merge(['treatment_id' => $id], $treatmentData));
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->setData('veterinaries', $this->userModel->getUsersByRole(ROLE_VETERINARY));
                $this->edit($id);
            }
            
        } catch (Exception $e) {
            logError("Treatment update error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while updating treatment');
            $this->edit($id);
        }
    }
    
    // Mark treatment as completed
    public function complete($id) {
        requireLogin();
        
        if (!$this->isPost()) {
            $this->redirect('/treatments/' . $id);
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $completed = $this->treatmentModel->markAsCompleted($id);
            
            if ($completed) {
                $this->setFlash('success', 'Treatment marked as completed');
            } else {
                $this->setFlash('error', 'Failed to complete treatment');
            }
            
            $this->redirect('/treatments/' . $id);
            
        } catch (Exception $e) {
            logError("Treatment completion error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while completing treatment');
            $this->redirect('/treatments/' . $id);
        }
    }
    
    // Get treatments by animal (AJAX)
    public function byAnimal($animalId) {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $treatments = $this->treatmentModel->getTreatmentsByAnimal($animalId);
        $this->json($treatments);
    }
    
    // Get upcoming follow-ups
    public function followUps() {
        requireLogin();
        
        $veterinaryId = $this->get('veterinary_id');
        $upcomingFollowUps = $this->treatmentModel->getUpcomingFollowUps($veterinaryId);
        $overdueFollowUps = $this->treatmentModel->getOverdueFollowUps($veterinaryId);
        
        $this->setTitle('Treatment Follow-ups');
        $this->setData('upcomingFollowUps', $upcomingFollowUps);
        $this->setData('overdueFollowUps', $overdueFollowUps);
        $this->view('treatments/follow-ups');
    }
    
    // AJAX treatment search
    public function search() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $term = $this->get('term');
        $treatments = [];
        
        if (!empty($term)) {
            $treatments = $this->treatmentModel->searchTreatments($term);
        }
        
        $this->json($treatments);
    }
    
    // Get treatment statistics (AJAX)
    public function stats() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $stats = $this->treatmentModel->getStats();
        $this->json($stats);
    }
}
?>