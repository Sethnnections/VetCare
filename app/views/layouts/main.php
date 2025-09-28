<?php
$currentUser = getCurrentUser();
$userRole = getUserRole();
$pageTitle = $page_title ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - <?= APP_NAME ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
<!-- Custom CSS -->
<!-- <link href="<?= asset('css/style.css') ?>" rel="stylesheet"> -->
<!-- Meta tags -->
<meta name="description" content="Veterinary Health Management System">
<meta name="author" content="NACIT Student Project">
<meta name="csrf-token" content="<?= generateCsrfToken() ?>">
</head>
<body class="<?= $page ?? '' ?>">
    
    <!-- Navigation -->
    <?php if (isLoggedIn()): ?>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
            <div class="container-fluid">
                <!-- Brand -->
                <a class="navbar-brand" href="<?= url('/') ?>">
                    <i class="fas fa-paw me-2"></i>
                    <?= APP_NAME ?>
                </a>
                
                <!-- Mobile toggle button -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!-- Navigation items -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <?php if (hasRole(ROLE_ADMIN)): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('admin/dashboard') ?>">
                                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-users me-1"></i> Users
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?= url('admin/users') ?>">Manage Users</a></li>
                                    <li><a class="dropdown-item" href="<?= url('auth/register') ?>">Add New User</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('admin/clients') ?>">
                                    <i class="fas fa-user-friends me-1"></i> Clients
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('admin/medicines') ?>">
                                    <i class="fas fa-pills me-1"></i> Medicines
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('admin/reports') ?>">
                                    <i class="fas fa-chart-bar me-1"></i> Reports
                                </a>
                            </li>
                        <?php elseif (hasRole(ROLE_VETERINARY)): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('veterinary/dashboard') ?>">
                                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('veterinary/treatments') ?>">
                                    <i class="fas fa-stethoscope me-1"></i> Treatments
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('veterinary/vaccinations') ?>">
                                    <i class="fas fa-syringe me-1"></i> Vaccinations
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('veterinary/reminders') ?>">
                                    <i class="fas fa-bell me-1"></i> Reminders
                                </a>
                            </li>
                        <?php elseif (hasRole(ROLE_CLIENT)): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('client/dashboard') ?>">
                                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('client/animals') ?>">
                                    <i class="fas fa-dog me-1"></i> My Animals
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('client/appointments') ?>">
                                    <i class="fas fa-calendar me-1"></i> Appointments
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                    
                    <!-- Right side navigation -->
                    <ul class="navbar-nav">
                        <!-- Notifications -->
                        <li class="nav-item dropdown">
                            <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" id="notificationDropdown">
                                <i class="fas fa-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationCount">
                                    0
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end notification-dropdown">
                                <li><h6 class="dropdown-header">Notifications</h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><div class="dropdown-item-text text-muted text-center">No new notifications</div></li>
                            </ul>
                        </li>
                        
                        <!-- User profile -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <img src="<?= asset('img/default-avatar.png') ?>" alt="Profile" class="rounded-circle me-1" width="25" height="25">
                                <?= $currentUser['name'] ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= url('auth/profile') ?>">
                                    <i class="fas fa-user me-2"></i> My Profile
                                </a></li>
                                <li><a class="dropdown-item" href="<?= url('auth/change-password') ?>">
                                    <i class="fas fa-key me-2"></i> Change Password
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= url('auth/logout') ?>">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <!-- Sidebar for larger screens -->
        <div class="d-none d-lg-block position-fixed bg-light border-end" id="sidebar">
            <div class="sidebar-content">
                <?php include VIEW_PATH . '/layouts/sidebar.php'; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Main content -->
    <main class="<?= isLoggedIn() ? 'main-content' : '' ?>">
        <!-- Flash messages -->
        <?php if (hasFlash()): ?>
            <div class="container-fluid mt-3">
                <?php foreach (getFlash() as $type => $message): ?>
                    <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                        <?php if ($type === 'success'): ?>
                            <i class="fas fa-check-circle me-2"></i>
                        <?php elseif ($type === 'error'): ?>
                            <i class="fas fa-exclamation-circle me-2"></i>
                        <?php elseif ($type === 'warning'): ?>
                            <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php else: ?>
                            <i class="fas fa-info-circle me-2"></i>
                        <?php endif; ?>
                        <?= $message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Page content -->
        <?php include $content; ?>
    </main>
    
    <!-- Footer -->
    <?php if (isLoggedIn()): ?>
        <footer class="footer bg-light mt-auto py-3">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <span class="text-muted">© <?= date('Y') ?> <?= APP_NAME ?> - Version <?= APP_VERSION ?></span>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="text-muted">Developed by NACIT Student</span>
                    </div>
                </div>
            </div>
        </footer>
    <?php endif; ?>
    
    <!-- Loading overlay -->
    <div class="loading-overlay" id="loadingOverlay" style="display: none;">
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= asset('js/app.js') ?>"></script>
    
    <script>
        // Global JavaScript variables
        window.APP_CONFIG = {
            baseUrl: '<?= BASE_URL ?>',
            csrfToken: '<?= generateCsrfToken() ?>',
            user: <?= json_encode($currentUser) ?>,
            userRole: '<?= $userRole ?>'
        };
    </script>
    
    <!-- Page-specific scripts -->
    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= asset("js/{$script}") ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if (isset($inline_scripts)): ?>
        <script>
            <?= $inline_scripts ?>
        </script>
    <?php endif; ?>
</body>
</html>