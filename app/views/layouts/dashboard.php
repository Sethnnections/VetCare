<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Veterinary IMS'; ?></title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo url('/img/favicon.png'); ?>">
    
    <!-- CDN CSS Files -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        :root {
            --primary-red: #fd742a;
            --primary-blue: #134d60;
            --accent-yellow: #fec525;
            --light-cream: #f9f1d5;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
            --border-radius: 12px;
            --box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: var(--dark-gray);
            overflow-x: hidden;
        }

        /* Preloader */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: white;
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: var(--transition);
        }

        #preloader:after {
            content: '';
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--primary-blue);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        body.loaded #preloader {
            opacity: 0;
            visibility: hidden;
        }

        /* Layout */
        #wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        #sidebar {
            width: 260px;
            background: white;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            z-index: 1000;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        #sidebar.collapsed {
            width: 70px;
        }

        #sidebar.collapsed .sidebar-menu-content .nav-link span,
        #sidebar.collapsed .sidebar-menu-content .nav-link .float-end,
        #sidebar.collapsed .admin-title,
        #sidebar.collapsed .sidebar-footer span {
            display: none;
        }

        #sidebar.collapsed .sidebar-menu-content .nav-item {
            text-align: center;
        }

        .sidebar-menu-content {
            padding: 20px 0;
        }

        .sidebar-menu-content .nav-link {
            color: var(--dark-gray);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            transition: var(--transition);
            border-left: 3px solid transparent;
        }

        .sidebar-menu-content .nav-link:hover,
        .sidebar-menu-content .nav-link.active {
            background-color: rgba(253, 116, 42, 0.1);
            color: var(--primary-red);
            border-left-color: var(--primary-red);
        }

        .sidebar-menu-content .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        .sidebar-menu-content .nav-link .float-end {
            transition: var(--transition);
        }

        .sidebar-nav-item.active .nav-link .float-end {
            transform: rotate(180deg);
        }

        .sub-group-menu {
            background-color: rgba(249, 241, 213, 0.3);
            padding-left: 20px;
            display: none;
        }

        .sidebar-nav-item.active .sub-group-menu {
            display: block;
        }

        .sub-group-menu .nav-link {
            padding: 10px 15px;
            font-size: 0.9rem;
        }

        .sidebar-footer {
            margin-top: auto;
            border-top: 1px solid #eee;
        }

        .logout-btn {
            color: #dc3545 !important;
        }

        .logout-btn:hover {
            background-color: rgba(220, 53, 69, 0.1) !important;
        }

        /* Header */
        .header-main {
            background: white;
            box-shadow: var(--box-shadow);
            padding: 0 20px;
            height: 70px;
            position: fixed;
            top: 0;
            right: 0;
            left: 260px;
            z-index: 999;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        #sidebar.collapsed ~ .header-main {
            left: 70px;
        }

        .header-logo img {
            height: 40px;
        }

        .toggle-button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .toggle-button:hover {
            background-color: var(--light-gray);
        }

        .btn-icon-wrap {
            display: flex;
            flex-direction: column;
            width: 20px;
            height: 16px;
            justify-content: space-between;
        }

        .btn-icon-wrap span {
            display: block;
            height: 2px;
            width: 100%;
            background-color: var(--dark-gray);
            border-radius: 2px;
            transition: var(--transition);
        }

        .header-search {
            flex: 1;
            max-width: 500px;
            margin: 0 20px;
        }

        .search-input-group {
            position: relative;
        }

        .search-input-group input {
            padding-left: 40px;
            border-radius: 20px;
            border: 1px solid #ddd;
        }

        .search-input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .user-profile {
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .user-profile:hover {
            background-color: var(--light-gray);
        }

        .user-info {
            margin-right: 10px;
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            margin-bottom: 0;
            font-size: 0.9rem;
        }

        .user-role {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .dropdown-menu {
            border: none;
            box-shadow: var(--box-shadow);
            border-radius: var(--border-radius);
            padding: 10px 0;
        }

        .dropdown-item {
            padding: 8px 20px;
            display: flex;
            align-items: center;
            transition: var(--transition);
        }

        .dropdown-item i {
            margin-right: 10px;
            width: 16px;
        }

        .dropdown-item:hover {
            background-color: rgba(253, 116, 42, 0.1);
            color: var(--primary-red);
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            margin-top: 70px;
            padding: 20px;
            transition: var(--transition);
            width: calc(100% - 260px);
        }

        #sidebar.collapsed ~ .main-content {
            margin-left: 70px;
            width: calc(100% - 70px);
        }

        .page-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 20px;
        }

        .page-title {
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: 0;
        }

        .breadcrumb {
            background: none;
            padding: 0;
            margin-bottom: 0;
        }

        .breadcrumb-item a {
            color: var(--primary-red);
            text-decoration: none;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--box-shadow);
            display: flex;
            align-items: center;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.5rem;
            color: white;
        }

        .stat-card.primary .stat-icon {
            background-color: var(--primary-blue);
        }

        .stat-card.success .stat-icon {
            background-color: #28a745;
        }

        .stat-card.danger .stat-icon {
            background-color: var(--primary-red);
        }

        .stat-card.info .stat-icon {
            background-color: #17a2b8;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        /* Dashboard Cards */
        .dashboard-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card-header {
            padding: 15px 20px;
            background-color: var(--light-gray);
            border-bottom: 1px solid #eee;
        }

        .card-title {
            margin-bottom: 0;
            font-weight: 600;
            color: var(--primary-blue);
        }

        .card-body {
            padding: 20px;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px 15px;
            background: white;
            border: 1px solid #eee;
            border-radius: var(--border-radius);
            text-decoration: none;
            color: var(--dark-gray);
            transition: var(--transition);
            text-align: center;
        }

        .action-btn:hover {
            background-color: var(--primary-blue);
            color: white;
            transform: translateY(-3px);
        }

        .action-btn i {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        /* Footer */
        .footer {
            background: white;
            padding: 15px 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #6c757d;
            margin-top: 30px;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            #sidebar {
                transform: translateX(-100%);
            }
            
            #sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .header-main {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .header-search {
                display: none;
            }
            
            .mobile-nav-toggle {
                display: block !important;
            }
        }

        .mobile-nav-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.2rem;
            color: var(--dark-gray);
        }

        /* Animation classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <!-- Preloader -->
    <div id="preloader"></div>

    <div id="wrapper">
        <!-- Sidebar Area Start Here -->
        <div class="sidebar-main sidebar-menu-one sidebar-expand-md sidebar-color" id="sidebar">
            <div class="mobile-sidebar-header d-md-none">
                <div class="header-logo">
                    <a href="<?php echo url('/dashboard'); ?>">
                        <img src="<?php echo url('/img/logo1.png'); ?>" alt="logo">
                    </a>
                </div>
            </div>
            <div class="sidebar-menu-content">
                <ul class="nav nav-sidebar-menu sidebar-toggle-view">
                    <li class="nav-item">
                        <a href="<?php echo url('/dashboard'); ?>" class="nav-link <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <?php $current_role = $_SESSION['role'] ?? 'client'; ?>
                    
                    <?php if($current_role == 'admin'): ?>
                    <!-- Admin Only Menu Items -->
                    <li class="nav-item sidebar-nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-users"></i>
                            <span>User Management</span>
                            <i class="fas fa-chevron-down float-end"></i>
                        </a>
                        <ul class="nav sub-group-menu">
                            <li class="nav-item">
                                <a href="<?php echo url('/users/create'); ?>" class="nav-link">
                                    <i class="fas fa-angle-right"></i>
                                    <span>Register Veterinary</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo url('/users'); ?>" class="nav-link">
                                    <i class="fas fa-angle-right"></i>
                                    <span>User Management</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item sidebar-nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-paw"></i>
                            <span>Animals</span>
                            <i class="fas fa-chevron-down float-end"></i>
                        </a>
                        <ul class="nav sub-group-menu">
                            <li class="nav-item">
                                <a href="<?php echo url('/animals'); ?>" class="nav-link">
                                    <i class="fas fa-angle-right"></i>
                                    <span>All Animals</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo url('/animals/create'); ?>" class="nav-link">
                                    <i class="fas fa-angle-right"></i>
                                    <span>Add Animal</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <?php elseif($current_role == 'veterinary'): ?>
                    <!-- Veterinary Only Menu Items -->
                    <li class="nav-item sidebar-nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-paw"></i>
                            <span>Animals</span>
                            <i class="fas fa-chevron-down float-end"></i>
                        </a>
                        <ul class="nav sub-group-menu">
                            <li class="nav-item">
                                <a href="<?php echo url('/animals'); ?>" class="nav-link">
                                    <i class="fas fa-angle-right"></i>
                                    <span>All Animals</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <?php else: ?>
                    <!-- Client Only Menu Items -->
                    <li class="nav-item sidebar-nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-paw"></i>
                            <span>My Animals</span>
                            <i class="fas fa-chevron-down float-end"></i>
                        </a>
                        <ul class="nav sub-group-menu">
                            <li class="nav-item">
                                <a href="<?php echo url('/client/animals'); ?>" class="nav-link">
                                    <i class="fas fa-angle-right"></i>
                                    <span>My Pets</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo url('/client/animals/add'); ?>" class="nav-link">
                                    <i class="fas fa-angle-right"></i>
                                    <span>Add Animal</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Common Menu Items for All Roles -->
                    <li class="nav-item sidebar-nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-stethoscope"></i>
                            <span>Treatments</span>
                            <i class="fas fa-chevron-down float-end"></i>
                        </a>
                        <ul class="nav sub-group-menu">
                            <li class="nav-item">
                                <a href="<?php echo url('/treatments'); ?>" class="nav-link">
                                    <i class="fas fa-angle-right"></i>
                                    <span>All Treatments</span>
                                </a>
                            </li>
                            <?php if($current_role != 'client'): ?>
                            <li class="nav-item">
                                <a href="<?php echo url('/treatments/create'); ?>" class="nav-link">
                                    <i class="fas fa-angle-right"></i>
                                    <span>Add Treatment</span>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    
                    <?php if($current_role != 'client'): ?>
                    <li class="nav-item sidebar-nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-pills"></i>
                            <span>Medicines</span>
                            <i class="fas fa-chevron-down float-end"></i>
                        </a>
                        <ul class="nav sub-group-menu">
                            <li class="nav-item">
                                <a href="<?php echo url('/medicines'); ?>" class="nav-link">
                                    <i class="fas fa-angle-right"></i>
                                    <span>Medicine Inventory</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo url('/medicines/create'); ?>" class="nav-link">
                                    <i class="fas fa-angle-right"></i>
                                    <span>Add Medicine</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a href="<?php echo url('/vaccines'); ?>" class="nav-link">
                            <i class="fas fa-syringe"></i>
                            <span>Vaccinations</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo url('/appointments'); ?>" class="nav-link">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Appointments</span>
                        </a>
                    </li>
                    
                    <?php if($current_role == 'client'): ?>
                    <li class="nav-item">
                        <a href="<?php echo url('/client/reminders'); ?>" class="nav-link">
                            <i class="fas fa-bell"></i>
                            <span>My Reminders</span>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a href="<?php echo url('/reminders'); ?>" class="nav-link">
                            <i class="fas fa-bell"></i>
                            <span>Reminders</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if($current_role != 'client'): ?>
                    <li class="nav-item sidebar-nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <span>Billing</span>
                            <i class="fas fa-chevron-down float-end"></i>
                        </a>
                        <ul class="nav sub-group-menu">
                            <li class="nav-item">
                                <a href="<?php echo url('/billings'); ?>" class="nav-link">
                                    <i class="fas fa-angle-right"></i>
                                    <span>All Invoices</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo url('/billings/payments'); ?>" class="nav-link">
                                    <i class="fas fa-angle-right"></i>
                                    <span>Payment History</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo url('/reports'); ?>" class="nav-link">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a href="<?php echo url('/feedback'); ?>" class="nav-link">
                            <i class="fas fa-comment"></i>
                            <span>Feedback</span>
                        </a>
                    </li>
                    
                    <?php if($current_role == 'admin'): ?>
                    <li class="nav-item">
                        <a href="<?php echo url('/admin/settings'); ?>" class="nav-link">
                            <i class="fas fa-cogs"></i>
                            <span>System Settings</span>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a href="<?php echo url('/auth/profile'); ?>" class="nav-link">
                            <i class="fas fa-user-cog"></i>
                            <span>Account Settings</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <!-- Logout Button at Bottom -->
                    <li class="nav-item sidebar-footer">
                        <a href="<?php echo url('/logout'); ?>" class="nav-link logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Log Out</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Sidebar Area End Here -->

        <!-- Header Area Start Here -->
        <div class="header-main">
            <div class="header-left">
                <button class="toggle-button" id="sidebarToggle">
                    <span class="btn-icon-wrap">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
                <button class="mobile-nav-toggle d-md-none" id="mobileSidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="header-search">
                <div class="search-input-group">
                    <i class="fas fa-search"></i>
                    <input type="text" class="form-control" placeholder="Find Something...">
                </div>
            </div>
            
            <div class="header-right">
                <div class="user-profile dropdown">
                    <div class="user-info">
                        <h6 class="user-name">
                            <?php 
                            if(!empty($_SESSION['first_name'])) {
                                echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']);
                            } else {
                                echo htmlspecialchars($_SESSION['username']);
                            }
                            ?>
                        </h6>
                        <div class="user-role"><?php echo ucfirst($_SESSION['role']); ?></div>
                    </div>
                    <div class="user-avatar">
                        <?php 
                        $initials = '';
                        if(!empty($_SESSION['first_name'])) {
                            $initials = strtoupper(substr($_SESSION['first_name'], 0, 1));
                            if(!empty($_SESSION['last_name'])) {
                                $initials .= strtoupper(substr($_SESSION['last_name'], 0, 1));
                            }
                        } else {
                            $initials = strtoupper(substr($_SESSION['username'], 0, 2));
                        }
                        echo $initials;
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header Area End Here -->

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">Dashboard</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo url('/dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </nav>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card primary fade-in">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">245</div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>
                <div class="stat-card success fade-in">
                    <div class="stat-icon">
                        <i class="fas fa-paw"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">156</div>
                        <div class="stat-label">Total Animals</div>
                    </div>
                </div>
                <div class="stat-card danger fade-in">
                    <div class="stat-icon">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">89</div>
                        <div class="stat-label">Total Treatments</div>
                    </div>
                </div>
                <div class="stat-card info fade-in">
                    <div class="stat-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">32</div>
                        <div class="stat-label">Active Treatments</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <a href="<?php echo url('/users/create'); ?>" class="action-btn">
                            <i class="fas fa-user-plus"></i>
                            <span>Add User</span>
                        </a>
                        <a href="<?php echo url('/animals/create'); ?>" class="action-btn">
                            <i class="fas fa-plus"></i>
                            <span>Add Animal</span>
                        </a>
                        <a href="<?php echo url('/treatments/create'); ?>" class="action-btn">
                            <i class="fas fa-stethoscope"></i>
                            <span>Add Treatment</span>
                        </a>
                        <a href="<?php echo url('/reports'); ?>" class="action-btn">
                            <i class="fas fa-chart-bar"></i>
                            <span>View Reports</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="footer">
                © <script>document.write(new Date().getFullYear())</script> Veterinary IMS. All rights reserved.
            </div>
        </div>
    </div>

    <!-- CDN JavaScript Files -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Counter-Up/1.0.0/jquery.counterup.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar toggle functionality
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
            });
        }
        
        if (mobileSidebarToggle) {
            mobileSidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('mobile-open');
            });
        }
        
        // Sub-menu toggle functionality
        const sidebarNavItems = document.querySelectorAll('.sidebar-nav-item');
        sidebarNavItems.forEach(item => {
            const link = item.querySelector('.nav-link');
            link.addEventListener('click', function(e) {
                if (!sidebar.classList.contains('collapsed')) {
                    e.preventDefault();
                    item.classList.toggle('active');
                }
            });
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !mobileSidebarToggle.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
            }
        });
        
        // Active page highlighting
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-link[href]');
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
                // Also activate parent menu items
                let parent = link.closest('.sub-group-menu');
                if (parent) {
                    parent.previousElementSibling.classList.add('active');
                    parent.parentElement.classList.add('active');
                }
            }
        });

        // Preloader Handling
        const preloader = document.getElementById('preloader');
        
        // Hide preloader when window fully loads
        window.addEventListener('load', function() {
            setTimeout(function() {
                if (preloader) {
                    preloader.style.opacity = '0';
                    preloader.style.visibility = 'hidden';
                    document.body.classList.add('loaded');
                }
            }, 500);
        });

        // Safety: force hide after 5 seconds
        setTimeout(function() {
            if (preloader) {
                preloader.style.display = 'none';
                preloader.style.opacity = '0';
                preloader.style.visibility = 'hidden';
                document.body.classList.add('loaded');
            }
        }, 5000);

        // Initialize counter animation for stats
        $('.stat-value').counterUp({
            delay: 10,
            time: 1000
        });
    });
    </script>
    
    <!-- Additional scripts from view -->
    <?php echo $scripts ?? ''; ?>
</body>
</html>