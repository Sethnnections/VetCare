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
        $status = $this->get('status');
        
        // Build query conditions
        $conditions = [];
        $params = [];
        
        if ($search) {
            $users = $this->userModel->searchUsers($search);
            $pagination = null;
        } else {
            // Apply filters
            if ($role) {
                $conditions[] = "role = :role";
                $params['role'] = $role;
            }
            
            if ($status !== null && $status !== '') {
                $conditions[] = "is_active = :status";
                $params['status'] = $status;
            }
            
            $whereClause = '';
            if (!empty($conditions)) {
                $whereClause = 'WHERE ' . implode(' AND ', $conditions);
            }
            
            $result = $this->userModel->getAllUsersWithFilters($page, 15, $whereClause, $params);
            $users = $result['data'];
            $pagination = [
                'current_page' => $result['page'],
                'total_pages' => $result['total_pages'],
                'total' => $result['total']
            ];
        }
        
        $this->setTitle('User Management');
        $this->setData('users', $users);
        $this->setData('search', $search);
        $this->setData('role', $role);
        $this->setData('status', $status);
        $this->setData('pagination', $pagination);
        $this->setData('stats', $this->userModel->getStats());
        $this->view('admin/users/index', 'dashboard');
    }
    
    // Show create user form (admin only)
    public function create() {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        $this->setTitle('Add New User');
        $this->view('admin/users/create', 'dashboard');
    }
    
    // Store new user (admin only)
    public function store() {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        if (!$this->isPost()) {
            $this->redirect('/admin/users/create');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $userData = $this->input();
            
            // Validate password confirmation
            $errors = [];
            if ($userData['password'] !== $userData['confirm_password']) {
                $errors['confirm_password'] = 'Passwords do not match';
            }
            
            // Validate with model
            $modelErrors = $this->userModel->validate($userData);
            $errors = array_merge($errors, $modelErrors);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('old', $userData);
                $this->create();
                return;
            }
            
            $userId = $this->userModel->createUser($userData);
            
            if ($userId) {
                logActivity("User created: {$userData['email']} by admin");
                $this->setFlash('success', 'User created successfully');
                $this->redirect('/admin/users/' . $userId);
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
            $this->redirect('/admin/users');
            return;
        }
        
        // Get user activity logs
        $activityLogs = $this->userModel->getUserActivityLogs($id);
        
        $this->setTitle('User Details: ' . $user['first_name'] . ' ' . $user['last_name']);
        $this->setData('user', $user);
        $this->setData('activityLogs', $activityLogs);
        $this->view('admin/users/show', 'dashboard');
    }
    
    // Show edit user form
    public function edit($id) {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            $this->setFlash('error', 'User not found');
            $this->redirect('/admin/users');
            return;
        }
        
        $this->setTitle('Edit User: ' . $user['first_name'] . ' ' . $user['last_name']);
        $this->setData('user', $user);
        $this->view('admin/users/edit', 'dashboard');
    }
    
    // Update user
    public function update($id) {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        if (!$this->isPost()) {
            $this->redirect('/admin/users/' . $id . '/edit');
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
                logActivity("User updated: ID {$id} by admin");
                $this->setFlash('success', 'User updated successfully');
                $this->redirect('/admin/users/' . $id);
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
            $this->redirect('/admin/users');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $user = $this->userModel->find($id);
            
            if (!$user) {
                $this->setFlash('error', 'User not found');
                $this->redirect('/admin/users');
                return;
            }
            
            // Prevent deactivating own account
            $currentUser = getCurrentUser();
            if ($user['user_id'] == $currentUser['user_id']) {
                $this->setFlash('error', 'You cannot deactivate your own account');
                $this->redirect('/admin/users');
                return;
            }
            
            $deactivated = $this->userModel->deactivateUser($id);
            
            if ($deactivated) {
                logActivity("User deactivated: {$user['email']} by admin");
                $this->setFlash('success', 'User deactivated successfully');
            } else {
                $this->setFlash('error', 'Failed to deactivate user');
            }
            
            $this->redirect('/admin/users');
            
        } catch (Exception $e) {
            logError("User deactivation error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while deactivating user');
            $this->redirect('/admin/users');
        }
    }
    
    // Activate user
    public function activate($id) {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        if (!$this->isPost()) {
            $this->redirect('/admin/users');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $activated = $this->userModel->activateUser($id);
            
            if ($activated) {
                $user = $this->userModel->find($id);
                logActivity("User activated: {$user['email']} by admin");
                $this->setFlash('success', 'User activated successfully');
            } else {
                $this->setFlash('error', 'Failed to activate user');
            }
            
            $this->redirect('/admin/users');
            
        } catch (Exception $e) {
            logError("User activation error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while activating user');
            $this->redirect('/admin/users');
        }
    }
    
    // Reset user password (admin)
    public function resetPassword($id) {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        if (!$this->isPost()) {
            $this->redirect('/admin/users/' . $id);
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $newPassword = $this->input('new_password');
            $confirmPassword = $this->input('confirm_password');
            
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
                $user = $this->userModel->find($id);
                logActivity("Password reset for user: {$user['email']} by admin");
                $this->setFlash('success', 'Password reset successfully');
            } else {
                $this->setFlash('error', 'Failed to reset password');
            }
            
            $this->redirect('/admin/users/' . $id);
            
        } catch (Exception $e) {
            logError("Password reset error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while resetting password');
            $this->redirect('/admin/users/' . $id);
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
    
    // Quick user status toggle (AJAX)
    public function toggleStatus($id) {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        try {
            $user = $this->userModel->find($id);
            if (!$user) {
                $this->json(['error' => 'User not found'], 404);
                return;
            }
            
            $newStatus = $user['is_active'] ? 0 : 1;
            $updated = $this->userModel->updateUser($id, ['is_active' => $newStatus]);
            
            if ($updated) {
                $this->json([
                    'success' => true,
                    'new_status' => $newStatus,
                    'status_text' => $newStatus ? 'Active' : 'Inactive',
                    'status_class' => $newStatus ? 'bg-success' : 'bg-warning'
                ]);
            } else {
                $this->json(['error' => 'Failed to update status'], 500);
            }
            
        } catch (Exception $e) {
            logError("User status toggle error: " . $e->getMessage());
            $this->json(['error' => 'An error occurred'], 500);
        }
    }
}