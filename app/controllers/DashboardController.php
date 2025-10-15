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
        $this->authorize(ROLE_CLIENT);
        
        $clientModel = new Client();
        $animalModel = new Animal();
        
        // Get client ID from user ID
        $client = $clientModel->getClientByUserId($_SESSION['user_id']);
        
        if ($client) {
            $stats = [
                'my_animals' => $animalModel->count(['client_id' => $client['client_id']]),
                'active_animals' => $animalModel->count(['client_id' => $client['client_id'], 'status' => STATUS_ACTIVE])
            ];
            
            $this->setData('stats', $stats);
        }
        
        $this->setTitle('Client Dashboard');
        $this->setData('current_page', 'dashboard');
        $this->view('client/dashboard', 'dashboard');
    }
}
?>