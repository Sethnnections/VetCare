<?php
// START OUTPUT BUFFERING - NO WHITESPACE BEFORE THIS!
ob_start();

require_once 'includes/init.php';
requireLogin();

$current_role = $auth->getUserRole();
$username = $_SESSION['username'];
?>
<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Veterinary IMS | <?php echo ucfirst($current_role); ?> Dashboard</title>
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
    <!-- Full Calender CSS -->
    <link rel="stylesheet" href="css/fullcalendar.min.css">
    <!-- Animate CSS -->
    <link rel="stylesheet" href="css/animate.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <!-- Modernize js -->
    <script src="js/modernizr-3.6.0.min.js"></script>
    <style>
    /* Fixed Sidebar Height */
    .sidebar-main {
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        margin-top: 80px;
        overflow-y: auto;
        z-index: 1000;
    }
    
    .sidebar-menu-content {
        height: calc(100vh - 160px);
        overflow-y: auto;
    }
    
    /* Sticky Footer */
    .dashboard-content-one {
        min-height: calc(100vh - 160px);
        display: flex;
        flex-direction: column;
    }
    
    .footer-wrap-layout1 {
        margin-top: auto;
    }
    
    /* Adjust main content area */
    .dashboard-page-one {
        margin-left: 260px;
    }
    
    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .sidebar-main {
            margin-top: 60px;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        
        .sidebar-main.mobile-open {
            transform: translateX(0);
        }
        
        .dashboard-page-one {
            margin-left: 0;
        }
    }

    /* ===== PRELOADER FIX ===== */
    #preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #fff;
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: opacity 0.5s ease, visibility 0.5s ease;
    }

    #preloader:after {
        content: '';
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #e86029;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Hide preloader when loaded */
    body.loaded #preloader {
        opacity: 0;
        visibility: hidden;
    }

    /* Ensure content is visible */
    #wrapper {
        opacity: 1;
        visibility: visible;
    }
    </style>
</head>

