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
            <!-- ==================== ADMIN MENU ==================== -->
            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-users-cog"></i>
                    <span>User Management</span>
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="<?php echo url('/users/create'); ?>" class="nav-link">
                            <i class="fas fa-user-plus"></i>
                            <span>Register Staff</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/users'); ?>" class="nav-link">
                            <i class="fas fa-users"></i>
                            <span>All Users</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/users/veterinarians'); ?>" class="nav-link">
                            <i class="fas fa-user-md"></i>
                            <span>Veterinarians</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/users/clients'); ?>" class="nav-link">
                            <i class="fas fa-user-friends"></i>
                            <span>Clients</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/users/roles'); ?>" class="nav-link">
                            <i class="fas fa-user-tag"></i>
                            <span>Role Management</span>
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
                        <a href="<?php echo url('/animals'); ?>" class="nav-link">
                            <i class="fas fa-list"></i>
                            <span>All Animals</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/animals/create'); ?>" class="nav-link">
                            <i class="fas fa-plus-circle"></i>
                            <span>Add Animal</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/animals/breeds'); ?>" class="nav-link">
                            <i class="fas fa-dna"></i>
                            <span>Breeds Management</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/animals/species'); ?>" class="nav-link">
                            <i class="fas fa-dog"></i>
                            <span>Species Types</span>
                        </a>
                    </li>
                </ul>
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
                    <li class="nav-item">
                        <a href="<?php echo url('/treatments/types'); ?>" class="nav-link">
                            <i class="fas fa-tags"></i>
                            <span>Treatment Types</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/treatments/schedule'); ?>" class="nav-link">
                            <i class="fas fa-calendar-check"></i>
                            <span>Treatment Schedule</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-pills"></i>
                    <span>Medicine & Inventory</span>
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="<?php echo url('/medicines'); ?>" class="nav-link">
                            <i class="fas fa-capsules"></i>
                            <span>Medicine Inventory</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/medicines/create'); ?>" class="nav-link">
                            <i class="fas fa-plus-circle"></i>
                            <span>Add Medicine</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/medicines/categories'); ?>" class="nav-link">
                            <i class="fas fa-tags"></i>
                            <span>Medicine Categories</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/medicines/stock'); ?>" class="nav-link">
                            <i class="fas fa-boxes"></i>
                            <span>Stock Management</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/medicines/expired'); ?>" class="nav-link">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Expired Medicines</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-syringe"></i>
                    <span>Vaccinations</span>
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="<?php echo url('/vaccines'); ?>" class="nav-link">
                            <i class="fas fa-list"></i>
                            <span>All Vaccinations</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/vaccines/schedule'); ?>" class="nav-link">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Vaccination Schedule</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/vaccines/types'); ?>" class="nav-link">
                            <i class="fas fa-vial"></i>
                            <span>Vaccine Types</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Billing & Payments</span>
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="<?php echo url('/billings'); ?>" class="nav-link">
                            <i class="fas fa-receipt"></i>
                            <span>All Invoices</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/billings/create'); ?>" class="nav-link">
                            <i class="fas fa-plus-circle"></i>
                            <span>Create Invoice</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/billings/payments'); ?>" class="nav-link">
                            <i class="fas fa-credit-card"></i>
                            <span>Payment History</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/billings/reports'); ?>" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Revenue Reports</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports & Analytics</span>
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="<?php echo url('/reports'); ?>" class="nav-link">
                            <i class="fas fa-chart-pie"></i>
                            <span>Overview Reports</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/reports/treatments'); ?>" class="nav-link">
                            <i class="fas fa-stethoscope"></i>
                            <span>Treatment Reports</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/reports/financial'); ?>" class="nav-link">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Financial Reports</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/reports/inventory'); ?>" class="nav-link">
                            <i class="fas fa-boxes"></i>
                            <span>Inventory Reports</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="<?php echo url('/admin/settings'); ?>" class="nav-link">
                    <i class="fas fa-cogs"></i>
                    <span>System Settings</span>
                </a>
            </li>

            <?php elseif($current_role == 'veterinary'): ?>
            <!-- ==================== VETERINARY MENU ==================== -->
            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-paw"></i>
                    <span>Animal Management</span>
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="<?php echo url('/animals'); ?>" class="nav-link">
                            <i class="fas fa-list"></i>
                            <span>All Animals</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/animals/medical-records'); ?>" class="nav-link">
                            <i class="fas fa-file-medical"></i>
                            <span>Medical Records</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/animals/emergency'); ?>" class="nav-link">
                            <i class="fas fa-ambulance"></i>
                            <span>Emergency Cases</span>
                        </a>
                    </li>
                </ul>
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
                    <li class="nav-item">
                        <a href="<?php echo url('/treatments/my-treatments'); ?>" class="nav-link">
                            <i class="fas fa-user-md"></i>
                            <span>My Treatments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/treatments/pending'); ?>" class="nav-link">
                            <i class="fas fa-clock"></i>
                            <span>Pending Treatments</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-pills"></i>
                    <span>Medicine Management</span>
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="<?php echo url('/medicines'); ?>" class="nav-link">
                            <i class="fas fa-capsules"></i>
                            <span>Medicine Inventory</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/medicines/prescribe'); ?>" class="nav-link">
                            <i class="fas fa-prescription"></i>
                            <span>Prescribe Medicine</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/medicines/dispense'); ?>" class="nav-link">
                            <i class="fas fa-pills"></i>
                            <span>Dispense Medicine</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-syringe"></i>
                    <span>Vaccinations</span>
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="<?php echo url('/vaccines'); ?>" class="nav-link">
                            <i class="fas fa-list"></i>
                            <span>All Vaccinations</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/vaccines/administer'); ?>" class="nav-link">
                            <i class="fas fa-syringe"></i>
                            <span>Administer Vaccine</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/vaccines/schedule'); ?>" class="nav-link">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Vaccination Schedule</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="<?php echo url('/veterinary/patients'); ?>" class="nav-link">
                    <i class="fas fa-heartbeat"></i>
                    <span>Patient Records</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?php echo url('/veterinary/schedule'); ?>" class="nav-link">
                    <i class="fas fa-calendar-day"></i>
                    <span>My Schedule</span>
                </a>
            </li>

            <?php else: ?>
            <!-- ==================== CLIENT MENU ==================== -->
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
                    <li class="nav-item">
                        <a href="<?php echo url('/client/animals/medical-records'); ?>" class="nav-link">
                            <i class="fas fa-file-medical"></i>
                            <span>Medical Records</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/client/animals/vaccinations'); ?>" class="nav-link">
                            <i class="fas fa-syringe"></i>
                            <span>Vaccination History</span>
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
                    <li class="nav-item">
                        <a href="<?php echo url('/auth/change-password'); ?>" class="nav-link">
                            <i class="fas fa-key"></i>
                            <span>Change Password</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-calendar-check"></i>
                    <span>My Appointments</span>
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="<?php echo url('/client/appointments'); ?>" class="nav-link">
                            <i class="fas fa-list"></i>
                            <span>All Appointments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/client/appointments/book'); ?>" class="nav-link">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Book Appointment</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/client/appointments/history'); ?>" class="nav-link">
                            <i class="fas fa-history"></i>
                            <span>Appointment History</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-file-invoice"></i>
                    <span>My Bills & Payments</span>
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="<?php echo url('/client/bills'); ?>" class="nav-link">
                            <i class="fas fa-receipt"></i>
                            <span>All Bills</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/client/bills/pending'); ?>" class="nav-link">
                            <i class="fas fa-clock"></i>
                            <span>Pending Payments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/client/bills/history'); ?>" class="nav-link">
                            <i class="fas fa-history"></i>
                            <span>Payment History</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-bell"></i>
                    <span>My Reminders</span>
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="<?php echo url('/client/reminders'); ?>" class="nav-link">
                            <i class="fas fa-list"></i>
                            <span>All Reminders</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/client/reminders/vaccination'); ?>" class="nav-link">
                            <i class="fas fa-syringe"></i>
                            <span>Vaccination Reminders</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/client/reminders/appointment'); ?>" class="nav-link">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Appointment Reminders</span>
                        </a>
                    </li>
                </ul>
            </li>

            <?php endif; ?>

            <!-- ==================== COMMON MENU ITEMS ==================== -->
            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Appointments</span>
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav sub-group-menu">
                    <li class="nav-item">
                        <a href="<?php echo url('/appointments'); ?>" class="nav-link">
                            <i class="fas fa-list"></i>
                            <span>All Appointments</span>
                        </a>
                    </li>
                    <?php if($current_role != 'client'): ?>
                    <li class="nav-item">
                        <a href="<?php echo url('/appointments/create'); ?>" class="nav-link">
                            <i class="fas fa-plus-circle"></i>
                            <span>Schedule Appointment</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/appointments/calendar'); ?>" class="nav-link">
                            <i class="fas fa-calendar"></i>
                            <span>Calendar View</span>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a href="<?php echo url('/appointments/book'); ?>" class="nav-link">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Book Appointment</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>

            <li class="nav-item sidebar-nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-bell"></i>
                    <span>Reminders</span>
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav sub-group-menu">
                    <?php if($current_role == 'client'): ?>
                    <li class="nav-item">
                        <a href="<?php echo url('/client/reminders'); ?>" class="nav-link">
                            <i class="fas fa-list"></i>
                            <span>My Reminders</span>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a href="<?php echo url('/reminders'); ?>" class="nav-link">
                            <i class="fas fa-list"></i>
                            <span>All Reminders</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo url('/reminders/create'); ?>" class="nav-link">
                            <i class="fas fa-plus-circle"></i>
                            <span>Create Reminder</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>

            <li class="nav-item">
                <a href="<?php echo url('/feedback'); ?>" class="nav-link">
                    <i class="fas fa-comment"></i>
                    <span>Feedback</span>
                </a>
            </li>
            <?php if($current_role == 'admin'): ?>
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