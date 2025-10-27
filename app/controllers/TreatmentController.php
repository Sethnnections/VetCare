<?php
class TreatmentController extends Controller {
    private $treatmentModel;
    private $animalModel;
    private $userModel;
    private $clientModel;
    
    public function __construct() {
        $this->treatmentModel = new Treatment();
        $this->animalModel = new Animal();
        $this->userModel = new User();
        $this->clientModel = new Client();
    }
    
    // List all treatments with role-based filtering
    public function index() {
        requireLogin();
        
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['user_id'];
        $page = $this->get('page', 1);
        $search = $this->get('search');
        $status = $this->get('status');
        
        // Role-based data filtering
        switch ($userRole) {
            case ROLE_ADMIN:
                $treatments = $this->treatmentModel->getAllTreatmentsWithDetails($page, 15, $search, $status);
                break;
                
            case ROLE_VETERINARY:
                $treatments = $this->treatmentModel->getTreatmentsByVeterinary($userId, $page, 15, $search, $status);
                break;
                
            case ROLE_CLIENT:
                $clientId = $this->clientModel->getClientIdByUserId($userId);
                if ($clientId) {
                    $treatments = $this->treatmentModel->getTreatmentsByClient($clientId, $page, 15, $search, $status);
                } else {
                    $treatments = ['data' => [], 'total' => 0, 'total_pages' => 0];
                }
                break;
                
            default:
                $treatments = ['data' => [], 'total' => 0, 'total_pages' => 0];
        }
        
        $this->setTitle('Treatments');
        $this->setData('treatments', $treatments['data']);
        $this->setData('pagination', [
            'current_page' => $page,
            'total_pages' => $treatments['total_pages'],
            'total' => $treatments['total']
        ]);
        $this->setData('search', $search);
        $this->setData('status', $status);
        $this->setData('stats', $this->treatmentModel->getTreatmentStats($userRole, $userId));
        
        // Load appropriate view based on role
        $viewPath = $userRole . '/treatments/index';
        $this->view($viewPath, 'dashboard');
    }
    
    // Show create treatment form
    public function getAnimalData($animalId) {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $animalModel = new Animal();
        $animal = $animalModel->getAnimalData($animalId);
        
        if ($animal) {
            $this->json(['success' => true, 'animal' => $animal]);
        } else {
            $this->json(['success' => false, 'error' => 'Animal not found']);
        }
    }

