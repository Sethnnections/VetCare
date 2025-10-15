<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Client Dashboard</h4>
            </div>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="row">
        <div class="col-12">
            <div class="card welcome-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="card-title text-primary">
                                Welcome back, <?php echo $_SESSION['first_name'] ?? $_SESSION['username']; ?>!
                            </h3>
                            <p class="card-text text-muted">
                                Manage your animals, view treatments, and stay updated with vaccination schedules.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="welcome-icon">
                                <i class="fas fa-paw fa-3x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mt-4">
        <div class="col-xl-6 col-md-6">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">My Animals</p>
                            <h4 class="mb-0"><?php echo $stats['my_animals'] ?? 0; ?></h4>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="avatar-title bg-success rounded-circle">
                                <i class="fas fa-paw fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Active Animals</p>
                            <h4 class="mb-0"><?php echo $stats['active_animals'] ?? 0; ?></h4>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="avatar-title bg-info rounded-circle">
                                <i class="fas fa-heart fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="<?php echo url('/client/animals'); ?>" class="btn btn-primary w-100">
                                <i class="fas fa-list me-2"></i>
                                View My Animals
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="<?php echo url('/client/animals/add'); ?>" class="btn btn-success w-100">
                                <i class="fas fa-plus me-2"></i>
                                Add New Animal
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="<?php echo url('/appointments'); ?>" class="btn btn-info w-100">
                                <i class="fas fa-calendar-check me-2"></i>
                                Book Appointment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.welcome-card {
    background: linear-gradient(135deg, var(--light-cream) 0%, #ffffff 100%);
    border: none;
    border-radius: 15px;
}

.welcome-icon {
    opacity: 0.7;
}
</style>