<?php
class AnimalController extends Controller {
    private $animalModel;
    private $clientModel;
    private $treatmentModel;
    private $vaccineModel;
    
    public function __construct() {
        $this->animalModel = new Animal();
        $this->clientModel = new Client();
        $this->treatmentModel = new Treatment();
        $this->vaccineModel = new Vaccine();
    }
    
    // List all animals
    public function index() {
        requireLogin();
        
        $page = $this->get('page', 1);
        $search = $this->get('search');
        $species = $this->get('species');
        
        if ($search) {
            $animals = $this->animalModel->searchAnimals($search);
        } else {
            $animals = $this->paginate($this->animalModel, $page);
        }
        
        // Filter by species if specified
        if ($species) {
            $animals = array_filter($animals, function($animal) use ($species) {
                return strtolower($animal['species']) === strtolower($species);
            });
        }
        
        $this->setTitle('Animals');
        $this->setData('animals', $animals);
        $this->setData('search', $search);
        $this->setData('species', $species);
        $this->setData('stats', $this->animalModel->getStats());
        $this->view('animals/index');
    }
    
    // Show create animal form
    public function create() {
        requireLogin();
        
        $clients = $this->clientModel->getActiveClients();
        
        $this->setTitle('Add New Animal');
        $this->setData('clients', $clients);
        $this->view('animals/create');
    }
    
    // Store new animal
    public function store() {
        requireLogin();
        
        if (!$this->isPost()) {
            $this->redirect('/animals/create');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $animalData = $this->input();
            $errors = $this->animalModel->validate($animalData);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('old', $animalData);
                $this->setData('clients', $this->clientModel->getActiveClients());
                $this->create();
                return;
            }
            
            $animalId = $this->animalModel->create($animalData);
            
            if ($animalId) {
                $this->setFlash('success', 'Animal added successfully');
                $this->redirect('/animals/' . $animalId);
            } else {
                $this->setFlash('error', 'Failed to add animal');
                $this->setData('old', $animalData);
                $this->setData('clients', $this->clientModel->getActiveClients());
                $this->create();
            }
            
        } catch (Exception $e) {
            logError("Animal creation error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while adding animal');
            $this->create();
        }
    }
    
    // Show animal details
    public function show($id) {
        requireLogin();
        
        $animal = $this->animalModel->getAnimalWithClient($id);
        
        if (!$animal) {
            $this->setFlash('error', 'Animal not found');
            $this->redirect('/animals');
            return;
        }
        
        $treatments = $this->treatmentModel->getTreatmentsByAnimal($id);
        $vaccines = $this->vaccineModel->getVaccinesByAnimal($id);
        $lastTreatment = $this->animalModel->getLastTreatment($id);
        $nextVaccination = $this->animalModel->getNextVaccination($id);
        
        $this->setTitle('Animal: ' . $animal['name']);
        $this->setData('animal', $animal);
        $this->setData('treatments', $treatments);
        $this->setData('vaccines', $vaccines);
        $this->setData('lastTreatment', $lastTreatment);
        $this->setData('nextVaccination', $nextVaccination);
        $this->view('animals/show');
    }
    
    // Show edit animal form
    public function edit($id) {
        requireLogin();
        
        $animal = $this->animalModel->find($id);
        
        if (!$animal) {
            $this->setFlash('error', 'Animal not found');
            $this->redirect('/animals');
            return;
        }
        
        $clients = $this->clientModel->getActiveClients();
        
        $this->setTitle('Edit Animal: ' . $animal['name']);
        $this->setData('animal', $animal);
        $this->setData('clients', $clients);
        $this->view('animals/edit');
    }
    
    // Update animal
    public function update($id) {
        requireLogin();
        
        if (!$this->isPost()) {
            $this->redirect('/animals/' . $id . '/edit');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $animalData = $this->input();
            $errors = $this->animalModel->validate($animalData, $id);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('animal', array_merge(['animal_id' => $id], $animalData));
                $this->setData('clients', $this->clientModel->getActiveClients());
                $this->edit($id);
                return;
            }
            
            $updated = $this->animalModel->update($id, $animalData);
            
            if ($updated) {
                $this->setFlash('success', 'Animal updated successfully');
                $this->redirect('/animals/' . $id);
            } else {
                $this->setFlash('error', 'Failed to update animal');
                $this->setData('animal', array_merge(['animal_id' => $id], $animalData));
                $this->setData('clients', $this->clientModel->getActiveClients());
                $this->edit($id);
            }
            
        } catch (Exception $e) {
            logError("Animal update error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while updating animal');
            $this->edit($id);
        }
    }
    
