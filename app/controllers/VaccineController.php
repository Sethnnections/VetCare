<?php
class VaccineController extends Controller {
    private $vaccineModel;
    private $animalModel;
    private $userModel;
    private $clientModel;
    
    public function __construct() {
        $this->vaccineModel = new Vaccine();
        $this->animalModel = new Animal();
        $this->userModel = new User();
        $this->clientModel = new Client();
    }
    
    // List all vaccinations with role-based filtering
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
                $vaccinations = $this->vaccineModel->getVaccinesWithDetails(['status' => $status]);
                break;
                
            case ROLE_VETERINARY:
                $vaccinations = $this->vaccineModel->getVaccinesWithDetails([
                    'status' => $status,
                    'administered_by' => $userId
                ]);
                break;
                
            case ROLE_CLIENT:
                $clientId = $this->clientModel->getClientIdByUserId($userId);
                if ($clientId) {
                    // Get client's animals and their vaccinations
                    $animals = $this->animalModel->getAnimalsByClient($clientId);
                    $animalIds = array_column($animals, 'animal_id');
                    if (!empty($animalIds)) {
                        $placeholders = str_repeat('?,', count($animalIds) - 1) . '?';
                        $sql = "SELECT * FROM vaccine_details_view 
                                WHERE animal_id IN ($placeholders)";
                        if ($status) {
                            $sql .= " AND vaccine_status = ?";
                            $animalIds[] = $status;
                        }
                        $sql .= " ORDER BY vaccine_date DESC";
                        $vaccinations = fetchAll($sql, $animalIds);
                    } else {
                        $vaccinations = [];
                    }
                } else {
                    $vaccinations = [];
                }
                break;
                
