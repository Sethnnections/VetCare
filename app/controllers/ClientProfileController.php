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
    
    $client = $this->checkProfile();
    if (!$client) return;
    
    $this->setTitle('My Profile');
    $this->setData('client', $client);
    $this->view('client/profile', 'dashboard'); // Add 'dashboard' as layout
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
    
    // Show client's animalspublic 
    // 
    function animals() {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        $client = $this->checkProfile();
        if (!$client) return;
        
        $animals = $this->clientModel->getClientAnimals($client['client_id']);
        
        $this->setTitle('My Animals');
        $this->setData('animals', $animals);
        $this->setData('client', $client);
        $this->view('client/animals', 'dashboard'); // Add 'dashboard' as layout
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
    // Add these methods to your existing ClientProfileController class

/**
 * Check if client profile exists, if not redirect to create profile
 */
public function checkProfile() {
    requireLogin();
    $this->authorize([ROLE_CLIENT]);
    
    $userId = $_SESSION['user_id'];
    $client = $this->clientModel->getClientByUserId($userId);
    
    if (!$client) {
        $this->redirect('/client/profile/create');
        return;
    }
    
    return $client;
}

/**
 * Show create profile form for new clients
 */
public function create() {
    requireLogin();
    $this->authorize([ROLE_CLIENT]);
    
    $userId = $_SESSION['user_id'];
    $existingClient = $this->clientModel->getClientByUserId($userId);
    
    if ($existingClient) {
        $this->redirect('/client/profile');
        return;
    }
    
    $user = $this->userModel->find($userId);
    
    $this->setTitle('Complete Your Profile');
    $this->setData('user', $user);
    $this->view('client/profile-create', 'dashboard'); // Add 'dashboard' as layout
}
/**
 * Store new client profile
 */
public function store() {
    requireLogin();
    $this->authorize([ROLE_CLIENT]);
    
    if (!$this->isPost()) {
        $this->redirect('/client/profile/create');
        return;
    }
    
    try {
        $this->validateCsrf();
        
        $userId = $_SESSION['user_id'];
        
        // Check if profile already exists
        $existingClient = $this->clientModel->getClientByUserId($userId);
        if ($existingClient) {
            $this->setFlash('error', 'Profile already exists');
            $this->redirect('/client/profile');
            return;
        }
        
        $profileData = [
            'user_id' => $userId,
            'emergency_contact' => $this->input('emergency_contact'),
            'preferred_contact_method' => $this->input('preferred_contact_method'),
            'notes' => $this->input('notes')
        ];
        
        // Also update user profile data
        $userData = [
            'first_name' => $this->input('first_name'),
            'last_name' => $this->input('last_name'),
            'phone' => $this->input('phone'),
            'address' => $this->input('address')
        ];
        
        // Validate data
        $errors = $this->validateProfileData($profileData, $userData);
        
        if (!empty($errors)) {
            $this->setFlash('error', 'Please fix the errors below');
            $this->setData('errors', $errors);
            $this->setData('old', array_merge($profileData, $userData));
            $this->create();
            return;
        }
        
        // Update user data first
        $this->userModel->updateUser($userId, $userData);
        
        // Create client profile
        $clientId = $this->clientModel->createClient($profileData);
        
        if ($clientId) {
            // Update session with new user data
            $_SESSION['first_name'] = $userData['first_name'];
            $_SESSION['last_name'] = $userData['last_name'];
            $_SESSION['phone'] = $userData['phone'];
            
            logActivity("Client profile created for user ID: {$userId}");
            $this->setFlash('success', 'Profile created successfully!');
            $this->redirect('/client/profile');
        } else {
            $this->setFlash('error', 'Failed to create profile');
            $this->setData('old', array_merge($profileData, $userData));
            $this->create();
        }
        
    } catch (Exception $e) {
        logError("Client profile creation error: " . $e->getMessage());
        $this->setFlash('error', 'An error occurred while creating profile');
        $this->create();
    }
}

/**
 * Validate profile creation data
 */
private function validateProfileData($profileData, $userData) {
    $errors = [];
    
    // Required fields
    $required = ['first_name', 'last_name', 'phone', 'emergency_contact'];
    foreach ($required as $field) {
        if (empty($userData[$field]) && empty($profileData[$field])) {
            $errors[$field] = 'This field is required';
        }
    }
    
    // Phone validation
    if (!empty($userData['phone']) && !validatePhone($userData['phone'])) {
        $errors['phone'] = 'Invalid phone number format';
    }
    
    // Emergency contact validation
    if (!empty($profileData['emergency_contact']) && !validatePhone($profileData['emergency_contact'])) {
        $errors['emergency_contact'] = 'Invalid emergency contact number';
    }
    
    // Preferred contact method validation
    $allowedMethods = ['phone', 'email', 'sms'];
    if (!empty($profileData['preferred_contact_method']) && !in_array($profileData['preferred_contact_method'], $allowedMethods)) {
        $errors['preferred_contact_method'] = 'Invalid contact method selected';
    }
    
    return $errors;
}

// Also update the existing profile method to use check

// Update animals method to check profile

}
?>