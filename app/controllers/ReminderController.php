<?php
class ReminderController extends Controller {
    private $reminderModel;
    private $animalModel;
    private $userModel;
    private $treatmentModel;
    private $vaccineModel;
    
    public function __construct() {
        $this->reminderModel = new Reminder();
        $this->animalModel = new Animal();
        $this->userModel = new User();
        $this->treatmentModel = new Treatment();
        $this->vaccineModel = new Vaccine();
    }
    
    // List all reminders
    public function index() {
        requireLogin();
        
        $page = $this->get('page', 1);
        $search = $this->get('search');
        $status = $this->get('status');
        $priority = $this->get('priority');
        
        if ($search) {
            $reminders = $this->reminderModel->searchReminders($search);
        } else {
            $reminders = $this->paginate($this->reminderModel, $page);
        }
        
        // Filter by status if specified
        if ($status) {
            $reminders = array_filter($reminders, function($reminder) use ($status) {
                return $reminder['status'] === $status;
            });
        }
        
        // Filter by priority if specified
        if ($priority) {
            $reminders = array_filter($reminders, function($reminder) use ($priority) {
                return $reminder['priority'] === $priority;
            });
        }
        
        $this->setTitle('Reminders');
        $this->setData('reminders', $reminders);
        $this->setData('search', $search);
        $this->setData('status', $status);
        $this->setData('priority', $priority);
        $this->setData('stats', $this->reminderModel->getStats());
        $this->view('reminders/index');
    }
    
    // Show create reminder form
    public function create() {
        requireLogin();
        
        $animals = $this->animalModel->getActiveAnimals();
        $users = $this->userModel->getActiveUsers();
        
        $this->setTitle('Create Reminder');
        $this->setData('animals', $animals);
        $this->setData('users', $users);
        $this->view('reminders/create');
    }
    
    // Store new reminder
    public function store() {
        requireLogin();
        
        if (!$this->isPost()) {
            $this->redirect('/reminders/create');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $reminderData = $this->input();
            $errors = $this->reminderModel->validate($reminderData);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('old', $reminderData);
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->setData('users', $this->userModel->getActiveUsers());
                $this->create();
                return;
            }
            
            $reminderId = $this->reminderModel->createReminder($reminderData);
            
            if ($reminderId) {
                $this->setFlash('success', 'Reminder created successfully');
                $this->redirect('/reminders/' . $reminderId);
            } else {
                $this->setFlash('error', 'Failed to create reminder');
                $this->setData('old', $reminderData);
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->setData('users', $this->userModel->getActiveUsers());
                $this->create();
            }
            
        } catch (Exception $e) {
            logError("Reminder creation error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while creating reminder');
            $this->create();
        }
    }
    
    // Show reminder details
    public function show($id) {
        requireLogin();
        
        $reminder = $this->reminderModel->getReminderWithDetails($id);
        
        if (!$reminder) {
            $this->setFlash('error', 'Reminder not found');
            $this->redirect('/reminders');
            return;
        }
        
        $this->setTitle('Reminder: ' . $reminder['title']);
        $this->setData('reminder', $reminder);
        $this->view('reminders/show');
    }
    
    // Show edit reminder form
    public function edit($id) {
        requireLogin();
        
        $reminder = $this->reminderModel->find($id);
        
        if (!$reminder) {
            $this->setFlash('error', 'Reminder not found');
            $this->redirect('/reminders');
            return;
        }
        
        $animals = $this->animalModel->getActiveAnimals();
        $users = $this->userModel->getActiveUsers();
        
        $this->setTitle('Edit Reminder');
        $this->setData('reminder', $reminder);
        $this->setData('animals', $animals);
        $this->setData('users', $users);
        $this->view('reminders/edit');
    }
    
    // Update reminder
    public function update($id) {
        requireLogin();
        
        if (!$this->isPost()) {
            $this->redirect('/reminders/' . $id . '/edit');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $reminderData = $this->input();
            $errors = $this->reminderModel->validate($reminderData, $id);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('reminder', array_merge(['reminder_id' => $id], $reminderData));
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->setData('users', $this->userModel->getActiveUsers());
                $this->edit($id);
                return;
            }
            
            $updated = $this->reminderModel->update($id, $reminderData);
            
            if ($updated) {
                $this->setFlash('success', 'Reminder updated successfully');
                $this->redirect('/reminders/' . $id);
            } else {
                $this->setFlash('error', 'Failed to update reminder');
                $this->setData('reminder', array_merge(['reminder_id' => $id], $reminderData));
                $this->setData('animals', $this->animalModel->getActiveAnimals());
                $this->setData('users', $this->userModel->getActiveUsers());
                $this->edit($id);
            }
            
        } catch (Exception $e) {
            logError("Reminder update error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while updating reminder');
            $this->edit($id);
        }
    }
    