            default:
                $vaccinations = [];
        }
        
        // Apply search filter
        if ($search && !empty($vaccinations)) {
            $vaccinations = array_filter($vaccinations, function($vaccine) use ($search) {
                return stripos($vaccine['vaccine_name'], $search) !== false ||
                       stripos($vaccine['animal_name'], $search) !== false ||
                       stripos($vaccine['client_full_name'], $search) !== false;
            });
        }
        
        $this->setTitle('Vaccinations');
        $this->setData('vaccinations', $vaccinations);
        $this->setData('search', $search);
        $this->setData('status', $status);
        $this->setData('stats', $this->vaccineModel->getVaccinationStats($userRole, $userId));
        
        // Load appropriate view based on role
        $viewPath = $userRole . '/vaccinations/index';
        $this->view($viewPath, 'dashboard');
    }
    
    // Show create vaccination form
    public function create() {
        requireLogin();
        $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
        
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['user_id'];
        
        // Get animals based on role
        if ($userRole === ROLE_VETERINARY) {
            $animals = $this->animalModel->getAnimalsByVeterinary($userId);
        } else {
            $animals = $this->animalModel->getActiveAnimals();
        }
        
        $this->setTitle('Add New Vaccination');
        $this->setData('animals', $animals);
        $this->view('vaccinations/create', 'dashboard');
    }
    
    // Store new vaccination
    public function store() {
        requireLogin();
        $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
        
        if (!$this->isPost()) {
            $this->redirect('/vaccinations/create');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $vaccineData = $this->input();
            $vaccineData['administered_by'] = $_SESSION['user_id'];
            $vaccineData['status'] = 'completed'; // Default to completed when administered
            
            $errors = $this->vaccineModel->validate($vaccineData);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('old', $vaccineData);
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->create();
                return;
            }
            
            $vaccineId = $this->vaccineModel->createVaccination($vaccineData);
            
            if ($vaccineId) {
                logActivity("Vaccination created: ID {$vaccineId} for animal {$vaccineData['animal_id']}", $_SESSION['user_id']);
                $this->setFlash('success', 'Vaccination recorded successfully');
                $this->redirect('/vaccinations/' . $vaccineId);
            } else {
                $this->setFlash('error', 'Failed to record vaccination');
                $this->setData('old', $vaccineData);
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->create();
            }
            
        } catch (Exception $e) {
            logError("Vaccination creation error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while recording vaccination');
            $this->create();
        }
    }
    
    // Show vaccination details
    public function show($id) {
        requireLogin();
        
        $vaccination = $this->vaccineModel->find($id);
        
        if (!$vaccination) {
            $this->setFlash('error', 'Vaccination record not found');
            $this->redirect('/vaccinations');
            return;
        }
        
        // Get vaccination with details
        $vaccinationDetails = $this->vaccineModel->getVaccinesWithDetails(['vaccine_id' => $id]);
        $vaccination = $vaccinationDetails[0] ?? $vaccination;
        
        $this->setTitle('Vaccination Details');
        $this->setData('vaccination', $vaccination);
        
        // Load appropriate view based on role
        $userRole = $_SESSION['role'];
        $viewPath = $userRole . '/vaccinations/show';
        $this->view($viewPath, 'dashboard');
    }
    
    // Show edit vaccination form
    public function edit($id) {
        requireLogin();
        $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
        
        $vaccination = $this->vaccineModel->find($id);
        
        if (!$vaccination) {
            $this->setFlash('error', 'Vaccination record not found');
            $this->redirect('/vaccinations');
            return;
        }
        
        $animals = $this->animalModel->getActiveAnimals();
        
        $this->setTitle('Edit Vaccination');
        $this->setData('vaccination', $vaccination);
        $this->setData('animals', $animals);
        $this->view('vaccinations/edit', 'dashboard');
    }
    
    // Update vaccination
    public function update($id) {
        requireLogin();
        $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
        
        if (!$this->isPost()) {
            $this->redirect('/vaccinations/' . $id . '/edit');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $vaccination = $this->vaccineModel->find($id);
            if (!$vaccination) {
                $this->setFlash('error', 'Vaccination record not found');
                $this->redirect('/vaccinations');
                return;
            }
            
            $vaccineData = $this->input();
            $errors = $this->vaccineModel->validate($vaccineData, $id);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('vaccination', array_merge(['vaccine_id' => $id], $vaccineData));
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->edit($id);
                return;
            }
            
            $updated = $this->vaccineModel->updateVaccination($id, $vaccineData);
            
            if ($updated) {
                logActivity("Vaccination updated: ID {$id}", $_SESSION['user_id']);
                $this->setFlash('success', 'Vaccination updated successfully');
                $this->redirect('/vaccinations/' . $id);
            } else {
                $this->setFlash('error', 'Failed to update vaccination');
                $this->setData('vaccination', array_merge(['vaccine_id' => $id], $vaccineData));
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->edit($id);
            }
            
        } catch (Exception $e) {
            logError("Vaccination update error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while updating vaccination');
            $this->edit($id);
        }
    }
    
    // Vaccination calendar view
    public function calendar() {
        requireLogin();
        
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['user_id'];
        
        $this->setTitle('Vaccination Calendar');
        $this->setData('stats', $this->vaccineModel->getVaccinationStats($userRole, $userId));
        
        $viewPath = $userRole . '/vaccinations/calendar';
        $this->view($viewPath, 'dashboard');
    }
    
    // Get calendar events (AJAX)
    public function calendarEvents() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $start = $this->get('start');
        $end = $this->get('end');
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['user_id'];
        
        $events = $this->vaccineModel->getVaccinationCalendarEvents($start, $end, $userId, $userRole);
        
        $this->json($events);
    }
    
    // Get upcoming vaccinations
    public function upcoming() {
        requireLogin();
        $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
        
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['user_id'];
        
        $veterinaryId = $userRole === ROLE_VETERINARY ? $userId : null;
        $upcomingVaccinations = $this->vaccineModel->getUpcomingVaccinations(30, $veterinaryId);
        $overdueVaccinations = $this->vaccineModel->getOverdueVaccinations($veterinaryId);
        
        $this->setTitle('Upcoming Vaccinations');
        $this->setData('upcomingVaccinations', $upcomingVaccinations);
        $this->setData('overdueVaccinations', $overdueVaccinations);
        
        $viewPath = $userRole . '/vaccinations/upcoming';
        $this->view($viewPath, 'dashboard');
    }
    
    // Schedule future vaccination
        public function schedule() {
    requireLogin();
    $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
    
    if ($this->isPost()) {
        $this->scheduleStore();
        return;
    }
    
    $userRole = $_SESSION['role'];
    $userId = $_SESSION['user_id'];
    
    // Get animals based on role
    if ($userRole === ROLE_VETERINARY) {
        $animals = $this->getAnimalsByVeterinary($userId);
    } else {
        $animals = $this->getActiveAnimals();
    }
    
    $this->setTitle('Schedule Vaccination');
    $this->setData('animals', $animals);
    
    // Try to load schedule view, fallback to create view
    $viewPath = 'vaccinations/schedule';
    if (!file_exists(VIEW_PATH . '/' . $viewPath . '.php')) {
        $viewPath = 'vaccinations/create';
        $this->setData('isScheduleView', true); // Add flag to modify create view
    }
    
    $this->view($viewPath, 'dashboard');
}
    
    // Store scheduled vaccination
    public function scheduleStore() {
        requireLogin();
        $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
        
        if (!$this->isPost()) {
            $this->redirect('/vaccinations/schedule');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $vaccineData = $this->input();
            $vaccineData['administered_by'] = $_SESSION['user_id'];
            $vaccineData['status'] = 'scheduled'; // Set as scheduled for future dates
            
            // If vaccine date is in future, set status to scheduled
            if (strtotime($vaccineData['vaccine_date']) > time()) {
                $vaccineData['status'] = 'scheduled';
            }
            
            $errors = $this->vaccineModel->validate($vaccineData);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('old', $vaccineData);
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->schedule();
                return;
            }
            
            $vaccineId = $this->vaccineModel->createVaccination($vaccineData);
            
            if ($vaccineId) {
                logActivity("Vaccination scheduled: ID {$vaccineId} for animal {$vaccineData['animal_id']}", $_SESSION['user_id']);
                $this->setFlash('success', 'Vaccination scheduled successfully');
                $this->redirect('/vaccinations/' . $vaccineId);
            } else {
                $this->setFlash('error', 'Failed to schedule vaccination');
                $this->setData('old', $vaccineData);
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->schedule();
            }
            
        } catch (Exception $e) {
            logError("Vaccination scheduling error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while scheduling vaccination');
            $this->schedule();
        }
    }
    
    // Mark vaccination as completed
    public function complete($id) {
        requireLogin();
        $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
        
        if (!$this->isPost()) {
            $this->redirect('/vaccinations/' . $id);
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $vaccination = $this->vaccineModel->find($id);
            if (!$vaccination) {
                $this->setFlash('error', 'Vaccination record not found');
                $this->redirect('/vaccinations');
                return;
            }
            
            $completed = $this->vaccineModel->markAsCompleted($id);
            
            if ($completed) {
                logActivity("Vaccination completed: ID {$id}", $_SESSION['user_id']);
                $this->setFlash('success', 'Vaccination marked as completed');
            } else {
                $this->setFlash('error', 'Failed to complete vaccination');
            }
            
            $this->redirect('/vaccinations/' . $id);
            
        } catch (Exception $e) {
            logError("Vaccination completion error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while completing vaccination');
            $this->redirect('/vaccinations/' . $id);
        }
    }
    
    // AJAX vaccination search
    public function search() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $term = $this->get('term');
        $vaccinations = [];
        
        if (!empty($term)) {
            $sql = "SELECT * FROM vaccine_details_view 
                    WHERE vaccine_name LIKE ? 
                    OR animal_name LIKE ? 
                    OR client_full_name LIKE ?
                    ORDER BY vaccine_date DESC 
                    LIMIT 50";
            
            $searchTerm = "%{$term}%";
            $vaccinations = fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm]);
        }
        
        $this->json(['success' => true, 'vaccinations' => $vaccinations]);
    }
    
    // Get vaccination statistics (AJAX)
    public function stats() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['user_id'];
        
        $stats = $this->vaccineModel->getVaccinationStats($userRole, $userId);
        $this->json(['success' => true, 'stats' => $stats]);
    }
    
    // Client-specific vaccination methods
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
        
        // Get client's animals and their vaccinations
        $animals = $this->animalModel->getAnimalsByClient($clientId);
        $vaccinations = [];
        
        foreach ($animals as $animal) {
            $animalVaccinations = $this->vaccineModel->getVaccinesByAnimal($animal['animal_id']);
            $vaccinations = array_merge($vaccinations, $animalVaccinations);
        }
        
        // Sort by date
        usort($vaccinations, function($a, $b) {
            return strtotime($b['vaccine_date']) - strtotime($a['vaccine_date']);
        });
        
        $this->setTitle('My Animal Vaccinations');
        $this->setData('vaccinations', $vaccinations);
        $this->setData('stats', $this->vaccineModel->getVaccinationStats(ROLE_CLIENT, $userId));
        $this->view('client/vaccinations/index', 'dashboard');
    }


    // Enhanced vaccination recording with medical process