    // Delete animal (soft delete)
    public function delete($id) {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        if (!$this->isPost()) {
            $this->redirect('/animals');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $animal = $this->animalModel->find($id);
            
            if (!$animal) {
                $this->setFlash('error', 'Animal not found');
                $this->redirect('/animals');
                return;
            }
            
            // Check if animal has active treatments
            $treatments = $this->treatmentModel->getTreatmentsByAnimal($id);
            $activeTreatments = array_filter($treatments, function($treatment) {
                return $treatment['status'] != STATUS_COMPLETED;
            });
            
            if (!empty($activeTreatments)) {
                $this->setFlash('error', 'Cannot delete animal with active treatments. Please complete or cancel treatments first.');
                $this->redirect('/animals/' . $id);
                return;
            }
            
            $deleted = $this->animalModel->update($id, ['status' => STATUS_INACTIVE]);
            
            if ($deleted) {
                $this->setFlash('success', 'Animal deactivated successfully');
            } else {
                $this->setFlash('error', 'Failed to deactivate animal');
            }
            
            $this->redirect('/animals');
            
        } catch (Exception $e) {
            logError("Animal deletion error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while deleting animal');
            $this->redirect('/animals');
        }
    }
    
    // Reactivate animal
    public function activate($id) {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        if (!$this->isPost()) {
            $this->redirect('/animals');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $activated = $this->animalModel->update($id, ['status' => STATUS_ACTIVE]);
            
            if ($activated) {
                $this->setFlash('success', 'Animal activated successfully');
            } else {
                $this->setFlash('error', 'Failed to activate animal');
            }
            
            $this->redirect('/animals');
            
        } catch (Exception $e) {
            logError("Animal activation error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while activating animal');
            $this->redirect('/animals');
        }
    }
    
    // Get animals by client (AJAX)
    public function byClient($clientId) {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $animals = $this->animalModel->getAnimalsByClient($clientId);
        $this->json($animals);
    }
    
    // AJAX animal search for autocomplete
    public function search() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $term = $this->get('term');
        $animals = [];
        
        if (!empty($term)) {
            $animals = $this->animalModel->searchAnimals($term);
        }
        
        $this->json($animals);
    }
    
    // Get animal medical history
    public function medicalHistory($id) {
        requireLogin();
        
        $animal = $this->animalModel->getAnimalWithClient($id);
        
        if (!$animal) {
            $this->setFlash('error', 'Animal not found');
            $this->redirect('/animals');
            return;
        }
        
        $treatments = $this->treatmentModel->getTreatmentHistory($id, 50);
        $vaccines = $this->vaccineModel->getVaccinationHistory($id, 50);
        
        $this->setTitle('Medical History: ' . $animal['name']);
        $this->setData('animal', $animal);
        $this->setData('treatments', $treatments);
        $this->setData('vaccines', $vaccines);
        $this->view('animals/medical-history');
    }

    // Client-specific animal methods
    public function clientIndex() {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        $userId = $_SESSION['user_id'];
        $clientId = $this->clientModel->getClientIdByUserId($userId);
        
        if (!$clientId) {
            $this->setFlash('error', 'Please complete your client profile first.');
            $this->redirect('/client/profile/create');
            return;
        }
        
        $animals = $this->animalModel->getAnimalsByClient($clientId);
        
        $this->setTitle('My Animals');
        $this->setData('animals', $animals);
        $this->setData('stats', [
            'total' => count($animals),
            'active' => count(array_filter($animals, function($animal) {
                return $animal['status'] == STATUS_ACTIVE;
            }))
        ]);
        $this->view('client/animals/index', 'dashboard');
    }

    public function clientCreate() {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        if ($this->isPost()) {
            $this->clientStore();
            return;
        }
        
        $this->setTitle('Add New Animal');
        $this->view('client/animals/create', 'dashboard');
    }

    public function clientStore() {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        if (!$this->isPost()) {
            $this->redirect('/client/animals/add');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $animalData = $this->input();
            $clientId = $this->clientModel->getClientIdByUserId(getCurrentUserId());
            
            if (!$clientId) {
                $this->setFlash('error', 'Client profile not found. Please complete your profile first.');
                $this->redirect('/client/profile/create');
                return;
            }
            
            $animalData['client_id'] = $clientId;
            $animalData['status'] = STATUS_ACTIVE;
            
            $errors = $this->animalModel->validate($animalData);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('old', $animalData);
                $this->clientCreate();
                return;
            }
            
            $animalId = $this->animalModel->create($animalData);
            
            if ($animalId) {
                logActivity("Animal created: {$animalData['name']} by client ID: {$clientId}");
                $this->setFlash('success', 'Animal added successfully');
                $this->redirect('/client/animals/' . $animalId);
            } else {
                $this->setFlash('error', 'Failed to add animal');
                $this->setData('old', $animalData);
                $this->clientCreate();
            }
            
        } catch (Exception $e) {
            logError("Animal creation error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while adding animal');
            $this->clientCreate();
        }
    }

    public function clientShow($id) {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        $userId = $_SESSION['user_id'];
        $clientId = $this->clientModel->getClientIdByUserId($userId);
        $animal = $this->animalModel->find($id);
        
        // Check if animal belongs to client
        if (!$animal || $animal['client_id'] != $clientId) {
            $this->setFlash('error', 'Animal not found or access denied');
            $this->redirect('/client/animals');
            return;
        }
        
        $treatments = $this->animalModel->getAnimalTreatments($id);
        $vaccines = $this->animalModel->getAnimalVaccinations($id);
        $lastTreatment = $this->animalModel->getLastTreatment($id);
        $nextVaccination = $this->animalModel->getNextVaccination($id);
        
        $this->setTitle('Animal: ' . $animal['name']);
        $this->setData('animal', $animal);
        $this->setData('treatments', $treatments);
        $this->setData('vaccines', $vaccines);
        $this->setData('lastTreatment', $lastTreatment);
        $this->setData('nextVaccination', $nextVaccination);
        $this->view('client/animals/show', 'dashboard');
    }

    public function clientEdit($id) {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        $userId = $_SESSION['user_id'];
        $clientId = $this->clientModel->getClientIdByUserId($userId);
        $animal = $this->animalModel->find($id);
        
        // Check if animal belongs to client
        if (!$animal || $animal['client_id'] != $clientId) {
            $this->setFlash('error', 'Animal not found or access denied');
            $this->redirect('/client/animals');
            return;
        }
        
        $this->setTitle('Edit Animal: ' . $animal['name']);
        $this->setData('animal', $animal);
        $this->view('client/animals/edit', 'dashboard');
    }

    public function clientUpdate($id) {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        if (!$this->isPost()) {
            $this->redirect('/client/animals/' . $id . '/edit');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $clientId = $this->clientModel->getClientIdByUserId(getCurrentUserId());
            $animal = $this->animalModel->find($id);
            
            // Check if animal belongs to client
            if (!$animal || $animal['client_id'] != $clientId) {
                $this->setFlash('error', 'Animal not found or access denied');
                $this->redirect('/client/animals');
                return;
            }
            
            $animalData = $this->input();
            $errors = $this->animalModel->validate($animalData, $id);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('animal', array_merge(['animal_id' => $id], $animalData));
                $this->clientEdit($id);
                return;
            }
            
            $updated = $this->animalModel->update($id, $animalData);
            
            if ($updated) {
                logActivity("Animal updated: {$animalData['name']} by client ID: {$clientId}");
                $this->setFlash('success', 'Animal updated successfully');
                $this->redirect('/client/animals/' . $id);
            } else {
                $this->setFlash('error', 'Failed to update animal');
                $this->setData('animal', array_merge(['animal_id' => $id], $animalData));
                $this->clientEdit($id);
            }
            
        } catch (Exception $e) {
            logError("Animal update error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while updating animal');
            $this->clientEdit($id);
        }
    }

    public function clientDelete($id) {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        if (!$this->isPost()) {
            $this->redirect('/client/animals');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $clientId = $this->clientModel->getClientIdByUserId(getCurrentUserId());
            $animal = $this->animalModel->find($id);
            
            // Check if animal belongs to client
            if (!$animal || $animal['client_id'] != $clientId) {
                $this->setFlash('error', 'Animal not found or access denied');
                $this->redirect('/client/animals');
                return;
            }
            
            // Check if animal has active treatments
            $treatments = $this->animalModel->getAnimalTreatments($id);
            $activeTreatments = array_filter($treatments, function($treatment) {
                return $treatment['status'] != STATUS_COMPLETED;
            });
            
            if (!empty($activeTreatments)) {
                $this->setFlash('error', 'Cannot delete animal with active treatments. Please contact the veterinary staff.');
                $this->redirect('/client/animals/' . $id);
                return;
            }
            
            // Soft delete by setting status to inactive
            $deleted = $this->animalModel->update($id, ['status' => STATUS_INACTIVE]);
            
            if ($deleted) {
                logActivity("Animal deleted: {$animal['name']} by client ID: {$clientId}");
                $this->setFlash('success', 'Animal deleted successfully');
            } else {
                $this->setFlash('error', 'Failed to delete animal');
            }
            
            $this->redirect('/client/animals');
            
        } catch (Exception $e) {
            logError("Animal deletion error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while deleting animal');
            $this->redirect('/client/animals');
        }
    }

    public function clientMedicalHistory($id) {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        $clientId = $this->clientModel->getClientIdByUserId(getCurrentUserId());
        $animal = $this->animalModel->find($id);
        
        // Check if animal belongs to client
        if (!$animal || $animal['client_id'] != $clientId) {
            $this->setFlash('error', 'Animal not found or access denied');
            $this->redirect('/client/animals');
            return;
        }
        
        $treatments = $this->animalModel->getAnimalTreatments($id);
        $vaccines = $this->animalModel->getAnimalVaccinations($id);
        
        $this->setTitle('Medical History: ' . $animal['name']);
        $this->setData('animal', $animal);
        $this->setData('treatments', $treatments);
        $this->setData('vaccines', $vaccines);
        $this->view('client/animals/medical-history', 'dashboard');
    }

    // AJAX method to get client's animals
    public function clientAnimalsAjax() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        $clientId = $this->clientModel->getClientIdByUserId(getCurrentUserId());
        $animals = $this->animalModel->getAnimalsByClient($clientId);
        
        $this->json(['success' => true, 'animals' => $animals]);
    }
    
    // Get animal statistics (AJAX)
    public function stats() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $stats = $this->animalModel->getStats();
        $this->json($stats);
    }


