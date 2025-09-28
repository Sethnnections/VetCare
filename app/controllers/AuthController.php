<?php

class AuthController extends Controller {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    // Show login form
    public function login() {
        // Redirect if already logged in
        if (isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        
        $this->setTitle('Login');
        $this->setData('page', 'login');
        $this->view('auth/login');
    }
    
    // Handle login form submission
    public function authenticate() {
        if (!$this->isPost()) {
            $this->redirect('/auth/login');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $email = $this->get('email');
            $password = $this->get('password');
            $remember = $this->get('remember');
            
            // Validate input
            $errors = [];
            if (empty($email)) {
                $errors['email'] = 'Email is required';
            }
            if (empty($password)) {
                $errors['password'] = 'Password is required';
            }
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fill in all required fields');
                $this->setData('errors', $errors);
                $this->setData('old', $this->input());
                $this->login();
                return;
            }
            
            // Attempt authentication
            $user = $this->userModel->authenticate($email, $password);
            
            if ($user) {
                // Start session and store user data
                startSession();
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user'] = $user;
                $_SESSION['last_activity'] = time();
                
                // Handle remember me
                if ($remember) {
                    $this->setRememberMeCookie($user['user_id']);
                }
                
                // Log successful login
                logError("User logged in: {$user['email']} (ID: {$user['user_id']})");
                
                $this->setFlash('success', 'Welcome back, ' . $user['name'] . '!');
                $this->redirectToDashboard();
            } else {
                // Log failed login attempt
                logError("Failed login attempt for: {$email}");
                
                $this->setFlash('error', 'Invalid email or password');
                $this->setData('old', ['email' => $email]);
                $this->login();
            }
            
        } catch (Exception $e) {
            logError("Login error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred during login');
            $this->login();
        }
    }
    
    // Handle logout
    public function logout() {
        startSession();
        
        // Log logout
        $user = getCurrentUser();
        if ($user) {
            logError("User logged out: {$user['email']} (ID: {$user['user_id']})");
        }
        
        // Clear remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        // Destroy session
        session_destroy();
        
        $this->setFlash('success', 'You have been logged out successfully');
        $this->redirect('/auth/login');
    }
    
    // Show registration form (for admin use)
    public function register() {
        $this->authorize(ROLE_ADMIN);
        
        $this->setTitle('Register New User');
        $this->setData('page', 'register');
        $this->view('auth/register');
    }
    
    // Handle user registration
    public function store() {
        $this->authorize(ROLE_ADMIN);
        
        if (!$this->isPost()) {
            $this->redirect('/auth/register');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $userData = $this->input();
            
            // Validate data
            $errors = $this->userModel->validate($userData);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('old', $userData);
                $this->register();
                return;
            }
            
            // Create user
            $userId = $this->userModel->createUser($userData);
            
            if ($userId) {
                logError("New user registered: {$userData['email']} (ID: {$userId}) by " . getCurrentUser()['name']);
                $this->setFlash('success', 'User registered successfully');
                $this->redirect('/admin/users');
            } else {
                $this->setFlash('error', 'Failed to register user');
                $this->setData('old', $userData);
                $this->register();
            }
            
        } catch (Exception $e) {
            logError("Registration error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred during registration');
            $this->setData('old', $this->input());
            $this->register();
        }
    }
    
    // Show change password form
    public function changePassword() {
        requireLogin();
        
        $this->setTitle('Change Password');
        $this->setData('page', 'change-password');
        $this->view('auth/change-password');
    }
    
    // Handle password change
    public function updatePassword() {
        requireLogin();
        
        if (!$this->isPost()) {
            $this->redirect('/auth/change-password');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $currentPassword = $this->get('current_password');
            $newPassword = $this->get('new_password');
            $confirmPassword = $this->get('confirm_password');
            
            // Validate input
            $errors = [];
            
            if (empty($currentPassword)) {
                $errors['current_password'] = 'Current password is required';
            }
            
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
                $this->changePassword();
                return;
            }
            
            // Change password
            $user = getCurrentUser();
            $result = $this->userModel->changePassword($user['user_id'], $currentPassword, $newPassword);
            
            if ($result['success']) {
                logError("Password changed for user: {$user['email']} (ID: {$user['user_id']})");
                $this->setFlash('success', $result['message']);
                $this->redirectToDashboard();
            } else {
                $this->setFlash('error', $result['error']);
                $this->changePassword();
            }
            
        } catch (Exception $e) {
            logError("Password change error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while changing password');
            $this->changePassword();
        }
    }
    
    // Show forgot password form
    public function forgotPassword() {
        if (isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        
        $this->setTitle('Forgot Password');
        $this->setData('page', 'forgot-password');
        $this->view('auth/forgot-password');
    }
    
    // Handle forgot password request
    public function sendResetLink() {
        if (!$this->isPost()) {
            $this->redirect('/auth/forgot-password');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $email = $this->get('email');
            
            if (empty($email)) {
                $this->setFlash('error', 'Email is required');
                $this->forgotPassword();
                return;
            }
            
            $user = $this->userModel->findBy('email', $email);
            
            if ($user) {
                // Generate reset token (simplified - in production, store in database)
                $resetToken = bin2hex(random_bytes(32));
                $resetLink = url("auth/reset-password?token={$resetToken}&email=" . urlencode($email));
                
                // In a real application, you would:
                // 1. Store the reset token in database with expiration
                // 2. Send email with reset link
                // For now, we'll just show a success message
                
                logError("Password reset requested for: {$email}");
            }
            
            // Always show success message for security (don't reveal if email exists)
            $this->setFlash('success', 'If an account with that email exists, a password reset link has been sent.');
            $this->redirect('/auth/login');
            
        } catch (Exception $e) {
            logError("Forgot password error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while processing your request');
            $this->forgotPassword();
        }
    }
    
    // Check session timeout
    public function checkSession() {
        startSession();
        
        if (isLoggedIn()) {
            $lastActivity = $_SESSION['last_activity'] ?? 0;
            
            if (time() - $lastActivity > SESSION_TIMEOUT) {
                session_destroy();
                $this->json(['status' => 'expired', 'message' => 'Session expired']);
                return;
            }
            
            $_SESSION['last_activity'] = time();
            $this->json(['status' => 'active']);
        } else {
            $this->json(['status' => 'not_logged_in']);
        }
    }
    
    // Handle remember me functionality
    private function setRememberMeCookie($userId) {
        $token = bin2hex(random_bytes(32));
        $expiry = time() + (30 * 24 * 60 * 60); // 30 days
        
        setcookie('remember_token', $token, $expiry, '/', '', false, true);
        
        // In production, store the token hash in database
        // For now, we'll use a simple approach
        setcookie('remember_user', $userId, $expiry, '/', '', false, true);
    }
    
    // Check remember me cookie
    public function checkRememberMe() {
        if (!isLoggedIn() && isset($_COOKIE['remember_token']) && isset($_COOKIE['remember_user'])) {
            $userId = $_COOKIE['remember_user'];
            $user = $this->userModel->find($userId);
            
            if ($user && $user['status'] == STATUS_ACTIVE) {
                startSession();
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user'] = $user;
                $_SESSION['last_activity'] = time();
                
                logError("User auto-logged in via remember me: {$user['email']} (ID: {$user['user_id']})");
                return true;
            } else {
                // Clear invalid cookies
                setcookie('remember_token', '', time() - 3600, '/');
                setcookie('remember_user', '', time() - 3600, '/');
            }
        }
        
        return false;
    }
    
    // Redirect to appropriate dashboard based on role
    private function redirectToDashboard() {
        $user = getCurrentUser();
        
        switch ($user['role']) {
            case ROLE_ADMIN:
                $this->redirect('/admin/dashboard');
                break;
            case ROLE_VETERINARY:
                $this->redirect('/veterinary/dashboard');
                break;
            case ROLE_CLIENT:
                $this->redirect('/client/dashboard');
                break;
            default:
                $this->redirect('/');
                break;
        }
    }
    
    // AJAX endpoint to check if email exists (for registration)
    public function checkEmail() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $email = $this->get('email');
        
        if (empty($email)) {
            $this->json(['exists' => false]);
            return;
        }
        
        $user = $this->userModel->findBy('email', $email);
        $this->json(['exists' => $user !== null]);
    }
    
    // Get user profile
    public function profile() {
        requireLogin();
        
        $user = getCurrentUser();
        $userDetails = $this->userModel->find($user['user_id']);
        
        $this->setTitle('My Profile');
        $this->setData('page', 'profile');
        $this->setData('user', $userDetails);
        $this->view('auth/profile');
    }
    
    // Update user profile
    public function updateProfile() {
        requireLogin();
        
        if (!$this->isPost()) {
            $this->redirect('/auth/profile');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $user = getCurrentUser();
            $profileData = arrayOnly($this->input(), ['name', 'phone', 'email']);
            
            // Validate data
            $errors = $this->userModel->validate($profileData, $user['user_id']);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->profile();
                return;
            }
            
            // Handle profile image upload
            $profileImage = $this->files('profile_image');
            if ($profileImage && $profileImage['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleUpload('profile_image', UPLOAD_PATH . '/profiles', ALLOWED_IMAGE_TYPES);
                
                if ($uploadResult['success']) {
                    $profileData['profile_image'] = $uploadResult['filename'];
                } else {
                    $this->setFlash('warning', 'Profile updated but image upload failed: ' . $uploadResult['error']);
                }
            }
            
            // Update profile
            $updated = $this->userModel->updateUser($user['user_id'], $profileData);
            
            if ($updated) {
                // Update session data
                $updatedUser = $this->userModel->find($user['user_id']);
                $_SESSION['user'] = $updatedUser;
                
                logError("Profile updated for user: {$user['email']} (ID: {$user['user_id']})");
                $this->setFlash('success', 'Profile updated successfully');
            } else {
                $this->setFlash('error', 'Failed to update profile');
            }
            
            $this->redirect('/auth/profile');
            
        } catch (Exception $e) {
            logError("Profile update error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while updating profile');
            $this->profile();
        }
    }
}
?>