public function recordWithProcess($animalId = null) {
    requireLogin();
    $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
    
    if ($this->isPost()) {
        $this->processVaccinationRecording();
        return;
    }
    
    $userRole = $_SESSION['role'];
    $userId = $_SESSION['user_id'];
    
    // Get animals based on role
    if ($userRole === ROLE_VETERINARY) {
        $animals = $this->animalModel->getAnimalsByVeterinary($userId);
    } else {
        $animals = $this->animalModel->getActiveAnimals();
    }
    
    // Get selected animal data
    $selectedAnimal = null;
    $animalVaccineHistory = [];
    $dueVaccinations = [];
    $vaccineProtocols = [];
    
    if ($animalId) {
        $selectedAnimal = $this->animalModel->getAnimalData($animalId);
        if ($selectedAnimal) {
            $animalVaccineHistory = $this->vaccineModel->getAnimalVaccinationHistory($animalId);
            $dueVaccinations = $this->vaccineModel->getDueVaccinations($animalId);
            $vaccineProtocols = $this->vaccineModel->getVaccineProtocols($selectedAnimal['species']);
        }
    }
    
    $this->setTitle('Record Vaccination - Medical Process');
    $this->setData('animals', $animals);
    $this->setData('selectedAnimal', $selectedAnimal);
    $this->setData('animalVaccineHistory', $animalVaccineHistory);
    $this->setData('dueVaccinations', $dueVaccinations);
    $this->setData('vaccineProtocols', $vaccineProtocols);
    $this->view('vaccinations/record-process', 'dashboard');
}