    // Veterinary-specific animal method
    // Veterinary-specific animal methods - SEE ONLY ASSIGNED ANIMALS
    public function veterinaryIndex() {
        requireLogin();
        $this->authorize([ROLE_VETERINARY]);
        
        $veterinaryId = $_SESSION['user_id'];
        $search = $this->get('search');
        
        // Get only animals assigned to this veterinary
        $assignedAnimals = $this->animalModel->getAnimalsByVeterinary($veterinaryId);
        
        // Apply search filter if provided
        if ($search && !empty($assignedAnimals)) {
            $assignedAnimals = array_filter($assignedAnimals, function($animal) use ($search) {
                return stripos($animal['name'], $search) !== false ||
                    stripos($animal['species'], $search) !== false ||
                    stripos($animal['breed'] ?? '', $search) !== false ||
                    stripos($animal['client_first_name'] . ' ' . $animal['client_last_name'], $search) !== false;
            });
        }
        
        $this->setTitle('My Assigned Animals');
        $this->setData('animals', $assignedAnimals);
        $this->setData('search', $search);
        $this->setData('stats', [
            'total' => count($assignedAnimals),
            'active' => count($assignedAnimals) // All assigned are active
        ]);
        $this->view('veterinary/animals/index', 'dashboard');
    }

    public function veterinaryShow($id) {
        requireLogin();
        $this->authorize([ROLE_VETERINARY]);
        
        $veterinaryId = $_SESSION['user_id'];
        $animal = $this->animalModel->find($id);
        
        // Check if animal is assigned to this veterinary
        if (!$animal || !$this->animalModel->isAssignedToVeterinary($id, $veterinaryId)) {
            $this->setFlash('error', 'Animal not found or not assigned to you');
            $this->redirect('/veterinary/animals');
            return;
        }
        
        $treatments = $this->animalModel->getAnimalTreatments($id);
        $vaccines = $this->animalModel->getAnimalVaccinations($id);
        $lastTreatment = $this->animalModel->getLastTreatment($id);
        $nextVaccination = $this->animalModel->getNextVaccination($id);
        $billings = $this->animalModel->getAnimalBillings($id);
        $reminders = $this->animalModel->getAnimalReminders($id);
        
        // Get client details
        $client = $this->clientModel->find($animal['client_id']);
        
        $this->setTitle('Animal: ' . $animal['name']);
        $this->setData('animal', $animal);
        $this->setData('client', $client);
        $this->setData('treatments', $treatments);
        $this->setData('vaccines', $vaccines);
        $this->setData('lastTreatment', $lastTreatment);
        $this->setData('nextVaccination', $nextVaccination);
        $this->setData('billings', $billings);
        $this->setData('reminders', $reminders);
        $this->view('veterinary/animals/show', 'dashboard');
    }

