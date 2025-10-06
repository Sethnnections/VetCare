<?php
require_once 'includes/init.php';
requireLogin();
requireRole(['admin']);

$current_role = $auth->getUserRole();
$username = $_SESSION['username'];

$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['message'], $_SESSION['error']);

// Get all users
$user = new User($db);
$users = $user->readAll();
?>
<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>User Management - Veterinary IMS</title>
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
        .user-card {
            transition: transform 0.2s ease;
        }
        .user-card:hover {
            transform: translateY(-2px);
        }
        .status-badge {
            font-size: 0.75rem;
        }
        .role-badge {
            font-size: 0.7rem;
        }
        .action-buttons .btn {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <!-- Preloader Start Here -->
    <div id="preloader"></div>
    <!-- Preloader End Here -->
    
    <div id="wrapper" class="wrapper bg-ash">
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
                    <h3>User Management</h3>
                    <ul>
                        <li>
                            <a href="dashboard.php">Home</a>
                        </li>
                        <li>User Management</li>
                    </ul>
                </div>
                <!-- Breadcubs Area End Here -->

                <!-- User Management Content Start Here -->
                <div class="container-fluid">
                    <?php if($message): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">All Users</h4>
                                    <a href="register_veterinary.php" class="btn btn-light btn-sm">
                                        <i class="fas fa-user-plus me-1"></i> Add Veterinary Staff
                                    </a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Username</th>
                                                    <th>Email</th>
                                                    <th>Role</th>
                                                    <th>Status</th>
                                                    <th>Created Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while($row = $users->fetch(PDO::FETCH_ASSOC)): ?>
                                                <tr>
                                                    <td><?php echo $row['id']; ?></td>
                                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                                    <td>
                                                        <span class="badge role-badge 
                                                            <?php echo $row['role'] == 'admin' ? 'bg-danger' : 
                                                                  ($row['role'] == 'veterinary' ? 'bg-warning' : 'bg-info'); ?>">
                                                            <?php echo ucfirst($row['role']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge status-badge 
                                                            <?php echo $row['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                                            <?php echo $row['is_active'] ? 'Active' : 'Inactive'; ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('M j, Y', strtotime($row['created_at'])); ?></td>
                                                    <td class="action-buttons">
                                                        <form method="POST" action="update_status.php" class="d-inline">
                                                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                            <input type="hidden" name="current_status" value="<?php echo $row['is_active']; ?>">
                                                            <button type="submit" class="btn btn-sm 
                                                                <?php echo $row['is_active'] ? 'btn-warning' : 'btn-success'; ?>">
                                                                <i class="fas 
                                                                    <?php echo $row['is_active'] ? 'fa-user-slash' : 'fa-user-check'; ?>">
                                                                </i>
                                                                <?php echo $row['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                                            </button>
                                                        </form>
                                                        
                                                        <?php if($row['role'] != 'admin'): ?>
                                                        <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Statistics -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">
                                                <?php 
                                                    $total_users = $users->rowCount();
                                                    $users->execute(); // Reset pointer
                                                    echo $total_users; 
                                                ?>
                                            </h4>
                                            <p class="mb-0">Total Users</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">
                                                <?php 
                                                    $active_count = 0;
                                                    while($row = $users->fetch(PDO::FETCH_ASSOC)) {
                                                        if($row['is_active']) $active_count++;
                                                    }
                                                    $users->execute(); // Reset pointer
                                                    echo $active_count; 
                                                ?>
                                            </h4>
                                            <p class="mb-0">Active Users</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-user-check fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">
                                                <?php 
                                                    $vet_count = 0;
                                                    while($row = $users->fetch(PDO::FETCH_ASSOC)) {
                                                        if($row['role'] == 'veterinary') $vet_count++;
                                                    }
                                                    $users->execute(); // Reset pointer
                                                    echo $vet_count; 
                                                ?>
                                            </h4>
                                            <p class="mb-0">Veterinary Staff</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-user-md fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">
                                                <?php 
                                                    $client_count = 0;
                                                    while($row = $users->fetch(PDO::FETCH_ASSOC)) {
                                                        if($row['role'] == 'client') $client_count++;
                                                    }
                                                    echo $client_count; 
                                                ?>
                                            </h4>
                                            <p class="mb-0">Clients</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-user fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- User Management Content End Here -->

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
    <script src="js/main.js"></script>

    <script>
        // Immediate preloader fix
        (function() {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                setTimeout(() => {
                    preloader.style.opacity = '0';
                    setTimeout(() => {
                        preloader.style.display = 'none';
                    }, 300);
                }, 100);
            }
        })();
    </script>
</body>
</html>