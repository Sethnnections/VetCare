<?php
class DashboardController extends Controller {
    
    public function index() {
        requireLogin();
        
        // Redirect based on user role
        $userRole = $_SESSION['role'] ?? '';
        
        switch ($userRole) {
            case ROLE_ADMIN:
                $this->admin();
                break;
            case ROLE_VETERINARY:
                $this->veterinary();
                break;
            case ROLE_CLIENT:
                $this->client();
                break;
            default:
                $this->redirect('/');
                break;
        }
    }
    
    public function admin() {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        // Get dashboard statistics
        $userModel = new User();
        $animalModel = new Animal();
        $treatmentModel = new Treatment();
        
        $stats = [
            'total_users' => $userModel->count(),
            'total_animals' => $animalModel->count(),
            'total_treatments' => $treatmentModel->count(),
            'active_treatments' => $treatmentModel->count(['status' => TREATMENT_ONGOING]),
            'users_by_role' => $userModel->getStats()
        ];
        
        $this->setTitle('Admin Dashboard');
        $this->setData('stats', $stats);
        $this->setData('current_page', 'dashboard');
        $this->view('admin/dashboard', 'dashboard');
    }
    
    public function veterinary() {
        requireLogin();
        $this->authorize(ROLE_VETERINARY);
        
        $animalModel = new Animal();
        $treatmentModel = new Treatment();
        
        $stats = [
            'total_animals' => $animalModel->count(),
            'my_treatments' => $treatmentModel->count(['veterinary_id' => $_SESSION['user_id']]),
            'pending_treatments' => $treatmentModel->count(['veterinary_id' => $_SESSION['user_id'], 'status' => TREATMENT_ONGOING])
        ];
        
        $this->setTitle('Veterinary Dashboard');
        $this->setData('stats', $stats);
        $this->setData('current_page', 'dashboard');
        $this->view('veterinary/dashboard', 'dashboard');
    }
    
    public function client() {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        $userId = $_SESSION['user_id'];
        $clientModel = new Client();
        $client = $clientModel->getClientByUserId($userId);
        
        
        // DEBUG: Check what's happening
        error_log("DEBUG: Client dashboard - User ID: $userId, Client exists: " . ($client ? 'YES' : 'NO'));
        
        // Redirect to create profile if doesn't exist
        if (!$client) {
            $this->setFlash('info', 'Please complete your profile to continue');
            $this->redirect('/client/profile/create');
            return;
        }
        
        $this->setTitle('Client Dashboard');
        $this->view('client/dashboard');
    }
}
?>








