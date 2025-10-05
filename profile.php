<?php
require_once 'includes/init.php';
requireLogin();

$current_role = $auth->getUserRole();
$user_id = $_SESSION['user_id'];

// Initialize user object
$user = new User($db);
$user->id = $user_id;

// Get user profile data
$user->getProfile();

// Handle form submission
$update_success = false;
$update_errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update profile information
    if (isset($_POST['update_profile'])) {
        $user->first_name = $_POST['first_name'] ?? '';
        $user->last_name = $_POST['last_name'] ?? '';
        $user->phone = $_POST['phone'] ?? '';
        $user->address = $_POST['address'] ?? '';
        
        if ($user->updateProfile()) {
            $update_success = true;
            // Refresh user data
            $user->getProfile();
        } else {
            $update_errors[] = "Failed to update profile. Please try again.";
        }
    }
    
    // Update password
    if (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validate current password
        $temp_user = new User($db);
        $temp_user->email = $user->email;
        if ($temp_user->emailExists() && password_verify($current_password, $temp_user->password)) {
            // Validate new password
            if ($new_password === $confirm_password) {
                if (strlen($new_password) >= 6) {
                    if ($user->updatePassword($new_password)) {
                        $update_success = true;
                        $update_errors[] = "Password updated successfully!";
                    } else {
                        $update_errors[] = "Failed to update password. Please try again.";
                    }
                } else {
                    $update_errors[] = "New password must be at least 6 characters long.";
                }
            } else {
                $update_errors[] = "New passwords do not match.";
            }
        } else {
            $update_errors[] = "Current password is incorrect.";
        }
    }
}
?>

<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Veterinary IMS | My Profile</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
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