<body>
    <!-- Preloader Start Here -->
    <div id="preloader"></div>
    <!-- Preloader End Here -->
    
    <!-- Immediate Preloader Fix Script -->
    <script>
    // Immediate preloader safety fix
    (function() {
        const preloader = document.getElementById('preloader');
        // Safety: hide preloader if it's still visible after a short delay
        setTimeout(() => {
            if (preloader && preloader.style.display !== 'none') {
                preloader.style.opacity = '0';
                preloader.style.visibility = 'hidden';
                document.body.classList.add('loaded');
            }
        }, 3000);
    })();
    </script>
    
    <div id="wrapper" class="wrapper bg-ash">
       <!-- Header Menu Area Start Here -->
        <div class="navbar navbar-expand-md header-menu-one bg-light">
            <div class="nav-bar-header-one">
                <div class="header-logo">
                    <a href="dashboard.php">
                        <img src="img/logo.png" width="50px" height="30px" alt="logo" style="padding: 0%; margin: 0%;">
                    </a>
                </div>
                 <div class="toggle-button sidebar-toggle">
                    <button type="button" class="item-link">
                        <span class="btn-icon-wrap">
                            <span></span>
                            <span></span>
                            <span></span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="d-md-none mobile-nav-bar">
               <button class="navbar-toggler pulse-animation" type="button" data-toggle="collapse" data-target="#mobile-navbar" aria-expanded="false">
                    <i class="far fa-arrow-alt-circle-down"></i>
                </button>
                <button type="button" class="navbar-toggler sidebar-toggle-mobile">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="header-main-menu collapse navbar-collapse" id="mobile-navbar">
                <ul class="navbar-nav">
                    <li class="navbar-item header-search-bar">
                        <div class="input-group stylish-input-group">
                            <span class="input-group-addon">
                                <button type="submit">
                                    <span class="flaticon-search" aria-hidden="true"></span>
                                </button>
                            </span>
                            <input type="text" class="form-control" placeholder="Find Something . . .">
                        </div>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="navbar-item dropdown header-admin">
                        <a class="navbar-nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
                            aria-expanded="false">
                            <div class="admin-title">
                                <h5 class="item-title"><?php echo $username; ?></h5>
                                <span><?php echo ucfirst($current_role); ?></span>
                            </div>
                            <div class="admin-img">
                                <img src="img/figure/admin.jpg" alt="Admin">
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="item-header">
                                <h6 class="item-title"><?php echo $username; ?></h6>
                            </div>
                            <div class="item-content">
                                <ul class="settings-list">
                                    <li><a href="profile.php"><i class="flaticon-user"></i>My Profile</a></li>
                                    <li><a href="#"><i class="flaticon-list"></i>Task</a></li>
                                    <li><a href="#"><i class="flaticon-chat-comment-oval-speech-bubble-with-text-lines"></i>Message</a></li>
                                    <li><a href="#"><i class="flaticon-gear-loading"></i>Account Settings</a></li>
                                    <li><a href="logout.php"><i class="flaticon-turn-off"></i>Log Out</a></li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Header Menu Area End Here -->
        <!-- Page Area Start Here -->
        <div class="dashboard-page-one">
            <!-- Sidebar Area Start Here -->
             <?php include 'includes/sidebar.php'; ?>

            <!-- Sidebar Area End Here -->
            <div class="dashboard-content-one">
                <!-- Breadcubs Area Start Here -->
                <div class="breadcrumbs-area">
                    <h3>Veterinary <?php echo ucfirst($current_role); ?> Dashboard</h3>
                    <ul>
                        <li>
                            <a href="dashboard.php">Home</a>
                        </li>
                        <li><?php echo ucfirst($current_role); ?></li>
                    </ul>
                </div>
                <!-- Breadcubs Area End Here -->

                <!-- Role-specific Dashboard Content -->
                <?php if($current_role == 'admin'): ?>
                <!-- Admin Dashboard Content -->
                <?php include 'includes/admin_dashboard.php'; ?>
                
                <?php elseif($current_role == 'veterinary'): ?>
                <!-- Veterinary Dashboard Content -->
                <?php include 'includes/veterinary_dashboard.php'; ?>
                
                <?php else: ?>
                <!-- Client Dashboard Content -->
                <?php include 'includes/client_dashboard.php'; ?>
                <?php endif; ?>

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
    <!-- Counterup Js -->
    <script src="js/jquery.counterup.min.js"></script>
    <!-- Moment Js -->
    <script src="js/moment.min.js"></script>
    <!-- Waypoints Js -->
    <script src="js/jquery.waypoints.min.js"></script>
    <!-- Scroll Up Js -->
    <script src="js/jquery.scrollUp.min.js"></script>
    <!-- Full Calender Js -->
    <script src="js/fullcalendar.min.js"></script>
    <!-- Chart Js -->
    <script src="js/Chart.min.js"></script>
    <!-- Custom Js -->
    <script src="js/main.js"></script>

    <!-- Preloader Handling Script -->
    <script>
    // Enhanced preloader handling
    document.addEventListener('DOMContentLoaded', function() {
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
            const preloader = document.getElementById('preloader');
            if (preloader) {
                preloader.style.display = 'none';
                preloader.style.opacity = '0';
                preloader.style.visibility = 'hidden';
                document.body.classList.add('loaded');
            }
        }, 5000);
    });
    </script>

    <!-- Scroll to Top Button -->
    <a href="#" class="scroll-to-top" style="display: none; position: fixed; bottom: 20px; right: 20px; width: 40px; height: 40px; background-color: #667eea; color: white; border-radius: 50%; text-align: center; line-height: 40px; z-index: 1000; text-decoration: none;">
        <i class="fas fa-chevron-up"></i>
    </a>
</body>
</html>
<?php
// END OUTPUT BUFFERING - flush all output at the end
ob_end_flush();
?>