    // Mark reminder as completed
    public function complete($id) {
        requireLogin();
        
        if (!$this->isPost()) {
            $this->redirect('/reminders/' . $id);
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $completed = $this->reminderModel->markAsCompleted($id);
            
            if ($completed) {
                $this->setFlash('success', 'Reminder marked as completed');
            } else {
                $this->setFlash('error', 'Failed to complete reminder');
            }
            
            $this->redirect('/reminders/' . $id);
            
        } catch (Exception $e) {
            logError("Reminder completion error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while completing reminder');
            $this->redirect('/reminders/' . $id);
        }
    }
    
    // Get upcoming reminders
    public function upcoming() {
        requireLogin();
        
        $days = $this->get('days', 7);
        $upcomingReminders = $this->reminderModel->getUpcomingReminders($days);
        $overdueReminders = $this->reminderModel->getOverdueReminders();
        
        $this->setTitle('Upcoming Reminders');
        $this->setData('upcomingReminders', $upcomingReminders);
        $this->setData('overdueReminders', $overdueReminders);
        $this->setData('days', $days);
        $this->view('reminders/upcoming');
    }
    
    // Get my reminders (for current user)
    public function myReminders() {
        requireLogin();
        
        $user = getCurrentUser();
        $myReminders = $this->reminderModel->getRemindersByUser($user['user_id']);
        
        $this->setTitle('My Reminders');
        $this->setData('myReminders', $myReminders);
        $this->view('reminders/my-reminders');
    }
    
    // Create treatment follow-up reminder
    public function createTreatmentFollowUp($treatmentId) {
        requireLogin();
        
        if (!$this->isPost()) {
            $this->redirect('/treatments/' . $treatmentId);
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $followUpDate = $this->get('follow_up_date');
            
            if (empty($followUpDate)) {
                $this->setFlash('error', 'Follow-up date is required');
                $this->redirect('/treatments/' . $treatmentId);
                return;
            }
            
            $created = $this->reminderModel->createTreatmentFollowUpReminder($treatmentId, $followUpDate);
            
            if ($created) {
                $this->setFlash('success', 'Follow-up reminder created successfully');
            } else {
                $this->setFlash('error', 'Failed to create follow-up reminder');
            }
            
            $this->redirect('/treatments/' . $treatmentId);
            
        } catch (Exception $e) {
            logError("Treatment follow-up reminder creation error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while creating follow-up reminder');
            $this->redirect('/treatments/' . $treatmentId);
        }
    }
    
    // AJAX reminder search
    public function search() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $term = $this->get('term');
        $reminders = [];
        
        if (!empty($term)) {
            $reminders = $this->reminderModel->searchReminders($term);
        }
        
        $this->json($reminders);
    }
    
    // Get reminder statistics (AJAX)
    public function stats() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $stats = $this->reminderModel->getStats();
        $this->json($stats);
    }
    
    // Get reminders for dashboard (AJAX)
    public function dashboardReminders() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $user = getCurrentUser();
        $reminders = [];
        
        switch ($user['role']) {
            case ROLE_ADMIN:
                $reminders = $this->reminderModel->getOverdueReminders();
                break;
                
            case ROLE_VETERINARY:
                $reminders = $this->reminderModel->getRemindersByUser($user['user_id']);
                break;
                
            case ROLE_CLIENT:
                $clientId = $this->getClientIdFromUser($user);
                if ($clientId) {
                    $reminders = $this->reminderModel->getRemindersByClient($clientId);
                }
                break;
        }
        
        $this->json(array_slice($reminders, 0, 10));
    }
    
    private function getClientIdFromUser($user) {
        $clientModel = new Client();
        $client = $clientModel->findBy('email', $user['email']);
        return $client ? $client['client_id'] : null;
    }
}
?>