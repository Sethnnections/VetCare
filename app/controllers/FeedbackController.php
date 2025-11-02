<?php
class FeedbackController extends Controller {
    private $feedbackModel;
    private $clientModel;
    private $animalModel;
    private $userModel;
    
    public function __construct() {
        $this->feedbackModel = new Feedback();
        $this->clientModel = new Client();
        $this->animalModel = new Animal();
        $this->userModel = new User();
    }
    
    // Client: Submit feedback
    public function create() {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        $userId = $_SESSION['user_id'];
        $clientId = $this->clientModel->getClientIdByUserId($userId);
        
        if (!$clientId) {
            $this->setFlash('error', 'Please complete your client profile first.');
            $this->redirect('/client/profile/create');
            return;
        }
        
        // Get client's animals and treatments for dropdowns
        $animals = $this->animalModel->getAnimalsByClient($clientId);
        $veterinarians = $this->userModel->getUsersByRole(ROLE_VETERINARY);
        
        $this->setTitle('Submit Feedback');
        $this->setData('animals', $animals);
        $this->setData('veterinarians', $veterinarians);
        $this->view('client/feedback/create', 'dashboard');
    }
    
    public function store() {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        if (!$this->isPost()) {
            $this->redirect('/feedback/create');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $userId = $_SESSION['user_id'];
            $clientId = $this->clientModel->getClientIdByUserId($userId);
            
            if (!$clientId) {
                $this->setFlash('error', 'Please complete your client profile first.');
                $this->redirect('/client/profile/create');
                return;
            }
            
            $feedbackData = $this->input();
            $feedbackData['client_id'] = $clientId;
            
            $errors = $this->feedbackModel->validate($feedbackData);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('old', $feedbackData);
                
                // Reload dropdown data
                $animals = $this->animalModel->getAnimalsByClient($clientId);
                $veterinarians = $this->userModel->getUsersByRole(ROLE_VETERINARY);
                $this->setData('animals', $animals);
                $this->setData('veterinarians', $veterinarians);
                
                $this->create();
                return;
            }
            
            $feedbackId = $this->feedbackModel->submitFeedback($feedbackData);
            
            if ($feedbackId) {
                logActivity("Feedback submitted: ID {$feedbackId}", $_SESSION['user_id']);
                $this->setFlash('success', 'Thank you for your feedback!');
                $this->redirect('/client/feedback');
            } else {
                $this->setFlash('error', 'Failed to submit feedback');
                $this->setData('old', $feedbackData);
                $this->create();
            }
            
        } catch (Exception $e) {
            logError("Feedback submission error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while submitting feedback');
            $this->create();
        }
    }
    
    // Client: View their feedback
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
        
        $page = $this->get('page', 1);
        $feedbacks = $this->feedbackModel->getFeedbackByClient($clientId, $page);
        $stats = $this->feedbackModel->getFeedbackStats(ROLE_CLIENT, $userId);
        