<body class="profile-wrapper">
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
                    <h3>My Profile</h3>
                    <ul>
                        <li>
                            <a href="dashboard.php">Home</a>
                        </li>
                        <li>Profile</li>
                    </ul>
                </div>
                <!-- Breadcubs Area End Here -->

                <!-- Profile Content Area Start Here -->
                <div class="container-fluid">
                    <!-- Profile Header -->
                    <div class="profile-header" style="color: #fff;">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center text-md-left">
                                <img src="<?php echo !empty($user->profile_picture) ? $user->profile_picture : 'img/figure/admin.jpg'; ?>" 
                                     alt="Profile Picture" class="profile-avatar">
                            </div>
                            <div class="col-md-7 mt-3 mt-md-0" style="color: #fff;">
                                <h2 style="color: #fff;" class="mb-1"><?php echo htmlspecialchars($user->first_name . ' ' . $user->last_name); ?></h2>
                                <p class="mb-1" style="color: #fff;">@<?php echo htmlspecialchars($user->username); ?></p>
                                <span class="badge-role"><?php echo ucfirst($user->role); ?></span>
                                <p class="mt-2 mb-0">
                                    <small style="color: #fff;">
                                        <i class="far fa-envelope mr-1"></i> <?php echo htmlspecialchars($user->email); ?> 
                                        | <i class="far fa-calendar-alt mr-1 ml-2"></i> Member since: <?php echo date('M Y', strtotime($user->created_at)); ?>
                                    </small>
                                </p>
                            </div>
                            <div class="col-md-3 text-center text-md-right mt-3 mt-md-0">
                                <button class="btn btn-light mr-2">
                                    <i class="fas fa-camera mr-1"></i> Change Photo
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Stats -->
                    <?php if($current_role != 'client'): ?>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h4 class="profile-section-title">Quick Overview</h4>
                        </div>
                        <?php if($current_role == 'admin'): ?>
                        <div class="col-md-3 col-sm-6">
                            <div class="stats-card">
                                <div class="stats-number">--</div>
                                <div class="stats-label">Total Users</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="stats-card">
                                <div class="stats-number">--</div>
                                <div class="stats-label">Active Animals</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="stats-card">
                                <div class="stats-number">--</div>
                                <div class="stats-label">Today's Appointments</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="stats-card">
                                <div class="stats-number">--</div>
                                <div class="stats-label">Pending Tasks</div>
                            </div>
                        </div>
                        <?php elseif($current_role == 'veterinary'): ?>
                        <div class="col-md-3 col-sm-6">
                            <div class="stats-card">
                                <div class="stats-number">--</div>
                                <div class="stats-label">Today's Appointments</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="stats-card">
                                <div class="stats-number">--</div>
                                <div class="stats-label">Pending Treatments</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="stats-card">
                                <div class="stats-number">--</div>
                                <div class="stats-label">Medicines Low Stock</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="stats-card">
                                <div class="stats-number">--</div>
                                <div class="stats-label">Completed This Week</div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Tab Navigation -->
                            <ul class="nav nav-tabs nav-tabs-custom" id="profileTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab">
                                        <i class="fas fa-user-circle mr-1"></i> Profile Info
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="password-tab" data-toggle="tab" href="#password" role="tab">
                                        <i class="fas fa-lock mr-1"></i> Change Password
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="role-tab" data-toggle="tab" href="#role" role="tab">
                                        <i class="fas fa-user-tag mr-1"></i> Role Information
                                    </a>
                                </li>
                            </ul>
                            
                            <!-- Tab Content -->
                            <div class="tab-content" id="profileTabsContent">
                                <!-- Profile Information Tab -->
                                <div class="tab-pane fade show active" id="profile" role="tabpanel">
                                    <div class="profile-card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="fas fa-user-edit mr-2"></i>Edit Profile Information</h5>
                                        </div>
                                        <div class="card-body">
                                            <?php if($update_success && empty($update_errors)): ?>
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <i class="fas fa-check-circle mr-2"></i>Profile updated successfully!
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <?php if(!empty($update_errors)): ?>
                                            <?php foreach($update_errors as $error): ?>
                                            <div class="alert alert-<?php echo strpos($error, 'successfully') !== false ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                                                <i class="fas <?php echo strpos($error, 'successfully') !== false ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> mr-2"></i>
                                                <?php echo $error; ?>
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                            
                                            <form method="POST" action="">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="first_name">First Name</label>
                                                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                                                   value="<?php echo htmlspecialchars($user->first_name ?? ''); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="last_name">Last Name</label>
                                                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                                                   value="<?php echo htmlspecialchars($user->last_name ?? ''); ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="username">Username</label>
                                                            <input type="text" class="form-control" id="username" 
                                                                   value="<?php echo htmlspecialchars($user->username); ?>" readonly>
                                                            <small class="form-text text-muted">Username cannot be changed</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="email">Email</label>
                                                            <input type="email" class="form-control" id="email" 
                                                                   value="<?php echo htmlspecialchars($user->email); ?>" readonly>
                                                            <small class="form-text text-muted">Email cannot be changed</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="phone">Phone</label>
                                                            <input type="text" class="form-control" id="phone" name="phone" 
                                                                   value="<?php echo htmlspecialchars($user->phone ?? ''); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="role">Role</label>
                                                            <input type="text" class="form-control" id="role" 
                                                                   value="<?php echo ucfirst($user->role); ?>" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="address">Address</label>
                                                    <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($user->address ?? ''); ?></textarea>
                                                </div>
                                                
                                                <button type="submit" name="update_profile" class="btn btn-primary">
                                                    <i class="fas fa-save mr-1"></i> Update Profile
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Password Tab -->
                                <div class="tab-pane fade" id="password" role="tabpanel">
                                    <div class="profile-card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="fas fa-lock mr-2"></i>Change Password</h5>
                                        </div>
                                        <div class="card-body">
                                            <form method="POST" action="">
                                                <div class="form-group">
                                                    <label for="current_password">Current Password</label>
                                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="new_password">New Password</label>
                                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                                    <small class="form-text text-muted">Minimum 6 characters</small>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="confirm_password">Confirm New Password</label>
                                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                                </div>
                                                
                                                <button type="submit" name="update_password" class="btn btn-warning">
                                                    <i class="fas fa-key mr-1"></i> Change Password
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Role Information Tab -->
                                <div class="tab-pane fade" id="role" role="tabpanel">
                                    <div class="profile-card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="fas fa-user-tag mr-2"></i>Role Information</h5>
                                        </div>
                                        <div class="card-body">
                                            <?php if($current_role == 'admin'): ?>
                                            <h5 class="text-primary mb-3">Administrator Privileges</h5>
                                            <p>As an administrator, you have full access to:</p>
                                            <ul class="list-group list-group-flush mb-4">
                                                <li class="list-group-item d-flex align-items-center">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    User management and registration
                                                </li>
                                                <li class="list-group-item d-flex align-items-center">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    System configuration
                                                </li>
                                                <li class="list-group-item d-flex align-items-center">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    All animal records and treatments
                                                </li>
                                                <li class="list-group-item d-flex align-items-center">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    Financial reports and analytics
                                                </li>
                                            </ul>
                                            <?php elseif($current_role == 'veterinary'): ?>
                                            <h5 class="text-primary mb-3">Veterinary Professional Access</h5>
                                            <p>As a veterinary professional, you can:</p>
                                            <ul class="list-group list-group-flush mb-4">
                                                <li class="list-group-item d-flex align-items-center">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    View and manage animal records
                                                </li>
                                                <li class="list-group-item d-flex align-items-center">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    Create treatment plans
                                                </li>
                                                <li class="list-group-item d-flex align-items-center">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    Manage medicine inventory
                                                </li>
                                                <li class="list-group-item d-flex align-items-center">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    Schedule and view appointments
                                                </li>
                                            </ul>
                                            <?php else: ?>
                                            <h5 class="text-primary mb-3">Client Services</h5>
                                            <p>As a client, you can:</p>
                                            <ul class="list-group list-group-flush mb-4">
                                                <li class="list-group-item d-flex align-items-center">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    Manage your pet's profiles
                                                </li>
                                                <li class="list-group-item d-flex align-items-center">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    View treatment history
                                                </li>
                                                <li class="list-group-item d-flex align-items-center">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    Schedule appointments
                                                </li>
                                                <li class="list-group-item d-flex align-items-center">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    Set reminders for vaccinations
                                                </li>
                                            </ul>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <!-- Account Summary -->
                            <div class="profile-card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Account Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="profile-info-item">
                                        <div class="profile-info-label">Member Since</div>
                                        <div class="profile-info-value"><?php echo date('M j, Y', strtotime($user->created_at)); ?></div>
                                    </div>
                                    <div class="profile-info-item">
                                        <div class="profile-info-label">Last Login</div>
                                        <div class="profile-info-value">--</div>
                                    </div>
                                    <div class="profile-info-item">
                                        <div class="profile-info-label">Account Status</div>
                                        <div class="profile-info-value">
                                            <span class="badge badge-success">Active</span>
                                        </div>
                                    </div>
                                    <div class="profile-info-item">
                                        <div class="profile-info-label">Email Verified</div>
                                        <div class="profile-info-value">
                                            <span class="badge badge-success">Verified</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contact Information -->
                            <div class="profile-card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-address-book mr-2"></i>Contact Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="profile-info-item">
                                        <div class="profile-info-label">Email</div>
                                        <div class="profile-info-value"><?php echo htmlspecialchars($user->email); ?></div>
                                    </div>
                                    <div class="profile-info-item">
                                        <div class="profile-info-label">Phone</div>
                                        <div class="profile-info-value"><?php echo !empty($user->phone) ? htmlspecialchars($user->phone) : 'Not provided'; ?></div>
                                    </div>
                                    <div class="profile-info-item">
                                        <div class="profile-info-label">Address</div>
                                        <div class="profile-info-value"><?php echo !empty($user->address) ? htmlspecialchars($user->address) : 'Not provided'; ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="profile-card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-bolt mr-2"></i>Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <?php if($current_role == 'admin'): ?>
                                        <a href="users.php" class="btn btn-outline-primary btn-sm text-left">
                                            <i class="fas fa-users mr-2"></i> Manage Users
                                        </a>
                                        <a href="animals.php" class="btn btn-outline-primary btn-sm text-left">
                                            <i class="fas fa-paw mr-2"></i> View All Animals
                                        </a>
                                        <a href="reports.php" class="btn btn-outline-primary btn-sm text-left">
                                            <i class="fas fa-chart-bar mr-2"></i> Generate Reports
                                        </a>
                                        <?php elseif($current_role == 'veterinary'): ?>
                                        <a href="appointments.php" class="btn btn-outline-primary btn-sm text-left">
                                            <i class="fas fa-calendar-alt mr-2"></i> View Appointments
                                        </a>
                                        <a href="treatments.php" class="btn btn-outline-primary btn-sm text-left">
                                            <i class="fas fa-stethoscope mr-2"></i> Pending Treatments
                                        </a>
                                        <a href="inventory.php" class="btn btn-outline-primary btn-sm text-left">
                                            <i class="fas fa-pills mr-2"></i> Medicine Inventory
                                        </a>
                                        <?php else: ?>
                                        <a href="my-pets.php" class="btn btn-outline-primary btn-sm text-left">
                                            <i class="fas fa-paw mr-2"></i> My Pets
                                        </a>
                                        <a href="appointments.php" class="btn btn-outline-primary btn-sm text-left">
                                            <i class="fas fa-calendar-alt mr-2"></i> Book Appointment
                                        </a>
                                        <a href="treatment-history.php" class="btn btn-outline-primary btn-sm text-left">
                                            <i class="fas fa-history mr-2"></i> Treatment History
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Profile Content Area End Here -->

                <!-- Footer Area Start Here -->
                <footer class="footer-wrap-layout1">
                    <div class="copyright">© Copyrights <a href="#">Veterinary IMS</a> 2025. All rights reserved. Veterinary Public Health Laboratory, Blantyre</div>
                </footer>
                <!-- Footer Area End Here -->
            </div>
        </div>
        <!-- Page Area End Here -->
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