// Process vaccination recording with medical validation
private function processVaccinationRecording() {
    try {
        $this->validateCsrf();
        
        $vaccineData = $this->input();
        $vaccineData['administered_by'] = $_SESSION['user_id'];
        
        // Medical process validation
        $errors = $this->validateMedicalProcess($vaccineData);
        
        if (!empty($errors)) {
            $this->setFlash('error', 'Please complete all medical requirements');
            $this->setData('errors', $errors);
            $this->setData('old', $vaccineData);
            $this->recordWithProcess($vaccineData['animal_id']);
            return;
        }
        
        // Create vaccination record
        $vaccineId = $this->vaccineModel->createVaccination($vaccineData);
        
        if ($vaccineId) {
            // Log the medical process
            logActivity("Vaccination medically recorded: {$vaccineData['vaccine_name']} for animal {$vaccineData['animal_id']} by {$_SESSION['user_id']}", $_SESSION['user_id']);
            
            // Generate certificate if requested
            if (isset($vaccineData['generate_certificate'])) {
                $this->generateVaccinationCertificate($vaccineId);
            }
            
            $this->setFlash('success', 'Vaccination recorded successfully with complete medical documentation');
            $this->redirect('/vaccinations/' . $vaccineId . '/summary');
        } else {
            throw new Exception('Failed to save vaccination record');
        }
        
    } catch (Exception $e) {
        logError("Vaccination recording error: " . $e->getMessage());
        $this->setFlash('error', 'Medical recording failed: ' . $e->getMessage());
        $this->recordWithProcess($vaccineData['animal_id'] ?? null);
    }
}

// Medical process validation
private function validateMedicalProcess($data) {
    $errors = [];
    
    // Check animal health status
    $animal = $this->animalModel->find($data['animal_id']);
    if (!$animal || $animal['status'] != 'active') {
        $errors['animal_id'] = 'Animal is not available for vaccination';
    }
    
    // Validate temperature if provided
    if (!empty($data['animal_temperature']) && ($data['animal_temperature'] < 37 || $data['animal_temperature'] > 40)) {
        $errors['animal_temperature'] = 'Animal temperature outside normal range (37-40°C)';
    }
    
    // Validate weight for dosage calculation
    if (!empty($data['current_weight']) && $data['current_weight'] <= 0) {
        $errors['current_weight'] = 'Invalid animal weight';
    }
    
    // Check for recent vaccinations of same type
    $recentVaccination = $this->checkRecentVaccination($data['animal_id'], $data['vaccine_name']);
    if ($recentVaccination) {
        $errors['vaccine_name'] = "Same vaccine administered recently on " . formatDate($recentVaccination['vaccine_date']);
    }
    
    return array_merge($errors, $this->vaccineModel->validate($data));
}

