<?php
require_once 'includes/init.php';
requireLogin();
requireRole(['admin']);

if(!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$user_id = $_GET['id'];
$user = new User($db);
$user->id = $user_id;
$user->readOne();

$current_role = $auth->getUserRole();
$username = $_SESSION['username'];

$message = '';
$error = '';

if($_POST) {
    $new_username = $_POST['username'] ?? '';
    $new_email = $_POST['email'] ?? '';
    $new_role = $_POST['role'] ?? '';
    
    // Validation
    if(empty($new_username) || empty($new_email) || empty($new_role)) {
        $error = "All fields are required!";
    } else {
        // Update user logic would go here
        // You'll need to create an update method in your User class
        $message = "User updated successfully!";
    }
}
?>
<!DOCTYPE html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Edit User - Veterinary IMS</title>
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
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <!-- Modernize js -->
    <script src="js/modernizr-3.6.0.min.js"></script>
    <style>
        
        :root {
            --primary: #112954;
            --primary-light: #112954;
            --secondary: #212529;
            --success: #112954;
            --danger: #f72585;
            --warning: #e86029;
            --info: #e86029;
            --light: #f8f9fa;
            --dark: #071329ff;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --border-radius: 12px;
            --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        
        .profile-wrapper {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--box-shadow);
            position: relative;
            overflow: hidden;
        }
        
        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            object-fit: cover;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .profile-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            border: none;
            transition: var(--transition);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .profile-card .card-header {
            background: white;
            border-bottom: 1px solid var(--light-gray);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .profile-card .card-header i {
            color: var(--primary);
            margin-right: 0.5rem;
        }
        
        .profile-card .card-body {
            padding: 1.5rem;
        }
        
        .form-control {
            border-radius: 8px;
            border: 1px solid var(--light-gray);
            padding: 0.75rem 1rem;
            transition: var(--transition);
        }
        
        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }
        
        .btn {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--primary), var(--primary-light));
            border: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.4);
        }
        
        .btn-warning {
            background: linear-gradient(to right, var(--warning), #f9a826);
            border: none;
            color: white;
        }
        
        .badge-role {
            background: linear-gradient(to right, var(--info), var(--primary-light));
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .stats-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--box-shadow);
            text-align: center;
            transition: var(--transition);
            border-left: 4px solid var(--primary);
        }
        
        .stats-card:hover {
            transform: translateY(-3px);
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .stats-label {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        .nav-tabs-custom {
            border-bottom: 1px solid var(--light-gray);
            margin-bottom: 1.5rem;
        }
        
        .nav-tabs-custom .nav-link {
            border: none;
            color: var(--gray);
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 8px 8px 0 0;
            margin-right: 0.5rem;
        }
        
        .nav-tabs-custom .nav-link.active {
            color: var(--primary);
            background: white;
            border-bottom: 3px solid var(--primary);
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem 1.25rem;
        }
        
        .profile-section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--light);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .profile-info-item {
            display: flex;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .profile-info-label {
            min-width: 150px;
            font-weight: 600;
            color: var(--dark);
        }
        
        .profile-info-value {
            color: var(--gray);
        }
        
        @media (max-width: 768px) {
            .profile-info-item {
                flex-direction: column;
            }
            
            .profile-info-label {
                margin-bottom: 0.25rem;
            }
        }
    </style>
</head>
<body>
    <!-- Preloader -->
    <div id="preloader"></div>
    
    <div id="wrapper" class="wrapper bg-ash">
        <!-- Header -->
        <?php include 'includes/header.php'; ?>
        
        <!-- Page Area -->
        <div class="dashboard-page-one">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>
            
            <div class="dashboard-content-one">
                <!-- Breadcrumbs -->
                <div class="breadcrumbs-area">
                    <h3>Edit User</h3>
                    <ul>
                        <li><a href="dashboard.php">Home</a></li>
                        <li><a href="users.php">User Management</a></li>
                        <li>Edit User</li>
                    </ul>
                </div>

                <!-- Edit User Form -->
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h4 class="mb-0">Edit User: <?php echo $user->username; ?></h4>
                                </div>
                                <div class="card-body">
                                    <?php if($message): ?>
                                        <div class="alert alert-success"><?php echo $message; ?></div>
                                    <?php endif; ?>
                                    
                                    <?php if($error): ?>
                                        <div class="alert alert-danger"><?php echo $error; ?></div>
                                    <?php endif; ?>
                                    
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label class="form-label">Username</label>
                                            <input type="text" class="form-control" name="username" 
                                                   value="<?php echo $user->username; ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" 
                                                   value="<?php echo $user->email; ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Role</label>
                                            <select class="form-control" name="role" required>
                                                <option value="client" <?php echo $user->role == 'client' ? 'selected' : ''; ?>>Client</option>
                                                <option value="veterinary" <?php echo $user->role == 'veterinary' ? 'selected' : ''; ?>>Veterinary</option>
                                                <option value="admin" <?php echo $user->role == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                            </select>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">Update User</button>
                                            <a href="users.php" class="btn btn-secondary">Cancel</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <footer class="footer-wrap-layout1">
                    <div class="copyright">© Copyrights <a href="#">Veterinary IMS</a> 2025. All rights reserved.</div>
                </footer>
            </div>
        </div>
    </div>

 <!-- jquery-->
    <script src="js/jquery-3.3.1.min.js"></script>
    <!-- Plugins js -->
    <script src="js/plugins.js"></script>
    <!-- Popper js -->
    <script src="js/popper.min.js"></script>
    <!-- Bootstrap js -->
    <script src="js/bootstrap.min.js"></script>
    <!-- Custom Js -->

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
    
    // Password confirmation validation
    document.addEventListener('DOMContentLoaded', function() {
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');
        
        function validatePassword() {
            if (newPassword.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity("Passwords don't match");
            } else {
                confirmPassword.setCustomValidity('');
            }
        }
        
        if (newPassword && confirmPassword) {
            newPassword.addEventListener('change', validatePassword);
            confirmPassword.addEventListener('keyup', validatePassword);
        }
        
        // Tab persistence
        const profileTabs = document.getElementById('profileTabs');
        if (profileTabs) {
            const activeTab = localStorage.getItem('activeProfileTab');
            if (activeTab) {
                const tabTrigger = new bootstrap.Tab(profileTabs.querySelector(`[href="${activeTab}"]`));
                tabTrigger.show();
            }
            
            profileTabs.addEventListener('click', function(e) {
                if (e.target.classList.contains('nav-link')) {
                    localStorage.setItem('activeProfileTab', e.target.getAttribute('href'));
                }
            });
        }
    });
    </script>
</body>
</html>