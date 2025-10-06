<?php
require_once 'includes/init.php';

if($auth->isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
if($_POST) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Check if user is active first
    if(!$auth->isUserActive($email)) {
        $error = "Your account is deactivated. Please contact administrator.";
    } elseif($auth->login($email, $password)) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinary Health Management System - Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-red: #fd742a;
            --primary-blue: #134d60;
            --accent-yellow: #fec525;
            --light-cream: #f9f1d5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--light-cream) 0%, #ffffff 100%);
            min-height: 100vh;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(19, 77, 96, 0.1);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            min-height: 600px;
        }

        .left-panel {
            background: linear-gradient(135deg, var(--primary-blue) 0%, #0f3a47 100%);
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, var(--accent-yellow) 0%, transparent 70%);
            opacity: 0.1;
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(0.8); opacity: 0.1; }
            50% { transform: scale(1.2); opacity: 0.05; }
        }

        .logo-container {
            position: relative;
            z-index: 2;
            margin-bottom: 40px;
        }

        .logo {
            width: 200px;
            height: 200px;
            border-radius: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .system-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 15px;
            position: relative;
            z-index: 2;
        }

        .system-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.6;
            position: relative;
            z-index: 2;
        }

        .right-panel {
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-title {
            color: var(--primary-blue);
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .login-subtitle {
            color: #666;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            color: var(--primary-blue);
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            border: none;
            border-bottom: 2px solid #e0e0e0;
            border-radius: 0;
            padding: 15px 0;
            font-size: 1rem;
            background: transparent;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: none;
            border-bottom-color: var(--primary-red);
            background: transparent;
        }

        .form-control::placeholder {
            color: #aaa;
        }

        .input-icon {
            position: absolute;
            right: 10px;
            top: 40px;
            color: #aaa;
            transition: color 0.3s ease;
        }

        .form-control:focus + .input-icon {
            color: var(--primary-red);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-red) 0%, #e85d0f 100%);
            border: none;
            padding: 15px 0;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(253, 116, 42, 0.3);
            color: white;
        }

        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }

        .forgot-password a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--primary-red);
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-top: 15px;
        }

        .form-check-input:checked {
            background-color: var(--primary-red);
            border-color: var(--primary-red);
        }

        .form-check-label {
            color: #666;
            margin-left: 8px;
        }

        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .demo-credentials {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            border-left: 4px solid var(--accent-yellow);
        }

        .demo-credentials h6 {
            color: var(--primary-blue);
            margin-bottom: 10px;
        }

        .register-btn {
            background: transparent;
            border: 2px solid var(--primary-blue);
            color: var(--primary-blue);
            padding: 12px 0;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 15px;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .register-btn:hover {
            background: var(--primary-blue);
            color: white;
        }

        @media (max-width: 768px) {
            .login-card {
                margin: 20px;
            }
            
            .left-panel {
                padding: 40px 30px;
            }
            
            .right-panel {
                padding: 40px 30px;
            }
            
            .system-title {
                font-size: 1.5rem;
            }
            
            .login-title {
                font-size: 1.8rem;
            }
        }

        .feature-list {
            margin-top: 30px;
            position: relative;
            z-index: 2;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .feature-item i {
            color: var(--accent-yellow);
            margin-right: 12px;
            width: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid login-container">
        <div class="login-card">
            <div class="row g-0 h-100">
                <!-- Left Panel -->
                <div class="col-md-6 left-panel">
                    <div class="logo-container">
                        <div class="logo">
                            <img src="img/logo.png" alt="VetCare Pro Logo" style="width:350px; height:350px; object-fit:contain;">
                        </div>
                        <h1 class="system-title">VetCare</h1>
                        <p class="system-subtitle">
                            Comprehensive Digital Health Record IMS
                        </p>
                    </div>
                    
                    <div class="feature-list">
                        <div class="feature-item">
                            <i class="fas fa-users"></i>
                            <span>Client & Animal Records Management</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-syringe"></i>
                            <span>Treatment & Vaccination Tracking</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-calendar-check"></i>
                            <span>Appointment Scheduling</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-file-medical"></i>
                            <span>Medical History & Reports</span>
                        </div>
                    </div>
                </div>

                <!-- Right Panel -->
                <div class="col-md-6 right-panel">
                    <div class="login-header">
                        <h2 class="login-title">Welcome</h2>
                        <p class="login-subtitle">Please sign in to your account</p>
                    </div>

                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" id="loginForm">
                        <div class="form-group">
                            <label class="form-label" for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            <i class="fas fa-lock input-icon"></i>
                        </div>

                        <div class="remember-me">
                            <input class="form-check-input" type="checkbox" id="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>

                        <button type="submit" class="btn btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Sign In
                        </button>

                        <div class="forgot-password">
                            <a href="#" id="forgotPassword">Forgot your password?</a>
                        </div>

                        <a href="register.php" class="register-btn">
                            <i class="fas fa-user-plus me-2"></i>
                            Register as Client
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            // Form validation is handled by PHP backend
            // This is just for UI feedback
            const submitBtn = document.querySelector('.btn-login');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';
            submitBtn.disabled = true;
            
            // Re-enable button after 3 seconds if there's an issue
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });

        document.getElementById('forgotPassword').addEventListener('click', function(e) {
            e.preventDefault();
            alert('Password reset functionality would be implemented here.');
        });

        // Add smooth focus animations
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
    </script>
</body>
</html>