// Check for recent vaccination of same type
private function checkRecentVaccination($animalId, $vaccineName) {
    $sql = "SELECT vaccine_date FROM vaccines 
            WHERE animal_id = ? AND vaccine_name = ? 
            AND vaccine_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ORDER BY vaccine_date DESC LIMIT 1";
    
    return fetchOne($sql, [$animalId, $vaccineName]);
}

// Vaccination summary and certificate
public function summary($id) {
    requireLogin();
    
    $vaccination = $this->vaccineModel->getVaccinationCertificate($id);
    
    if (!$vaccination) {
        $this->setFlash('error', 'Vaccination record not found');
        $this->redirect('/vaccinations');
        return;
    }
    
    $this->setTitle('Vaccination Summary - ' . $vaccination['vaccine_name']);
    $this->setData('vaccination', $vaccination);
    $this->setData('currentUser', getCurrentUser());
    
    $userRole = $_SESSION['role'];
    $viewPath = $userRole . '/vaccinations/summary';
    $this->view($viewPath, 'dashboard');
}

// Generate vaccination certificate (PDF)
public function generateCertificate($id) {
    requireLogin();
    
    $vaccination = $this->vaccineModel->getVaccinationCertificate($id);
    
    if (!$vaccination) {
        $this->setFlash('error', 'Vaccination record not found');
        $this->redirect('/vaccinations');
        return;
    }
    
    // Generate PDF certificate
    $this->generateVaccinationCertificatePDF($vaccination);
}

// Record adverse reaction
public function recordReaction($id) {
    requireLogin();
    $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
    
    if (!$this->isPost()) {
        $this->redirect('/vaccinations/' . $id);
        return;
    }
    
    try {
        $this->validateCsrf();
        
        $reactionData = $this->input();
        $success = $this->vaccineModel->recordReaction(
            $id, 
            $reactionData['reaction_notes'], 
            $reactionData['severity']
        );
        
        if ($success) {
            logActivity("Adverse reaction recorded for vaccination ID: {$id}", $_SESSION['user_id']);
            $this->setFlash('warning', 'Adverse reaction recorded and flagged for review');
        } else {
            $this->setFlash('error', 'Failed to record adverse reaction');
        }
        
        $this->redirect('/vaccinations/' . $id);
        
    } catch (Exception $e) {
        logError("Reaction recording error: " . $e->getMessage());
        $this->setFlash('error', 'Error recording reaction: ' . $e->getMessage());
        $this->redirect('/vaccinations/' . $id);
    }
}

// Verify vaccination (quality control)
public function verify($id) {
    requireLogin();
    $this->authorize([ROLE_ADMIN]);
    
    if (!$this->isPost()) {
        $this->redirect('/vaccinations/' . $id);
        return;
    }
    
    try {
        $this->validateCsrf();
        
        $verificationData = $this->input();
        $success = $this->vaccineModel->verifyVaccination(
            $id, 
            $_SESSION['user_id'],
            $verificationData['verification_notes'] ?? null
        );
        
        if ($success) {
            logActivity("Vaccination verified: ID {$id}", $_SESSION['user_id']);
            $this->setFlash('success', 'Vaccination verified successfully');
        } else {
            $this->setFlash('error', 'Failed to verify vaccination');
        }
        
        $this->redirect('/vaccinations/' . $id);
        
    } catch (Exception $e) {
        logError("Vaccination verification error: " . $e->getMessage());
        $this->setFlash('error', 'Error verifying vaccination: ' . $e->getMessage());
        $this->redirect('/vaccinations/' . $id);
    }
}