    // Update the create method to include animals based on role
    public function create() {
        requireLogin();
        $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
        
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['user_id'];
        
        // Get animals based on role
        $animals = $this->treatmentModel->getAnimalsForTreatment($userRole, $userId);
        $veterinarians = $this->userModel->getUsersByRole(ROLE_VETERINARY);
        
        $this->setTitle('Add New Treatment');
        $this->setData('animals', $animals);
        $this->setData('veterinarians', $veterinarians);
        $this->view('treatments/create', 'dashboard');
    }
    // Store new treatment
    public function store() {
        requireLogin();
        $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
        
        if (!$this->isPost()) {
            $this->redirect('/treatments/create');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $treatmentData = $this->input();
            $treatmentData['veterinary_id'] = $_SESSION['role'] === ROLE_VETERINARY ? $_SESSION['user_id'] : $treatmentData['veterinary_id'];
            
            $errors = $this->treatmentModel->validate($treatmentData);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('old', $treatmentData);
                $this->create();
                return;
            }
            
            $treatmentId = $this->treatmentModel->createTreatment($treatmentData);
            
            if ($treatmentId) {
                logActivity("Treatment created: ID {$treatmentId} for animal {$treatmentData['animal_id']}", $_SESSION['user_id']);
                $this->setFlash('success', 'Treatment added successfully');
                $this->redirect('/treatments/' . $treatmentId);
            } else {
                $this->setFlash('error', 'Failed to add treatment');
                $this->setData('old', $treatmentData);
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
        
        // Check permissions
        if (!$this->canViewTreatment($treatment)) {
            $this->setFlash('error', 'Access denied');
            $this->redirect('/treatments');
            return;
        }
        
        $this->setTitle('Treatment Details');
        $this->setData('treatment', $treatment);
        
        // Load appropriate view based on role
        $userRole = $_SESSION['role'];
        $viewPath = $userRole . '/treatments/show';
        $this->view($viewPath, 'dashboard');
    }
  
    // Update treatment
    public function update($id) {
        requireLogin();
        $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
        
        if (!$this->isPost()) {
            $this->redirect('/treatments/' . $id . '/edit');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $treatment = $this->treatmentModel->find($id);
            if (!$treatment) {
                $this->setFlash('error', 'Treatment not found');
                $this->redirect('/treatments');
                return;
            }
            
            // Check if veterinary can update this treatment
            if ($_SESSION['role'] === ROLE_VETERINARY && $treatment['veterinary_id'] != $_SESSION['user_id']) {
                $this->setFlash('error', 'You can only update your own treatments');
                $this->redirect('/treatments');
                return;
            }
            
            $treatmentData = $this->input();
            $errors = $this->treatmentModel->validate($treatmentData, $id);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('treatment', array_merge(['treatment_id' => $id], $treatmentData));
                $this->edit($id);
                return;
            }
            
            $updated = $this->treatmentModel->updateTreatment($id, $treatmentData);
            
            if ($updated) {
                logActivity("Treatment updated: ID {$id}", $_SESSION['user_id']);
                $this->setFlash('success', 'Treatment updated successfully');
                $this->redirect('/treatments/' . $id);
            } else {
                $this->setFlash('error', 'Failed to update treatment');
                $this->setData('treatment', array_merge(['treatment_id' => $id], $treatmentData));
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
        $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
        
        if (!$this->isPost()) {
            $this->redirect('/treatments/' . $id);
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $treatment = $this->treatmentModel->find($id);
            if (!$treatment) {
                $this->setFlash('error', 'Treatment not found');
                $this->redirect('/treatments');
                return;
            }
            
            // Check if veterinary can complete this treatment
            if ($_SESSION['role'] === ROLE_VETERINARY && $treatment['veterinary_id'] != $_SESSION['user_id']) {
                $this->setFlash('error', 'You can only complete your own treatments');
                $this->redirect('/treatments');
                return;
            }
            
            $completed = $this->treatmentModel->markAsCompleted($id);
            
            if ($completed) {
                logActivity("Treatment completed: ID {$id}", $_SESSION['user_id']);
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
    
    // Schedule follow-up
    public function scheduleFollowUp($id) {
        requireLogin();
        $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
        
        if (!$this->isPost()) {
            $this->redirect('/treatments/' . $id);
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $followUpDate = $this->input('follow_up_date');
            $notes = $this->input('notes');
            
            if (empty($followUpDate)) {
                $this->setFlash('error', 'Follow-up date is required');
                $this->redirect('/treatments/' . $id);
                return;
            }
            
            $scheduled = $this->treatmentModel->scheduleFollowUp($id, $followUpDate, $notes);
            
            if ($scheduled) {
                logActivity("Follow-up scheduled for treatment: ID {$id}", $_SESSION['user_id']);
                $this->setFlash('success', 'Follow-up scheduled successfully');
            } else {
                $this->setFlash('error', 'Failed to schedule follow-up');
            }
            
            $this->redirect('/treatments/' . $id);
            
        } catch (Exception $e) {
            logError("Follow-up scheduling error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while scheduling follow-up');
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
        $this->json(['success' => true, 'treatments' => $treatments]);
    }
    
    // Get upcoming follow-ups
    public function followUps() {
        requireLogin();
        $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
        
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['user_id'];
        
        $veterinaryId = $userRole === ROLE_VETERINARY ? $userId : null;
        $upcomingFollowUps = $this->treatmentModel->getUpcomingFollowUps($veterinaryId);
        $overdueFollowUps = $this->treatmentModel->getOverdueFollowUps($veterinaryId);
        
        $this->setTitle('Treatment Follow-ups');
        $this->setData('upcomingFollowUps', $upcomingFollowUps);
        $this->setData('overdueFollowUps', $overdueFollowUps);
        
        $viewPath = $userRole . '/treatments/follow-ups';
        $this->view($viewPath, 'dashboard');
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
        
        $this->json(['success' => true, 'treatments' => $treatments]);
    }
    
    // Get treatment statistics (AJAX)
    public function stats() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['user_id'];
        
        $stats = $this->treatmentModel->getTreatmentStats($userRole, $userId);
        $this->json(['success' => true, 'stats' => $stats]);
    }
    
    // Permission checks
    private function canViewTreatment($treatment) {
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['user_id'];
        
        switch ($userRole) {
            case ROLE_ADMIN:
                return true;
                
            case ROLE_VETERINARY:
                return $treatment['veterinary_id'] == $userId;
                
            case ROLE_CLIENT:
                $clientId = $this->clientModel->getClientIdByUserId($userId);
                return $clientId && $treatment['client_id'] == $clientId;
                
            default:
                return false;
        }
    }

 public function edit($id) {
    requireLogin();
    $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
    
    try {
        $treatment = $this->treatmentModel->find($id);
        
        if (!$treatment) {
            $this->setFlash('error', 'Treatment not found');
            $this->redirect('/treatments');
            return;
        }
        
        // Check if veterinary can edit this treatment
        if ($_SESSION['role'] === ROLE_VETERINARY && $treatment['veterinary_id'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'You can only edit your own treatments');
            $this->redirect('/treatments');
            return;
        }
        
        $animals = $this->animalModel->getActiveAnimals();
        $veterinarians = $this->userModel->getUsersByRole(ROLE_VETERINARY);
        
        $this->setTitle('Edit Treatment');
        $this->setData('treatment', $treatment);
        $this->setData('animals', $animals);
        $this->setData('veterinarians', $veterinarians);
        $this->view('treatments/edit', 'dashboard');
        
    } catch (Exception $e) {
        logError("Treatment edit error: " . $e->getMessage());
        $this->setFlash('error', 'An error occurred while loading the treatment form');
        $this->redirect('/treatments');
    }
}
}
?>