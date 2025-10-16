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

        /* Sidebar - Blue Background, White Text, Orange Icons */
        #sidebar {
            width: 260px;
            background: var(--primary-blue);
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
            color: white;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            transition: var(--transition);
            border-left: 3px solid transparent;
        }

        .sidebar-menu-content .nav-link:hover,
        .sidebar-menu-content .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: var(--primary-red);
        }

        .sidebar-menu-content .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            color: var(--primary-red); /* Orange icons */
        }

        .sidebar-menu-content .nav-link .float-end {
            transition: var(--transition);
            color: white;
        }

        .sidebar-nav-item.active .nav-link .float-end {
            transform: rotate(180deg);
        }

        .sub-group-menu {
            background-color: rgba(19, 77, 96, 0.8);
            padding-left: 20px;
            display: none;
        }

        .sidebar-nav-item.active .sub-group-menu {
            display: block;
        }

        .sub-group-menu .nav-link {
            padding: 10px 15px;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .sub-group-menu .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .sidebar-footer {
            margin-top: auto;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logout-btn {
            color: white !important;
        }

        .logout-btn:hover {
            background-color: rgba(220, 53, 69, 0.2) !important;
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
            color: var(--primary-blue);
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
            min-height: calc(100vh - 70px);
        }

        #sidebar.collapsed ~ .main-content {
            margin-left: 70px;
            width: calc(100% - 70px);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
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
            background-color: var(--primary-red);
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
            color: var(--primary-blue);
        }

        .action-btn:hover i {
            color: white;
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
        <?php include 'sidebar.php'; ?>

        <!-- Sidebar Area End Here -->

        <!-- Header Area Start Here -->
        <?php include 'header.php'; ?>
        <!-- Header Area End Here -->

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo url('/dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active"><?php echo $title ?? 'Dashboard'; ?></li>
                    </ol>
                </nav>
            </div>

            <!-- Dynamic Content from Views -->
            <?php echo $content ?? 'No content available'; ?>
            
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