<div class="container-fluid">


    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Total Users</p>
                            <h4 class="mb-0"><?php echo $stats['total_users'] ?? 0; ?></h4>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-primary rounded-circle">
                                    <i class="fas fa-users fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Total Animals</p>
                            <h4 class="mb-0"><?php echo $stats['total_animals'] ?? 0; ?></h4>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-success rounded-circle">
                                    <i class="fas fa-paw fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Total Treatments</p>
                            <h4 class="mb-0"><?php echo $stats['total_treatments'] ?? 0; ?></h4>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-info rounded-circle">
                                    <i class="fas fa-stethoscope fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Active Treatments</p>
                            <h4 class="mb-0"><?php echo $stats['active_treatments'] ?? 0; ?></h4>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-warning rounded-circle">
                                    <i class="fas fa-heartbeat fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo url('/users/create'); ?>" class="btn btn-primary w-100">
                                <i class="fas fa-user-plus me-2"></i>
                                Add User
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo url('/animals/create'); ?>" class="btn btn-success w-100">
                                <i class="fas fa-plus me-2"></i>
                                Add Animal
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo url('/treatments/create'); ?>" class="btn btn-info w-100">
                                <i class="fas fa-stethoscope me-2"></i>
                                Add Treatment
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo url('/reports'); ?>" class="btn btn-warning w-100">
                                <i class="fas fa-chart-bar me-2"></i>
                                View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card-hover {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: none;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.avatar-title {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
}

.page-title-box {
    padding: 20px 0;
}

.breadcrumb {
    margin-bottom: 0;
    background: transparent;
}
</style>