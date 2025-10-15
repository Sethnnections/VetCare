<?php
// This file should ONLY contain the registration form HTML and PHP logic
// NO layout declaration, NO full HTML structure
?>

<div class="container-fluid registration-container">
    <div class="registration-card">
        <div class="row g-0 h-100">
            <!-- Left Panel -->
            <div class="col-md-5 left-panel">
                <div class="logo-container">
                    <div class="logo">
                        <div style="width:200px; height:200px; background: #fec525; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
                            <i class="fas fa-paw" style="font-size: 80px; color: #134d60;"></i>
                        </div>
                    </div>
                    <h1 class="system-title">VetCare</h1>
                    <p class="system-subtitle">
                        Client Registration Portal
                    </p>
                </div>
                
                <div class="feature-list">
                    <div class="feature-item">
                        <i class="fas fa-paw"></i>
                        <span>Register your animals</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-calendar-check"></i>
                        <span>Book appointments online</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-file-medical"></i>
                        <span>Access medical records</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-bell"></i>
                        <span>Get vaccination reminders</span>
                    </div>
                </div>
            </div>

            <!-- Right Panel -->
            <div class="col-md-7 right-panel">
                <div class="registration-header">
                    <h2 class="registration-title">Client Registration</h2>
                    <p class="registration-subtitle">Create your account to manage your animals</p>
                </div>

                <!-- Flash Messages -->
                <?php if ($flash = getFlashMessage()): ?>
                    <div class="alert alert-<?php echo $flash['type']; ?>">
                        <?php echo htmlspecialchars($flash['message']); ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($error) && $error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if(isset($success) && $success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="POST" action="<?php echo url('/auth/register'); ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="first_name">First Name *</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="<?php echo $userData['first_name'] ?? ''; ?>" 
                                   placeholder="Enter your first name" required>
                            <i class="fas fa-user input-icon"></i>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="last_name">Last Name *</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="<?php echo $userData['last_name'] ?? ''; ?>" 
                                   placeholder="Enter your last name" required>
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="username">Username *</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo $userData['username'] ?? ''; ?>" 
                               placeholder="Choose a username" required>
                        <i class="fas fa-user input-icon"></i>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email Address *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo $userData['email'] ?? ''; ?>" 
                               placeholder="Enter your email" required>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number *</label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               value="<?php echo $userData['phone'] ?? ''; ?>" 
                               placeholder="Enter your phone number" required>
                        <i class="fas fa-phone input-icon"></i>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="password">Password *</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Create a password" required>
                            <i class="fas fa-lock input-icon"></i>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="confirm_password">Confirm Password *</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                   placeholder="Confirm your password" required>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" 
                                  placeholder="Enter your address" 
                                  rows="2"><?php echo $userData['address'] ?? ''; ?></textarea>
                        <i class="fas fa-home input-icon"></i>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> and <a href="#" class="text-decoration-none">Privacy Policy</a>
                        </label>
                    </div>

                    <!-- Hidden fields -->
                    <input type="hidden" name="role" value="<?php echo ROLE_CLIENT; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <button type="submit" class="btn btn-register">
                        <i class="fas fa-user-plus me-2"></i>
                        Create Account
                    </button>

                    <div class="login-link">
                        <p>Already have an account? <a href="<?php echo url('/login'); ?>">Sign in here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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

.registration-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.registration-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(19, 77, 96, 0.1);
    overflow: hidden;
    max-width: 1200px;
    width: 100%;
    min-height: 700px;
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
    padding: 40px 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.registration-header {
    text-align: center;
    margin-bottom: 30px;
}

.registration-title {
    color: var(--primary-blue);
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.registration-subtitle {
    color: #666;
    font-size: 1rem;
}

.form-group {
    margin-bottom: 20px;
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
    padding: 12px 0;
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

.btn-register {
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

.btn-register:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(253, 116, 42, 0.3);
    color: white;
}

.form-check-input:checked {
    background-color: var(--primary-red);
    border-color: var(--primary-red);
}

.form-check-label {
    color: #666;
    margin-left: 8px;
}

.login-link {
    text-align: center;
    margin-top: 20px;
}

.login-link a {
    color: var(--primary-blue);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.login-link a:hover {
    color: var(--primary-red);
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

.form-row {
    display: flex;
    gap: 15px;
}

.form-row .form-group {
    flex: 1;
}

.alert {
    border-radius: 10px;
    border: none;
    padding: 15px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .registration-card {
        margin: 20px;
    }
    
    .left-panel {
        padding: 40px 30px;
    }
    
    .right-panel {
        padding: 30px 25px;
    }
    
    .system-title {
        font-size: 1.5rem;
    }
    
    .registration-title {
        font-size: 1.8rem;
    }
    
    .form-row {
        flex-direction: column;
        gap: 0;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password confirmation validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match. Please check and try again.');
            document.getElementById('password').focus();
            return;
        }
        
        if (password.length < <?php echo PASSWORD_MIN_LENGTH; ?>) {
            e.preventDefault();
            alert('Password must be at least <?php echo PASSWORD_MIN_LENGTH; ?> characters long.');
            document.getElementById('password').focus();
            return;
        }

        // Show loading state
        const submitBtn = document.querySelector('.btn-register');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Account...';
        submitBtn.disabled = true;
        
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 3000);
    });

    // Phone number formatting
    document.getElementById('phone').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.startsWith('265')) {
            value = '+' + value;
        } else if (value.startsWith('0')) {
            value = '+265' + value.substring(1);
        }
        e.target.value = value;
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

    // Real-time password strength check
    document.getElementById('password').addEventListener('input', function(e) {
        const password = e.target.value;
        const strengthIndicator = document.getElementById('password-strength') || createStrengthIndicator();
        
        let strength = 'Weak';
        let color = '#dc3545';
        
        if (password.length >= 8) {
            strength = 'Medium';
            color = '#fd7e14';
        }
        
        if (password.length >= 12 && /[A-Z]/.test(password) && /[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password)) {
            strength = 'Strong';
            color = '#198754';
        }
        
        strengthIndicator.textContent = `Password strength: ${strength}`;
        strengthIndicator.style.color = color;
    });

    function createStrengthIndicator() {
        const indicator = document.createElement('div');
        indicator.id = 'password-strength';
        indicator.style.fontSize = '0.875rem';
        indicator.style.marginTop = '5px';
        document.getElementById('password').parentNode.appendChild(indicator);
        return indicator;
    }
});
</script>