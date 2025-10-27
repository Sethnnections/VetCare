<div class="sidebar-main sidebar-menu-one sidebar-expand-md sidebar-color" id="sidebar">
    <div class="mobile-sidebar-header d-md-none">
        <div class="header-logo">
            <a href="<?php echo url('/dashboard'); ?>">
                <img src="<?php echo url('/assets/logo.png'); ?>" alt="logo" onerror="this.src='<?php echo url('/img/logo.png'); ?>'">
            </a>
        </div>
    </div>
    
    <!-- Logo Section -->
    <div class="sidebar-logo text-center py-3">
        <a href="<?php echo url('/dashboard'); ?>">
            <img src="<?php echo url('/assets/logo.png'); ?>" alt="Veterinary IMS" 
                 style="max-width: 180px; height: auto; padding: 10px;"
                 onerror="this.src='<?php echo url('/img/logo.png'); ?>'; this.style.width='180px'">
        </a>
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
            
            <!-- ==================== APPOINTMENTS MENU - SIMPLIFIED ==================== -->
            <li class="nav-item sidebar-nav-item <?php echo (strpos($current_page, 'appointments') !== false) ? 'active' : ''; ?>">
                <a href="#" class="nav-link">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Appointments</span>
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="<?php echo url('/appointments'); ?>" class="nav-link <?php echo ($current_page == 'appointments') ? 'active' : ''; ?>">
                            <i class="fas fa-list"></i>
                            <span>All Appointments</span>
                        </a>
                    </li>
                    
                    <?php if($current_role != 'client'): ?>
                    <li class="nav-item">
                        <a href="<?php echo url('/appointments/create'); ?>" class="nav-link <?php echo ($current_page == 'appointments_create') ? 'active' : ''; ?>">
                            <i class="fas fa-plus-circle"></i>
                            <span>Schedule Appointment</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/appointments/calendar'); ?>" class="nav-link <?php echo ($current_page == 'appointments_calendar') ? 'active' : ''; ?>">
                            <i class="fas fa-calendar"></i>
                            <span>Calendar View</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/appointments/today'); ?>" class="nav-link <?php echo ($current_page == 'appointments_today') ? 'active' : ''; ?>">
                            <i class="fas fa-calendar-day"></i>
                            <span>Today's Schedule</span>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a href="<?php echo url('/appointments/book'); ?>" class="nav-link <?php echo ($current_page == 'appointments_book') ? 'active' : ''; ?>">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Book Appointment</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if($current_role == 'admin'): ?>
                    <li class="nav-item">
                        <a href="<?php echo url('/appointments/reports'); ?>" class="nav-link <?php echo ($current_page == 'appointments_reports') ? 'active' : ''; ?>">
                            <i class="fas fa-chart-bar"></i>
                            <span>Appointment Reports</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>

            <!-- ==================== ADMIN MENU ==================== -->
            <?php if($current_role == 'admin'): ?>
            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-users-cog"></i>
                    <span>User Management</span>
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="<?php echo url('/admin/users/create'); ?>" class="nav-link">
                            <i class="fas fa-user-plus"></i>
                            <span>Add New User</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/admin/users'); ?>" class="nav-link">
                            <i class="fas fa-users"></i>
                            <span>All Users</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-paw"></i>
                    <span>Animal Management</span>
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="<?php echo url('/admin/animals'); ?>" class="nav-link">
                            <i class="fas fa-list"></i>
                            <span>All Animals</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/admin/animal-assignments'); ?>" class="nav-link">
                            <i class="fas fa-user-md"></i>
                            <span>Animal Assignments</span>
                        </a>
                    </li>
                </ul>
            </li>
            <?php endif; ?>

            <!-- ==================== VETERINARY MENU ==================== -->
            <?php if($current_role == 'veterinary'): ?>
            <li class="nav-item">
                <a href="<?php echo url('/veterinary/animals'); ?>" class="nav-link">
                    <i class="fas fa-paw"></i>
                    <span>My Assigned Animals</span>
                </a>
            </li>

            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-stethoscope"></i>
                    <span>Treatment Management</span>
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="<?php echo url('/treatments'); ?>" class="nav-link">
                            <i class="fas fa-list"></i>
                            <span>All Treatments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/treatments/create'); ?>" class="nav-link">
                            <i class="fas fa-plus-circle"></i>
                            <span>New Treatment</span>
                        </a>
                    </li>
                </ul>
            </li>
            <?php endif; ?>

            <!-- ==================== CLIENT MENU ==================== -->
            <?php if($current_role == 'client'): ?>
            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-paw"></i>
                    <span>My Animals</span>
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="<?php echo url('/client/animals'); ?>" class="nav-link">
                            <i class="fas fa-list"></i>
                            <span>My Pets</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/client/animals/add'); ?>" class="nav-link">
                            <i class="fas fa-plus-circle"></i>
                            <span>Add Animal</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-user"></i>
                    <span>My Profile</span>
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="<?php echo url('/client/profile'); ?>" class="nav-link">
                            <i class="fas fa-user-circle"></i>
                            <span>View Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/client/profile/edit'); ?>" class="nav-link">
                            <i class="fas fa-edit"></i>
                            <span>Edit Profile</span>
                        </a>
                    </li>
                </ul>
            </li>
            <?php endif; ?>

            <!-- Logout Button -->
            <li class="nav-item sidebar-footer">
                <a href="<?php echo url('/logout'); ?>" class="nav-link logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Log Out</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<style>