// Get animal vaccination report
public function animalReport($animalId) {
    requireLogin();
    
    $animal = $this->animalModel->getAnimalData($animalId);
    
    if (!$animal) {
        $this->setFlash('error', 'Animal not found');
        $this->redirect('/animals');
        return;
    }
    
    // Check permissions
    $userRole = $_SESSION['role'];
    $userId = $_SESSION['user_id'];
    
    if ($userRole === ROLE_CLIENT) {
        $clientId = $this->clientModel->getClientIdByUserId($userId);
        if (!$clientId || $animal['client_id'] != $clientId) {
            $this->setFlash('error', 'Access denied');
            $this->redirect('/client/animals');
            return;
        }
    } elseif ($userRole === ROLE_VETERINARY) {
        if (!$this->animalModel->isAssignedToVeterinary($animalId, $userId)) {
            $this->setFlash('error', 'Animal not assigned to you');
            $this->redirect('/veterinary/animals');
            return;
        }
    }
    
    $vaccinationHistory = $this->vaccineModel->getAnimalVaccinationHistory($animalId);
    $dueVaccinations = $this->vaccineModel->getDueVaccinations($animalId);
    $vaccineProtocols = $this->vaccineModel->getVaccineProtocols($animal['species']);
    
    $this->setTitle('Vaccination Report - ' . $animal['animal_name']);
    $this->setData('animal', $animal);
    $this->setData('vaccinationHistory', $vaccinationHistory);
    $this->setData('dueVaccinations', $dueVaccinations);
    $this->setData('vaccineProtocols', $vaccineProtocols);
    
    $viewPath = $userRole . '/vaccinations/animal-report';
    $this->view($viewPath, 'dashboard');
}

// Add to VaccineController.php
public function sendVaccinationReminders() {
    $dueSoon = $this->vaccineModel->getDueVaccinations(7); // 7 days ahead
    
    foreach ($dueSoon as $vaccination) {
        $this->sendVaccinationReminder($vaccination);
    }
    
    return count($dueSoon);
}

private function sendVaccinationReminder($vaccination) {
    $clientEmail = $vaccination['client_email'];
    $subject = "Vaccination Reminder: {$vaccination['vaccine_name']} for {$vaccination['animal_name']}";
    
    $message = "
    <h3>Vaccination Reminder</h3>
    <p>Dear {$vaccination['client_full_name']},</p>
    <p>This is a reminder that your pet <strong>{$vaccination['animal_name']}</strong> is due for vaccination:</p>
    
    <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;'>
        <strong>Vaccine:</strong> {$vaccination['vaccine_name']}<br>
        <strong>Due Date:</strong> " . formatDate($vaccination['next_due_date']) . "<br>
        <strong>Animal:</strong> {$vaccination['animal_name']} ({$vaccination['species']})
    </div>
    
    <p>Please schedule an appointment at your earliest convenience.</p>
    <p>Best regards,<br>Veterinary Clinic Team</p>
    ";
    
    return sendEmail($clientEmail, $subject, $message);
}

// Add to VaccineController class

/**
 * Get animals by veterinary (for veterinary role)
 */
private function getAnimalsByVeterinary($veterinaryId) {
    $sql = "SELECT a.*, c.name as client_name 
            FROM animals a 
            JOIN clients c ON a.client_id = c.client_id 
            WHERE a.assigned_veterinary = ? AND a.status = 'active'";
    return fetchAll($sql, [$veterinaryId]);
}

/**
 * Get active animals (for admin role)
 */
private function getActiveAnimals() {
    $sql = "SELECT a.*, c.name as client_name 
            FROM animals a 
            JOIN clients c ON a.client_id = c.client_id 
            WHERE a.status = 'active'";
    return fetchAll($sql);
}

/**
 * Generate vaccination certificate PDF (placeholder)
 */
private function generateVaccinationCertificatePDF($vaccination) {
    // For now, redirect to the certificate view
    $this->redirect('/vaccinations/' . $vaccination['vaccine_id'] . '/certificate');
}

/**
 * Send vaccination reminders
 */
public function sendReminders() {
    requireLogin();
    $this->authorize([ROLE_ADMIN]);
    
    $remindersSent = $this->sendVaccinationReminders();
    
    $this->setFlash('success', "Vaccination reminders sent: {$remindersSent} reminders");
    $this->redirect('/vaccinations');
}
}
?>