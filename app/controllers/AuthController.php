<?php
class AuthController extends Controller {
private $userModel;
    private $auth;
    
    public function __construct() {
        $this->userModel = new User();
        global $database;
        
        // Check if Auth class exists, if not create a simple auth handler
        if (class_exists('Auth')) {
            $this->auth = new Auth($database->getConnection());
        } else {
            // Fallback to basic authentication using User model
            $this->auth = $this->userModel;
        }
    }
    
    // Show login form
    public function login() {
        // Redirect if already logged in
        if (isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        
        $error = '';
        
        if ($this->isPost()) {
            $email = $this->input('email');
            $password = $this->input('password');
            
            // Check if user is active first
            if (!$this->auth->isUserActive($email)) {
                $error = "Your account is deactivated. Please contact administrator.";
            } elseif ($this->auth->login($email, $password)) {
                $this->redirectToDashboard();
                return;
            } else {
                $error = "Invalid email or password!";
            }
        }
        
        $this->setData('error', $error);
        $this->setTitle('Login - Veterinary IMS');
        $this->view('auth/login', 'auth'); // Use auth layout
    }
    
    // Handle login form submission
    public function authenticate() {
        if (!$this->isPost()) {
            $this->redirect('/login');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $email = $this->input('email');
            $password = $this->input('password');
            $remember = $this->input('remember');
            
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
            
            // Check if user is active first
            if (!$this->auth->isUserActive($email)) {
                $this->setFlash('error', 'Your account is deactivated. Please contact administrator.');
                $this->setData('old', ['email' => $email]);
                $this->login();
                return;
            }
            
            // Attempt authentication
            if ($this->auth->login($email, $password)) {
                // Handle remember me
                if ($remember) {
                    $this->setRememberMeCookie($_SESSION['user_id']);
                }
                
                // Log successful login
                logActivity("User logged in: {$_SESSION['email']} (ID: {$_SESSION['user_id']})");
                
                $this->setFlash('success', 'Welcome back, ' . ($_SESSION['first_name'] ?? $_SESSION['username']) . '!');
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
        // Log logout
        if (isset($_SESSION['email'])) {
            logActivity("User logged out: {$_SESSION['email']} (ID: {$_SESSION['user_id']})");
        }
        
        // Clear remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        // Destroy session using auth class
        $this->auth->logout();
        
        $this->setFlash('success', 'You have been logged out successfully');
        $this->redirect('/login');
    }
    
    // Show registration form (for clients)
    public function register() {
        // If already logged in, redirect to dashboard
        if (isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        
        $error = '';
        $success = '';
        $userData = [];
        
        if ($this->isPost()) {
            $userData = [
                'username' => $this->input('username'),
                'email' => $this->input('email'),
                'password' => $this->input('password'),
                'first_name' => $this->input('first_name'),
                'last_name' => $this->input('last_name'),
                'phone' => $this->input('phone'),
                'address' => $this->input('address'),
                'role' => 'client'
            ];
            
            $confirmPassword = $this->input('confirm_password');
            $terms = $this->input('terms');
            
            // Validate passwords match
            if ($userData['password'] !== $confirmPassword) {
                $error = "Passwords do not match!";
            } elseif (!$terms) {
                $error = "You must agree to the terms and conditions!";
            } else {
                // Attempt to register
                $result = $this->auth->register($userData);
                
                if ($result['success']) {
                    $success = "Registration successful! You can now login.";
                    // Clear form data
                    $userData = [];
                } else {
                    $error = $result['error'];
                }
            }
        }
        
        $this->setData('error', $error);
        $this->setData('success', $success);
        $this->setData('userData', $userData);
        $this->setTitle('Register - Veterinary IMS');
        $this->view('auth/register', 'auth'); // Use auth layout
    }
    // Show change password form
    public function changePassword() {
        requireLogin();
        
        $this->setTitle('Change Password');
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
            
            $currentPassword = $this->input('current_password');
            $newPassword = $this->input('new_password');
            $confirmPassword = $this->input('confirm_password');
            
            // Validate input
            $errors = [];
            
            if (empty($currentPassword)) {
                $errors['current_password'] = 'Current password is required';
            }
            
            if (empty($newPassword)) {
                $errors['new_password'] = 'New password is required';
            } elseif (strlen($newPassword) < 6) {
                $errors['new_password'] = 'Password must be at least 6 characters';
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
                logActivity("Password changed for user: {$user['email']} (ID: {$user['user_id']})");
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
        $this->view('auth/forgot-password', 'auth');
    }
    
    // Handle forgot password request
    public function sendResetLink() {
        if (!$this->isPost()) {
            $this->redirect('/auth/forgot-password');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $email = $this->input('email');
            
            if (empty($email)) {
                $this->setFlash('error', 'Email is required');
                $this->forgotPassword();
                return;
            }
            
            $user = $this->userModel->findBy('email', $email);
            
            if ($user) {
                // Generate reset token (simplified - in production, store in database)
                $resetToken = bin2hex(random_bytes(32));
                $resetLink = Router::url("/auth/reset-password?token={$resetToken}&email=" . urlencode($email));
                
                // In a real application, you would:
                // 1. Store the reset token in database with expiration
                // 2. Send email with reset link
                // For now, we'll just show a success message
                
                logActivity("Password reset requested for: {$email}");
            }
            
            // Always show success message for security (don't reveal if email exists)
            $this->setFlash('success', 'If an account with that email exists, a password reset link has been sent.');
            $this->redirect('/login');
            
        } catch (Exception $e) {
            logError("Forgot password error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while processing your request');
            $this->forgotPassword();
        }
    }
    
    // Check session timeout
    public function checkSession() {
        if (isLoggedIn()) {
            $lastActivity = $_SESSION['last_activity'] ?? 0;
            
            if (time() - $lastActivity > 3600) { // 1 hour timeout
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
            
            if ($user && $user['is_active'] == 1) {
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['last_activity'] = time();
                
                logActivity("User auto-logged in via remember me: {$user['email']} (ID: {$user['user_id']})");
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
            $userRole = $_SESSION['role'] ?? '';
            
            switch ($userRole) {
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
            $profileData = [
                'first_name' => $this->input('first_name'),
                'last_name' => $this->input('last_name'),
                'phone' => $this->input('phone'),
                'email' => $this->input('email')
            ];
            
            // Validate data
            $errors = $this->userModel->validate($profileData, $user['user_id']);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->profile();
                return;
            }
            
            // Update profile
            $updated = $this->userModel->updateUser($user['user_id'], $profileData);
            
            if ($updated) {
                // Update session data
                $_SESSION['first_name'] = $profileData['first_name'];
                $_SESSION['last_name'] = $profileData['last_name'];
                $_SESSION['phone'] = $profileData['phone'];
                $_SESSION['email'] = $profileData['email'];
                
                logActivity("Profile updated for user: {$user['email']} (ID: {$user['user_id']})");
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