<?php
class ClientProfileController extends Controller {
    private $clientModel;
    private $animalModel;
    private $userModel;
    
    public function __construct() {
        $this->clientModel = new Client();
        $this->animalModel = new Animal();
        $this->userModel = new User();
    }
    
    // Show client profile
    public function profile() {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        $userId = $_SESSION['user_id'];
        $client = $this->clientModel->getClientByUserId($userId);
        
        if (!$client) {
            $this->setFlash('error', 'Client profile not found');
            $this->redirect('/dashboard');
            return;
        }
        
        $this->setTitle('My Profile');
        $this->setData('client', $client);
        $this->view('client/profile');
    }
    
    // Update client profile
    public function updateProfile() {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        if (!$this->isPost()) {
            $this->redirect('/client/profile');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $profileData = $this->input();
            $userId = $_SESSION['user_id'];
            $client = $this->clientModel->getClientByUserId($userId);
            
            if (!$client) {
                $this->setFlash('error', 'Client profile not found');
                $this->redirect('/client/profile');
                return;
            }
            
            // Validate required fields
            $errors = [];
            if (empty($profileData['first_name'])) {
                $errors['first_name'] = 'First name is required';
            }
            if (empty($profileData['last_name'])) {
                $errors['last_name'] = 'Last name is required';
            }
            if (empty($profileData['phone'])) {
                $errors['phone'] = 'Phone number is required';
            }
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('client', array_merge($client, $profileData));
                $this->profile();
                return;
            }
            
            $updated = $this->clientModel->updateClientProfile($client['client_id'], $profileData);
            
            if ($updated) {
                $this->setFlash('success', 'Profile updated successfully');
            } else {
                $this->setFlash('error', 'Failed to update profile');
            }
            
            $this->redirect('/client/profile');
            
        } catch (Exception $e) {
            logError("Profile update error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while updating profile');
            $this->redirect('/client/profile');
        }
    }
    
    // Show client's animals
    public function animals() {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        $userId = $_SESSION['user_id'];
        $client = $this->clientModel->getClientByUserId($userId);
        
        if (!$client) {
            $this->setFlash('error', 'Client profile not found');
            $this->redirect('/dashboard');
            return;
        }
        
        $animals = $this->clientModel->getClientAnimals($client['client_id']);
        
        $this->setTitle('My Animals');
        $this->setData('animals', $animals);
        $this->setData('client', $client);
        $this->view('client/animals');
    }
    
    // Add new animal (AJAX modal)
    public function addAnimal() {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        if (!$this->isPost()) {
            $this->json(['error' => 'Invalid request method'], 400);
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $animalData = $this->input();
            $userId = $_SESSION['user_id'];
            $client = $this->clientModel->getClientByUserId($userId);
            
            if (!$client) {
                $this->json(['error' => 'Client profile not found'], 400);
                return;
            }
            
            $animalData['client_id'] = $client['client_id'];
            $animalData['status'] = 'active';
            
            // Validate animal data
            $errors = $this->validateAnimalData($animalData);
            
            if (!empty($errors)) {
                $this->json(['success' => false, 'errors' => $errors]);
                return;
            }
            
            $animalId = $this->animalModel->create($animalData);
            
            if ($animalId) {
                $this->json([
                    'success' => true, 
                    'message' => 'Animal added successfully',
                    'animal_id' => $animalId
                ]);
            } else {
                $this->json(['success' => false, 'error' => 'Failed to add animal']);
            }
            
        } catch (Exception $e) {
            logError("Animal creation error: " . $e->getMessage());
            $this->json(['success' => false, 'error' => 'An error occurred while adding animal']);
        }
    }
    
    // Edit animal (AJAX modal)
    public function editAnimal($animalId) {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        if ($this->isPost()) {
            $this->updateAnimal($animalId);
            return;
        }
        
        // GET request - return animal data
        $animal = $this->animalModel->find($animalId);
        $userId = $_SESSION['user_id'];
        $client = $this->clientModel->getClientByUserId($userId);
        
        if (!$animal || $animal['client_id'] != $client['client_id']) {
            $this->json(['error' => 'Animal not found or access denied'], 404);
            return;
        }
        
        $this->json(['success' => true, 'animal' => $animal]);
    }
    
