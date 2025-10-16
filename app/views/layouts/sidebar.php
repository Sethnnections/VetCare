 <div class="sidebar-main sidebar-menu-one sidebar-expand-md sidebar-color" id="sidebar">
            <div class="mobile-sidebar-header d-md-none">
                
                <div class="header-logo">
                    <a href="<?php echo url('/dashboard'); ?>">
                        <img src="<?php echo url('/img/logo.png'); ?>" alt="logo">
                    </a>
                </div>
            </div>
            <a href="<?php echo url('/dashboard'); ?>">
                        <img src="<?php echo url('/img/logo.png'); ?>" alt="logo" style="width: 200px; height: 220px; padding: 10px; margin-left: 10px;">
                    </a>
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