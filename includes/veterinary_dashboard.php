<?php
require_once 'includes/init.php';
requireLogin();
requireRole(['admin']);

$current_role = $auth->getUserRole();
$user_id = $_SESSION['user_id'];

$message = '';
$error = '';

if($_POST) {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = 'veterinary';

    // Validation
    if(empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } elseif($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters long!";
    } else {
        // Check if email already exists
        $user = new User($db);
        $user->email = $email;
        if($user->emailExists()) {
            $error = "Email already exists!";
        } else {
            // Register veterinary staff
            if($auth->registerUser($username, $email, $password, $role)) {
                $message = "Veterinary staff registered successfully!";
                // Clear form fields
                $_POST = array();
            } else {
                $error = "Registration failed! Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Veterinary - Veterinary IMS</title>
    <!-- Bootstrap CSS -->
      <link rel="shortcut icon" type="image/x-icon" href="img/favicon.png">
    <!-- Normalize CSS -->
    <link rel="stylesheet" href="css/normalize.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="css/main.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="css/all.min.css">
    <!-- Flaticon CSS -->
    <link rel="stylesheet" href="fonts/flaticon.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <!-- Modernize js -->
    <script src="js/modernizr-3.6.0.min.js"></script>
    <style>
        :root {
            --primary: #112954;
            --primary-light: #112954;
            --secondary: #212529;
            --success: #28a745;
            --danger: #f72585;
            --warning: #e86029;
            --info: #17a2b8;
            --light: #f8f9fa;
            --dark: #071329ff;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --orange-deep: #e86029;
            --orange-light: #ff8c5a;
            --border-radius: 12px;
            --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .registration-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
            margin: 2rem 0;
        }
        
        .registration-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .registration-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .registration-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .registration-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
            color: var(--orange-deep);
        }
        
        .registration-body {
            padding: 2rem;
        }
        
        .form-control {
            border-radius: 8px;
            border: 1px solid var(--light-gray);
            padding: 0.75rem 1rem;
            transition: var(--transition);
        }
        
        .form-control:focus {
            border-color: var(--orange-deep);
            box-shadow: 0 0 0 0.2rem rgba(232, 96, 41, 0.25);
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        .btn {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--orange-deep), var(--orange-light));
            border: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(232, 96, 41, 0.4);
            background: linear-gradient(to right, var(--orange-light), var(--orange-deep));
        }
        
        .btn-outline-secondary {
            border: 1px solid var(--gray);
            color: var(--gray);
        }
        
        .btn-outline-secondary:hover {
            background-color: var(--gray);
            color: white;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem 1.25rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border-left: 4px solid var(--success);
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f1b0b7 100%);
            color: #721c24;
            border-left: 4px solid var(--danger);
        }
        
        .alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
            border-left: 4px solid var(--warning);
        }
        
        .password-strength {
            height: 5px;
            border-radius: 5px;
            margin-top: 5px;
            transition: var(--transition);
        }
        
        .password-weak {
            background-color: var(--danger);
            width: 25%;
        }
        
        .password-medium {
            background-color: var(--warning);
            width: 50%;
        }
        
        .password-strong {
            background-color: #4CAF50;
            width: 100%;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
        }
        
        .feature-list li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
        }
        
        .feature-list li i {
            color: var(--orange-deep);
            margin-right: 0.75rem;
        }
        
        .steps-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .steps-indicator::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: var(--light-gray);
            z-index: 1;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }
        
        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: white;
            border: 2px solid var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 0.5rem;
            transition: var(--transition);
        }
        
        .step.active .step-number {
            background-color: var(--orange-deep);
            border-color: var(--orange-deep);
            color: white;
        }
        
        .step-label {
            font-size: 0.85rem;
            color: var(--gray);
            text-align: center;
        }
        
        .step.active .step-label {
            color: var(--orange-deep);
            font-weight: 600;
        }
        
        .input-group-text {
            background-color: white;
            border-right: none;
        }
        
        .input-group .form-control {
            border-left: none;
        }
        
        .input-group .form-control:focus {
            border-color: var(--light-gray);
        }
        
        .input-group:focus-within .input-group-text {
            border-color: var(--orange-deep);
            background-color: rgba(232, 96, 41, 0.05);
        }
        
        .input-group:focus-within .form-control {
            border-color: var(--orange-deep);
        }
        
        .icon-orange {
            color: var(--orange-deep);
        }
        
        .form-text {
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
        
        .password-match {
            color: var(--success);
        }
        
        .password-mismatch {
            color: var(--danger);
        }
        
        li {
            color: #fff;
        }
        
        @media (max-width: 768px) {
            .registration-wrapper {
                padding: 1rem;
            }
            
            .registration-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Preloader Start Here -->
    <div id="preloader"></div>
    <!-- Preloader End Here -->
    
    <div id="wrapper" class="wrapper">
        <!-- Header Area Start Here -->
        <?php include 'includes/header.php'; ?>
        <!-- Header Area End Here -->
        
        <!-- Page Area Start Here -->
        <div class="dashboard-page-one">
            <!-- Sidebar Area Start Here -->
            <?php include 'includes/sidebar.php'; ?>
            <!-- Sidebar Area End Here -->
            
            <div class="dashboard-content-one">
                <!-- Breadcubs Area Start Here -->
                <div class="breadcrumbs-area">
                    <h3>Register Veterinary Staff</h3>
                    <ul>
                        <li>
                            <a href="dashboard.php">Home</a>
                        </li>
                        <li>
                            <a href="users.php">User Management</a>
                        </li>
                        <li>Register Veterinary</li>
                    </ul>
                </div>
                <!-- Breadcubs Area End Here -->

                <!-- Registration Content Area Start Here -->
                <div class="registration-wrapper">
                    <div class="container-fluid">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="registration-card">
                                    <div class="row g-0" style="color: #fff;">
                                        <div class="col-lg-5 d-none d-lg-block">
                                            <div class="registration-header h-100 d-flex flex-column justify-content-center">
                                                <i class="bi bi-person-plus-fill registration-icon"></i>
                                                <h2 style="color: #fff;">Add Veterinary Staff</h2>
                                                <p class="mt-2" style="color: #fff;">Register a new veterinary professional to your team</p>
                                                
                                                <div class="mt-4 text-start " style="color: #fff;">
                                                    <h5 class="mb-3" style="color: #fff;">Veterinary Staff Can:</h5>
                                                    <ul class="feature-list">
                                                        <li>
                                                            <i class="bi bi-check-circle-fill"></i>
                                                            Manage animal records
                                                        </li>
                                                        <li>
                                                            <i class="bi bi-check-circle-fill"></i>
                                                            Create treatment plans
                                                        </li>
                                                        <li>
                                                            <i class="bi bi-check-circle-fill"></i>
                                                            Schedule appointments
                                                        </li>
                                                        <li>
                                                            <i class="bi bi-check-circle-fill"></i>
                                                            Access medicine inventory
                                                        </li>
                                                        <li>
                                                            <i class="bi bi-check-circle-fill"></i>
                                                            View medical history
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="registration-body">
                                                <!-- Steps Indicator -->
                                                <div class="steps-indicator">
                                                    <div class="step active">
                                                        <div class="step-number">1</div>
                                                        <div class="step-label">Account Details</div>
                                                    </div>
                                                    <div class="step">
                                                        <div class="step-number">2</div>
                                                        <div class="step-label">Profile Setup</div>
                                                    </div>
                                                    <div class="step">
                                                        <div class="step-number">3</div>
                                                        <div class="step-label">Confirmation</div>
                                                    </div>
                                                </div>
                                                
                                                <h4 class="mb-4">Create Veterinary Staff Account</h4>
                                                
                                                <?php if($message): ?>
                                                    <div class="alert alert-success d-flex align-items-center" role="alert">
                                                        <i class="bi bi-check-circle-fill me-2 icon-orange"></i>
                                                        <div>
                                                            <h5 class="alert-heading mb-1">Success!</h5>
                                                            <?php echo $message; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if($error): ?>
                                                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                                        <div>
                                                            <h5 class="alert-heading mb-1">Registration Error</h5>
                                                            <?php echo $error; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <form method="POST" id="registrationForm">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="username" class="form-label">Username</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text">
                                                                        <i class="bi bi-person icon-orange"></i>
                                                                    </span>
                                                                    <input type="text" class="form-control" id="username" name="username" 
                                                                           value="<?php echo $_POST['username'] ?? ''; ?>" 
                                                                           placeholder="Enter username" required>
                                                                </div>
                                                                <div class="form-text">Choose a unique username for the veterinary staff</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="email" class="form-label">Email Address</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text">
                                                                        <i class="bi bi-envelope icon-orange"></i>
                                                                    </span>
                                                                    <input type="email" class="form-control" id="email" name="email" 
                                                                           value="<?php echo $_POST['email'] ?? ''; ?>" 
                                                                           placeholder="Enter email address" required>
                                                                </div>
                                                                <div class="form-text">A valid email address for communication</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="password" class="form-label">Password</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text">
                                                                        <i class="bi bi-lock icon-orange"></i>
                                                                    </span>
                                                                    <input type="password" class="form-control" id="password" name="password" 
                                                                           placeholder="Enter password" required>
                                                                </div>
                                                                <div class="password-strength" id="passwordStrength"></div>
                                                                <div class="form-text">Password must be at least 6 characters long.</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text">
                                                                        <i class="bi bi-lock-fill icon-orange"></i>
                                                                    </span>
                                                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                                                           placeholder="Confirm password" required>
                                                                </div>
                                                                <div class="form-text" id="passwordMatchText"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-grid gap-2 mt-4">
                                                        <button type="submit" class="btn btn-primary btn-lg">
                                                            <i class="bi bi-person-plus me-2"></i> Register Veterinary Staff
                                                        </button>
                                                    </div>
                                                    
                                                    <div class="mt-3 text-center">
                                                        <a href="users.php" class="btn btn-outline-secondary">
                                                            <i class="bi bi-arrow-left me-2"></i> Back to User Management
                                                        </a>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Registration Content Area End Here -->

                <!-- Footer Area Start Here -->
                <footer class="footer-wrap-layout1">
                    <div class="copyright">© Copyrights <a href="#">Veterinary IMS</a> 2025. All rights reserved. Veterinary Public Health Laboratory, Blantyre</div>
                </footer>
                <!-- Footer Area End Here -->
            </div>
        </div>
        <!-- Page Area End Here -->
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jquery-->
    <script src="js/jquery-3.3.1.min.js"></script>
    <!-- Plugins js -->
    <script src="js/plugins.js"></script>
    <!-- Popper js -->
    <script src="js/popper.min.js"></script>
    <!-- Bootstrap js -->
    <script src="js/bootstrap.min.js"></script>
    <!-- Custom Js -->

    
    <!-- Custom Scripts -->
    <script>
        // Immediate preloader fix
        (function() {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                // Hide preloader after very short delay
                setTimeout(() => {
                    preloader.style.opacity = '0';
                    setTimeout(() => {
                        preloader.style.display = 'none';
                    }, 300);
                }, 100);
            }
        })();
        
        // Password strength indicator
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const passwordStrength = document.getElementById('passwordStrength');
            const passwordMatchText = document.getElementById('passwordMatchText');
            
            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;
                    
                    if (password.length >= 6) strength += 1;
                    if (password.length >= 8) strength += 1;
                    if (/[A-Z]/.test(password)) strength += 1;
                    if (/[0-9]/.test(password)) strength += 1;
                    if (/[^A-Za-z0-9]/.test(password)) strength += 1;
                    
                    // Reset classes
                    passwordStrength.className = 'password-strength';
                    
                    if (password.length === 0) {
                        passwordStrength.style.width = '0%';
                    } else if (strength <= 2) {
                        passwordStrength.classList.add('password-weak');
                    } else if (strength <= 4) {
                        passwordStrength.classList.add('password-medium');
                    } else {
                        passwordStrength.classList.add('password-strong');
                    }
                });
            }
            
            // Password confirmation validation
            if (confirmPasswordInput && passwordInput) {
                function validatePassword() {
                    if (passwordInput.value !== confirmPasswordInput.value) {
                        confirmPasswordInput.setCustomValidity("Passwords don't match");
                        passwordMatchText.innerHTML = '<span class="password-mismatch"><i class="bi bi-x-circle-fill me-1"></i>Passwords do not match</span>';
                        confirmPasswordInput.classList.add('is-invalid');
                        confirmPasswordInput.classList.remove('is-valid');
                    } else {
                        confirmPasswordInput.setCustomValidity('');
                        if (confirmPasswordInput.value.length > 0) {
                            passwordMatchText.innerHTML = '<span class="password-match"><i class="bi bi-check-circle-fill me-1"></i>Passwords match</span>';
                            confirmPasswordInput.classList.remove('is-invalid');
                            confirmPasswordInput.classList.add('is-valid');
                        } else {
                            passwordMatchText.innerHTML = '';
                            confirmPasswordInput.classList.remove('is-invalid');
                            confirmPasswordInput.classList.remove('is-valid');
                        }
                    }
                }
                
                passwordInput.addEventListener('change', validatePassword);
                confirmPasswordInput.addEventListener('keyup', validatePassword);
            }
            
            // Form validation
            const form = document.getElementById('registrationForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const password = document.getElementById('password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;
                    
                    if (password !== confirmPassword) {
                        e.preventDefault();
                        // Create a more stylish alert
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-danger d-flex align-items-center mt-3';
                        alertDiv.innerHTML = `
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div>
                                <h5 class="alert-heading mb-1">Password Mismatch</h5>
                                Passwords do not match. Please check and try again.
                            </div>
                        `;
                        
                        // Insert after the form
                        form.parentNode.insertBefore(alertDiv, form.nextSibling);
                        
                        // Remove alert after 5 seconds
                        setTimeout(() => {
                            alertDiv.remove();
                        }, 5000);
                        
                        return false;
                    }
                    
                    if (password.length < 6) {
                        e.preventDefault();
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-warning d-flex align-items-center mt-3';
                        alertDiv.innerHTML = `
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div>
                                <h5 class="alert-heading mb-1">Password Too Short</h5>
                                Password must be at least 6 characters long.
                            </div>
                        `;
                        
                        form.parentNode.insertBefore(alertDiv, form.nextSibling);
                        
                        setTimeout(() => {
                            alertDiv.remove();
                        }, 5000);
                        
                        return false;
                    }
                    
                    return true;
                });
            }
            
            // Add real-time validation indicators
            const inputs = document.querySelectorAll('input[required]');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.value.trim() === '') {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                });
            });
        });
    </script>
</body>
</html>