    // Update animal
    private function updateAnimal($animalId) {
        try {
            $this->validateCsrf();
            
            $animalData = $this->input();
            $userId = $_SESSION['user_id'];
            $client = $this->clientModel->getClientByUserId($userId);
            $animal = $this->animalModel->find($animalId);
            
            if (!$animal || $animal['client_id'] != $client['client_id']) {
                $this->json(['success' => false, 'error' => 'Animal not found or access denied']);
                return;
            }
            
            // Validate animal data
            $errors = $this->validateAnimalData($animalData, $animalId);
            
            if (!empty($errors)) {
                $this->json(['success' => false, 'errors' => $errors]);
                return;
            }
            
            $updated = $this->animalModel->update($animalId, $animalData);
            
            if ($updated) {
                $this->json([
                    'success' => true, 
                    'message' => 'Animal updated successfully'
                ]);
            } else {
                $this->json(['success' => false, 'error' => 'Failed to update animal']);
            }
            
        } catch (Exception $e) {
            logError("Animal update error: " . $e->getMessage());
            $this->json(['success' => false, 'error' => 'An error occurred while updating animal']);
        }
    }
    
    // Delete animal
    public function deleteAnimal($animalId) {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        if (!$this->isPost()) {
            $this->json(['error' => 'Invalid request method'], 400);
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $userId = $_SESSION['user_id'];
            $client = $this->clientModel->getClientByUserId($userId);
            $animal = $this->animalModel->find($animalId);
            
            if (!$animal || $animal['client_id'] != $client['client_id']) {
                $this->json(['success' => false, 'error' => 'Animal not found or access denied']);
                return;
            }
            
            // Soft delete by setting status to inactive
            $deleted = $this->animalModel->update($animalId, ['status' => 'inactive']);
            
            if ($deleted) {
                $this->json([
                    'success' => true, 
                    'message' => 'Animal deleted successfully'
                ]);
            } else {
                $this->json(['success' => false, 'error' => 'Failed to delete animal']);
            }
            
        } catch (Exception $e) {
            logError("Animal deletion error: " . $e->getMessage());
            $this->json(['success' => false, 'error' => 'An error occurred while deleting animal']);
        }
    }
    
    // Validate animal data
    private function validateAnimalData($data, $animalId = null) {
        $errors = [];
        
        // Required fields
        if (empty($data['name'])) {
            $errors['name'] = 'Animal name is required';
        }
        if (empty($data['species'])) {
            $errors['species'] = 'Species is required';
        }
        
        // Validate gender
        if (!empty($data['gender']) && !in_array($data['gender'], ['male', 'female', 'unknown'])) {
            $errors['gender'] = 'Invalid gender selected';
        }
        
        // Validate birth date
        if (!empty($data['birth_date'])) {
            $birthDate = strtotime($data['birth_date']);
            if (!$birthDate || $birthDate > time()) {
                $errors['birth_date'] = 'Invalid birth date';
            }
        }
        
        // Validate weight
        if (!empty($data['weight']) && (!is_numeric($data['weight']) || $data['weight'] <= 0)) {
            $errors['weight'] = 'Weight must be a positive number';
        }
        
        // Check unique microchip if provided
        if (!empty($data['microchip'])) {
            $existingAnimal = $this->animalModel->findBy('microchip', $data['microchip']);
            if ($existingAnimal && (!$animalId || $existingAnimal['animal_id'] != $animalId)) {
                $errors['microchip'] = 'Microchip number already exists';
            }
        }
        
        return $errors;
    }
    
    // Get animal details for view
    public function viewAnimal($animalId) {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        $userId = $_SESSION['user_id'];
        $client = $this->clientModel->getClientByUserId($userId);
        $animal = $this->animalModel->find($animalId);
        
        if (!$animal || $animal['client_id'] != $client['client_id']) {
            $this->setFlash('error', 'Animal not found or access denied');
            $this->redirect('/client/animals');
            return;
        }
        
        // Get animal treatments and vaccines
        $treatments = $this->animalModel->getAnimalTreatments($animalId);
        $vaccines = $this->animalModel->getAnimalVaccinations($animalId);
        
        $this->setTitle('Animal: ' . $animal['name']);
        $this->setData('animal', $animal);
        $this->setData('treatments', $treatments);
        $this->setData('vaccines', $vaccines);
        $this->view('client/animal-view');
    }
}
?>