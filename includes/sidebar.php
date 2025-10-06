           <!-- Sidebar Area Start Here -->
<div class="sidebar-main sidebar-menu-one sidebar-expand-md sidebar-color">
   <div class="mobile-sidebar-header d-md-none">
        <div class="header-logo">
            <a href="dashboard.php"><img src="img/logo1.png" alt="logo"></a>
        </div>
   </div>
    <div class="sidebar-menu-content">
        <ul class="nav nav-sidebar-menu sidebar-toggle-view">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link active">
                    <i class="flaticon-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <?php if($current_role == 'admin'): ?>
            <!-- Admin Only Menu Items -->
            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="flaticon-classmates"></i>
                    <span>User Management</span>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="register_veterinary.php" class="nav-link">
                            <i class="fas fa-angle-right"></i>Register Veterinary
                        </a>
                    </li>
                <li class="sidebar-item">
                    <a href="users.php" class="sidebar-link">
                        <i class="flaticon-multiple-users-silhouette"></i>
                        <span>User Management</span>
                    </a>
                </li>
                </ul>
            </li>
            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="flaticon-multiple-users-silhouette"></i>
                    <span>Animals</span>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="fas fa-angle-right"></i>All Animals</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="fas fa-angle-right"></i>Animal Details</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="fas fa-angle-right"></i>Add Animal</a>
                    </li>
                </ul>
            </li>
            <?php elseif($current_role == 'veterinary'): ?>
            <!-- Veterinary Only Menu Items -->
            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="flaticon-multiple-users-silhouette"></i>
                    <span>Animals</span>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="fas fa-angle-right"></i>All Animals</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="fas fa-angle-right"></i>Animal Details</a>
                    </li>
                </ul>
            </li>
            <?php else: ?>
            <!-- Client Only Menu Items -->
            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="flaticon-multiple-users-silhouette"></i>
                    <span>My Animals</span>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="fas fa-angle-right"></i>My Pets</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="fas fa-angle-right"></i>Add Animal</a>
                    </li>
                </ul>
            </li>
            <?php endif; ?>

            <!-- Common Menu Items for All Roles -->
            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="flaticon-books"></i>
                    <span>Treatments</span>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="fas fa-angle-right"></i>All Treatments</a>
                    </li>
                    <?php if($current_role != 'client'): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="fas fa-angle-right"></i>Add Treatment</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>
            
            <?php if($current_role != 'client'): ?>
            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="flaticon-technological"></i>
                    <span>Medicines</span>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="fas fa-angle-right"></i>Medicine Inventory</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="fas fa-angle-right"></i>Categories</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="fas fa-angle-right"></i>Add Medicine</a>
                    </li>
                </ul>
            </li>
            <?php endif; ?>
            
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="flaticon-open-book"></i>
                    <span>Vaccinations</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="flaticon-calendar"></i>
                    <span>Appointments</span>
                </a>
            </li>
            
            <?php if($current_role == 'client'): ?>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="flaticon-checklist"></i>
                    <span>My Reminders</span>
                </a>
            </li>
            <?php else: ?>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="flaticon-checklist"></i>
                    <span>Reminders</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if($current_role != 'client'): ?>
            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="flaticon-shopping-list"></i>
                    <span>Billing</span>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="fas fa-angle-right"></i>All Invoices</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="fas fa-angle-right"></i>Payment History</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="flaticon-script"></i>
                    <span>Reports</span>
                </a>
            </li>
            <?php endif; ?>
            
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="flaticon-chat"></i>
                    <span>Feedback</span>
                </a>
            </li>
            
            <?php if($current_role == 'admin'): ?>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="flaticon-settings"></i>
                    <span>System Settings</span>
                </a>
            </li>
            <?php else: ?>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="flaticon-settings"></i>
                    <span>Account Settings</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Logout Button at Bottom -->
            <li class="nav-item sidebar-footer">
                <a href="logout.php" class="nav-link logout-btn">
                    <i class="flaticon-turn-off"></i>
                    <span>Log Out</span>
                </a>
            </li>
        </ul>
    </div>
</div>
<!-- Sidebar Area End Here -->