/* Sidebar Styling */
#sidebar {
    background: linear-gradient(135deg, #134d60 0%, #0d3a4d 100%);
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.sidebar-logo {
    border-bottom: 1px solid rgba(255,255,255,0.1);
    margin-bottom: 10px;
}

.sidebar-logo img {
    transition: transform 0.3s ease;
}

.sidebar-logo img:hover {
    transform: scale(1.05);
}

/* Menu Item Styling */
.nav-sidebar-menu .nav-link {
    color: #e9ecef;
    padding: 12px 20px;
    margin: 2px 10px;
    border-radius: 8px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    border: none;
}

.nav-sidebar-menu .nav-link:hover {
    background: rgba(253, 116, 42, 0.9);
    color: white;
    transform: translateX(5px);
}

.nav-sidebar-menu .nav-link.active {
    background: rgba(253, 116, 42, 0.9);
    color: white;
    box-shadow: 0 2px 8px rgba(253, 116, 42, 0.3);
}

.nav-sidebar-menu .nav-link i {
    width: 20px;
    margin-right: 12px;
    font-size: 16px;
}

/* Sub-menu Styling */
.sub-group-menu {
    background: rgba(13, 58, 77, 0.8);
    margin: 0;
    padding: 5px 0;
    border-radius: 0 0 8px 8px;
}

.sub-group-menu .nav-link {
    padding: 10px 20px 10px 50px;
    margin: 0;
    border-radius: 0;
    font-size: 0.9em;
    border-left: 3px solid transparent;
}

.sub-group-menu .nav-link:hover {
    background: rgba(253, 116, 42, 0.7);
    transform: translateX(0);
    border-left-color: #fd742a;
}

.sub-group-menu .nav-link.active {
    background: rgba(253, 116, 42, 0.8);
    border-left-color: #fff;
}

/* Dropdown Arrow Animation */
.sidebar-nav-item > .nav-link .fa-chevron-down {
    transition: transform 0.3s ease;
}

.sidebar-nav-item.active > .nav-link .fa-chevron-down {
    transform: rotate(180deg);
}

/* Logout Button */
.sidebar-footer {
    margin-top: auto;
    border-top: 1px solid rgba(255,255,255,0.1);
    padding-top: 10px;
}

.logout-btn {
    color: #ff6b6b !important;
    background: rgba(255, 107, 107, 0.1) !important;
}

.logout-btn:hover {
    background: rgba(255, 107, 107, 0.2) !important;
    color: #ff5252 !important;
}

/* Mobile Responsive */
@media (max-width: 767.98px) {
    .sidebar-logo img {
        max-width: 140px;
    }
    
    .nav-sidebar-menu .nav-link {
        padding: 10px 15px;
        margin: 1px 5px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-expand active menu items
    const activeMenuItems = document.querySelectorAll('.sidebar-nav-item.active');
    activeMenuItems.forEach(item => {
        const subMenu = item.querySelector('.sub-group-menu');
        if (subMenu) {
            subMenu.style.display = 'block';
        }
    });

    // Handle menu toggle with smooth animation
    const menuToggles = document.querySelectorAll('.sidebar-nav-item > .nav-link');
    menuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            if (this.getAttribute('href') === '#') {
                e.preventDefault();
                const parent = this.parentElement;
                const subMenu = parent.querySelector('.sub-group-menu');
                
                if (subMenu) {
                    const isActive = parent.classList.contains('active');
                    
                    // Close all other menus
                    document.querySelectorAll('.sidebar-nav-item').forEach(item => {
                        if (item !== parent) {
                            item.classList.remove('active');
                            const otherSubMenu = item.querySelector('.sub-group-menu');
                            if (otherSubMenu) {
                                otherSubMenu.style.display = 'none';
                            }
                        }
                    });
                    
                    // Toggle current menu
                    if (!isActive) {
                        parent.classList.add('active');
                        subMenu.style.display = 'block';
                    } else {
                        parent.classList.remove('active');
                        subMenu.style.display = 'none';
                    }
                }
            }
        });
    });

    // Set current page for appointments
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
});
</script>