    public function veterinaryEdit($id) {
        requireLogin();
        $this->authorize([ROLE_VETERINARY]);
        
        $veterinaryId = $_SESSION['user_id'];
        $animal = $this->animalModel->find($id);
        
        // Check if animal is assigned to this veterinary
        if (!$animal || !$this->animalModel->isAssignedToVeterinary($id, $veterinaryId)) {
            $this->setFlash('error', 'Animal not found or not assigned to you');
            $this->redirect('/veterinary/animals');
            return;
        }
        
        $this->setTitle('Edit Animal: ' . $animal['name']);
        $this->setData('animal', $animal);
        $this->view('veterinary/animals/edit', 'dashboard');
    }

    public function veterinaryUpdate($id) {
        requireLogin();
        $this->authorize([ROLE_VETERINARY]);
        
        if (!$this->isPost()) {
            $this->redirect('/veterinary/animals/' . $id . '/edit');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $veterinaryId = $_SESSION['user_id'];
            $animal = $this->animalModel->find($id);
            
            // Check if animal is assigned to this veterinary
            if (!$animal || !$this->animalModel->isAssignedToVeterinary($id, $veterinaryId)) {
                $this->setFlash('error', 'Animal not found or not assigned to you');
                $this->redirect('/veterinary/animals');
                return;
            }
            
            $animalData = $this->input();
            $errors = $this->animalModel->validate($animalData, $id);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('animal', array_merge(['animal_id' => $id], $animalData));
                $this->veterinaryEdit($id);
                return;
            }
            
            $updated = $this->animalModel->update($id, $animalData);
            
            if ($updated) {
                logActivity("Animal updated by veterinary {$veterinaryId}: {$animalData['name']} (ID: {$id})");
                $this->setFlash('success', 'Animal updated successfully');
                $this->redirect('/veterinary/animals/' . $id);
            } else {
                $this->setFlash('error', 'Failed to update animal');
                $this->setData('animal', array_merge(['animal_id' => $id], $animalData));
                $this->veterinaryEdit($id);
            }
            
        } catch (Exception $e) {
            logError("Animal update error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while updating animal');
            $this->veterinaryEdit($id);
        }
    }

