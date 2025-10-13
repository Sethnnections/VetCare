<?php
class UserController extends Controller {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    // List all users (admin only)
    public function index() {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        $page = $this->get('page', 1);
        $search = $this->get('search');
        $role = $this->get('role');
        
        if ($search) {
            $users = $this->userModel->searchUsers($search);
        } else {
            $users = $this->paginate($this->userModel, $page);
        }
        
        // Filter by role if specified
        if ($role) {
            $users = array_filter($users, function($user) use ($role) {
                return $user['role'] === $role;
            });
        }
        
        $this->setTitle('User Management');
        $this->setData('users', $users);
        $this->setData('search', $search);
        $this->setData('role', $role);
        $this->setData('stats', $this->userModel->getStats());
        $this->view('users/index');
    }
    
    // Show create user form (admin only)
    public function create() {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        $this->setTitle('Add New User');
        $this->view('users/create');
    }
    
    // Store new user (admin only)
    public function store() {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        if (!$this->isPost()) {
            $this->redirect('/users/create');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $userData = $this->input();
            $errors = $this->userModel->validate($userData);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('old', $userData);
                $this->create();
                return;
            }
            
            $userId = $this->userModel->createUser($userData);
            
            if ($userId) {
                $this->setFlash('success', 'User created successfully');
                $this->redirect('/users/' . $userId);
            } else {
                $this->setFlash('error', 'Failed to create user');
                $this->setData('old', $userData);
                $this->create();
            }
            
        } catch (Exception $e) {
            logError("User creation error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while creating user');
            $this->create();
        }
    }
    
    // Show user details
    public function show($id) {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            $this->setFlash('error', 'User not found');
            $this->redirect('/users');
            return;
        }
        
        $this->setTitle('User: ' . $user['name']);
        $this->setData('user', $user);
        $this->view('users/show');
    }
    
    // Show edit user form
    public function edit($id) {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            $this->setFlash('error', 'User not found');
            $this->redirect('/users');
            return;
        }
        
        $this->setTitle('Edit User: ' . $user['name']);
        $this->setData('user', $user);
        $this->view('users/edit');
    }
    
    // Update user
    public function update($id) {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        if (!$this->isPost()) {
            $this->redirect('/users/' . $id . '/edit');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $userData = $this->input();
            $errors = $this->userModel->validate($userData, $id);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('user', array_merge(['user_id' => $id], $userData));
                $this->edit($id);
                return;
            }
            
            $updated = $this->userModel->updateUser($id, $userData);
            
            if ($updated) {
                $this->setFlash('success', 'User updated successfully');
                $this->redirect('/users/' . $id);
            } else {
                $this->setFlash('error', 'Failed to update user');
                $this->setData('user', array_merge(['user_id' => $id], $userData));
                $this->edit($id);
            }
            
        } catch (Exception $e) {
            logError("User update error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while updating user');
            $this->edit($id);
        }
    }
    
    // Deactivate user
    public function deactivate($id) {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        if (!$this->isPost()) {
            $this->redirect('/users');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $user = $this->userModel->find($id);
            
            if (!$user) {
                $this->setFlash('error', 'User not found');
                $this->redirect('/users');
                return;
            }
            
            // Prevent deactivating own account
            $currentUser = getCurrentUser();
            if ($user['user_id'] == $currentUser['user_id']) {
                $this->setFlash('error', 'You cannot deactivate your own account');
                $this->redirect('/users');
                return;
            }
            
            $deactivated = $this->userModel->deactivateUser($id);
            
            if ($deactivated) {
                $this->setFlash('success', 'User deactivated successfully');
            } else {
                $this->setFlash('error', 'Failed to deactivate user');
            }
            
            $this->redirect('/users');
            
        } catch (Exception $e) {
            logError("User deactivation error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while deactivating user');
            $this->redirect('/users');
        }
    }
    
    // Activate user
    public function activate($id) {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        if (!$this->isPost()) {
            $this->redirect('/users');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $activated = $this->userModel->activateUser($id);
            
            if ($activated) {
                $this->setFlash('success', 'User activated successfully');
            } else {
                $this->setFlash('error', 'Failed to activate user');
            }
            
            $this->redirect('/users');
            
        } catch (Exception $e) {
            logError("User activation error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while activating user');
            $this->redirect('/users');
        }
    }
    
    // Reset user password (admin)
    public function resetPassword($id) {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        if (!$this->isPost()) {
            $this->redirect('/users/' . $id);
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $newPassword = $this->get('new_password');
            $confirmPassword = $this->get('confirm_password');
            
            // Validate input
            $errors = [];
            
            if (empty($newPassword)) {
                $errors['new_password'] = 'New password is required';
            } elseif (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
                $errors['new_password'] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
            }
            
            if ($newPassword !== $confirmPassword) {
                $errors['confirm_password'] = 'Passwords do not match';
            }
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->show($id);
                return;
            }
            
            // Update password directly
            $hashedPassword = hashPassword($newPassword);
            $updated = $this->userModel->updateUser($id, ['password' => $hashedPassword]);
            
            if ($updated) {
                logError("Password reset for user ID: {$id} by admin: " . getCurrentUser()['name']);
                $this->setFlash('success', 'Password reset successfully');
            } else {
                $this->setFlash('error', 'Failed to reset password');
            }
            
            $this->redirect('/users/' . $id);
            
        } catch (Exception $e) {
            logError("Password reset error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while resetting password');
            $this->redirect('/users/' . $id);
        }
    }
    
    // AJAX user search
    public function search() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $term = $this->get('term');
        $users = [];
        
        if (!empty($term)) {
            $users = $this->userModel->searchUsers($term);
        }
        
        $this->json($users);
    }
    
    // Get user statistics (AJAX)
    public function stats() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $stats = $this->userModel->getStats();
        $this->json($stats);
    }
    
    // Get users by role (AJAX)
    public function byRole($role) {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $users = $this->userModel->getUsersByRole($role);
        $this->json($users);
    }
}
?>