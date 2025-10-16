<?php
// app/views/client/dashboard.php
$current_page = 'dashboard';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-tachometer-alt me-2"></i>Client Dashboard
                    </h4>
                </div>
                <div class="card-body">
                    <?php 
                    $flash = getFlashMessage();
                    if ($flash): ?>
                        <div class="alert alert-<?php echo $flash['type']; ?>">
                            <?php echo $flash['message']; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        Welcome to your dashboard! Your profile is complete.
                    </div>
                    
                    <div class="quick-actions">
                        <a href="<?php echo url('/client/animals'); ?>" class="action-btn">
                            <i class="fas fa-paw"></i>
                            <span>My Animals</span>
                        </a>
                        <a href="<?php echo url('/client/profile'); ?>" class="action-btn">
                            <i class="fas fa-user"></i>
                            <span>My Profile</span>
                        </a>
                        <a href="<?php echo url('/appointments'); ?>" class="action-btn">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Appointments</span>
                        </a>
                        <a href="<?php echo url('/client/reminders'); ?>" class="action-btn">
                            <i class="fas fa-bell"></i>
                            <span>Reminders</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>