    // Admin method to assign animals to veterinary
    public function assignToVeterinary() {
        requireLogin();
        $this->authorize([ROLE_ADMIN]);
        
        if ($this->isPost()) {
            try {
                $this->validateCsrf();
                
                $animalId = $this->input('animal_id');
                $veterinaryId = $this->input('veterinary_id');
                $assignedBy = $_SESSION['user_id'];
                
                $assigned = $this->animalModel->assignToVeterinary($animalId, $veterinaryId, $assignedBy);
                
                if ($assigned) {
                    logActivity("Animal {$animalId} assigned to veterinary {$veterinaryId} by admin {$assignedBy}");
                    $this->setFlash('success', 'Animal assigned to veterinary successfully');
                } else {
                    $this->setFlash('error', 'Failed to assign animal to veterinary');
                }
                
                $this->redirect('/animals/' . $animalId);
                
            } catch (Exception $e) {
                logError("Animal assignment error: " . $e->getMessage());
                $this->setFlash('error', 'An error occurred while assigning animal');
                $this->redirect('/animals');
            }
        }
    }


    public function veterinaryMedicalHistory($id) {
        requireLogin();
        $this->authorize([ROLE_VETERINARY, ROLE_ADMIN]);
        
        $animal = $this->animalModel->getAnimalWithClient($id);
        
        if (!$animal) {
            $this->setFlash('error', 'Animal not found');
            $this->redirect('/veterinary/animals');
            return;
        }
        
        $treatments = $this->animalModel->getAnimalTreatments($id);
        $vaccines = $this->animalModel->getAnimalVaccinations($id);
        
        $this->setTitle('Medical History: ' . $animal['name']);
        $this->setData('animal', $animal);
        $this->setData('treatments', $treatments);
        $this->setData('vaccines', $vaccines);
        $this->view('veterinary/animals/medical-history', 'dashboard');
    }

    // Veterinary dashboard with assigned animals
    public function veterinaryAssignedAnimals() {
        requireLogin();
        $this->authorize([ROLE_VETERINARY]);
        
        $veterinaryId = $_SESSION['user_id'];
        
        // Get animals assigned to this veterinary (you'll need to implement this method)
        $assignedAnimals = $this->animalModel->getAnimalsByVeterinary($veterinaryId);
        $recentTreatments = $this->treatmentModel->getRecentTreatmentsByVeterinary($veterinaryId, 10);
        
        $this->setTitle('My Assigned Animals');
        $this->setData('animals', $assignedAnimals);
        $this->setData('recentTreatments', $recentTreatments);
        $this->view('veterinary/animals/assigned', 'dashboard');
    }
}
?>