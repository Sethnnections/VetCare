<?php
class DashboardController extends Controller {
    
    public function index() {
        requireLogin();
        
        // Redirect based on user role
        $userRole = $_SESSION['role'] ?? '';
        
        switch ($userRole) {
            case ROLE_ADMIN:
                $this->redirect('/admin/dashboard');
                break;
            case ROLE_VETERINARY:
                $this->redirect('/veterinary/dashboard');
                break;
            case ROLE_CLIENT:
                $this->redirect('/client/animals');
                break;
            default:
                $this->redirect('/');
                break;
        }
    }
    
    public function admin() {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        $this->setTitle('Admin Dashboard');
        $this->view('admin/dashboard');
    }
    
    public function veterinary() {
        requireLogin();
        $this->authorize(ROLE_VETERINARY);
        
        $this->setTitle('Veterinary Dashboard');
        $this->view('veterinary/dashboard');
    }
    
    public function client() {
        requireLogin();
        $this->authorize(ROLE_CLIENT);
        
        $this->redirect('/client/animals');
    }
}
?>