        $this->setTitle('My Feedback');
        $this->setData('feedbacks', $feedbacks);
        $this->setData('stats', $stats);
        $this->setData('current_page', $page);
        $this->view('client/feedback/index', 'dashboard');
    }
    
    // Veterinary: View feedback about them
    public function veterinaryIndex() {
        requireLogin();
        $this->authorize([ROLE_VETERINARY]);
        
        $veterinaryId = $_SESSION['user_id'];
        $page = $this->get('page', 1);
        
        $feedbacks = $this->feedbackModel->getFeedbackByVeterinary($veterinaryId, $page);
        $stats = $this->feedbackModel->getFeedbackStats(ROLE_VETERINARY, $veterinaryId);
        
        $this->setTitle('Client Feedback');
        $this->setData('feedbacks', $feedbacks);
        $this->setData('stats', $stats);
        $this->setData('current_page', $page);
        $this->view('veterinary/feedback/index', 'dashboard');
    }
    
    // Admin: Manage all feedback
    public function adminIndex() {
        requireLogin();
        $this->authorize([ROLE_ADMIN]);
        
        $page = $this->get('page', 1);
        $status = $this->get('status');
        $rating = $this->get('rating');
        $veterinaryId = $this->get('veterinary_id');
        
        $filters = [];
        if ($status) $filters['status'] = $status;
        if ($rating) $filters['rating'] = $rating;
        if ($veterinaryId) $filters['veterinary_id'] = $veterinaryId;
        
        $feedbacks = $this->feedbackModel->getAllFeedback($filters, $page);
        $stats = $this->feedbackModel->getFeedbackStats(ROLE_ADMIN);
        $veterinarians = $this->userModel->getUsersByRole(ROLE_VETERINARY);
        
        $this->setTitle('Manage Feedback');
        $this->setData('feedbacks', $feedbacks);
        $this->setData('stats', $stats);
        $this->setData('veterinarians', $veterinarians);
        $this->setData('filters', compact('status', 'rating', 'veterinaryId'));
        $this->setData('current_page', $page);
        $this->view('admin/feedback/index', 'dashboard');
    }
    
    // View single feedback
    public function show($id) {
        requireLogin();
        
        $feedback = $this->feedbackModel->getFeedbackWithDetails($id);
        
        if (!$feedback) {
            $this->setFlash('error', 'Feedback not found');
            $this->redirectBack();
            return;
        }
        
        // Check permissions
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['user_id'];
        
        if ($userRole == ROLE_CLIENT) {
            $clientId = $this->clientModel->getClientIdByUserId($userId);
            if ($feedback['client_id'] != $clientId) {
                $this->setFlash('error', 'Access denied');
                $this->redirect('/client/feedback');
                return;
            }
        } elseif ($userRole == ROLE_VETERINARY) {
            if ($feedback['veterinary_id'] != $userId) {
                $this->setFlash('error', 'Access denied');
                $this->redirect('/veterinary/feedback');
                return;
            }
        }
        
        $this->setTitle('Feedback Details');
        $this->setData('feedback', $feedback);
        
        $viewPath = $userRole . '/feedback/show';
        $this->view($viewPath, 'dashboard');
    }
    
    // Admin: Update feedback status
    public function updateStatus($id) {
        requireLogin();
        $this->authorize([ROLE_ADMIN]);
        
        if (!$this->isPost()) {
            $this->redirect('/admin/feedback');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $feedback = $this->feedbackModel->find($id);
            if (!$feedback) {
                $this->setFlash('error', 'Feedback not found');
                $this->redirect('/admin/feedback');
                return;
            }
            
            $statusData = $this->input();
            $updated = $this->feedbackModel->updateFeedbackStatus(
                $id, 
                $statusData['status'],
                $statusData['admin_notes'] ?? null
            );
            
            if ($updated) {
                logActivity("Feedback status updated: ID {$id} to {$statusData['status']}", $_SESSION['user_id']);
                $this->setFlash('success', 'Feedback status updated successfully');
            } else {
                $this->setFlash('error', 'Failed to update feedback status');
            }
            
            $this->redirect('/admin/feedback/' . $id);
            
        } catch (Exception $e) {
            logError("Feedback status update error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while updating feedback status');
            $this->redirect('/admin/feedback/' . $id);
        }
    }
    
    // Admin/Veterinary: Respond to feedback
    public function respond($id) {
        requireLogin();
        $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
        
        if (!$this->isPost()) {
            $this->redirectBack();
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $feedback = $this->feedbackModel->find($id);
            if (!$feedback) {
                $this->setFlash('error', 'Feedback not found');
                $this->redirectBack();
                return;
            }
            
            $userRole = $_SESSION['role'];
            $responseData = $this->input();
            
            $updated = $this->feedbackModel->addResponse(
                $id, 
                $responseData['response'],
                $userRole
            );
            
            if ($updated) {
                logActivity("Response added to feedback: ID {$id}", $_SESSION['user_id']);
                $this->setFlash('success', 'Response added successfully');
            } else {
                $this->setFlash('error', 'Failed to add response');
            }
            
            $this->redirectBack();
            
        } catch (Exception $e) {
            logError("Feedback response error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while adding response');
            $this->redirectBack();
        }
    }
    
    // AJAX: Get feedback statistics
    public function stats() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['user_id'];
        
        $stats = $this->feedbackModel->getFeedbackStats($userRole, $userId);
        $this->json(['success' => true, 'stats' => $stats]);
    }
    
    private function redirectBack() {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/dashboard';
        $this->redirect($referer);
    }
}
?>