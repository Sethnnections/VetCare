<div class="container-fluid">


    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-4 col-md-6">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Total Animals</p>
                            <h4 class="mb-0"><?php echo $stats['total_animals'] ?? 0; ?></h4>
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

        <div class="col-xl-4 col-md-6">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">My Treatments</p>
                            <h4 class="mb-0"><?php echo $stats['my_treatments'] ?? 0; ?></h4>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="avatar-title bg-info rounded-circle">
                                <i class="fas fa-stethoscope fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Pending Treatments</p>
                            <h4 class="mb-0"><?php echo $stats['pending_treatments'] ?? 0; ?></h4>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="avatar-title bg-warning rounded-circle">
                                <i class="fas fa-clock fs-4"></i>
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
                            <a href="<?php echo url('/treatments/create'); ?>" class="btn btn-primary w-100">
                                <i class="fas fa-stethoscope me-2"></i>
                                Add Treatment
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="<?php echo url('/vaccines'); ?>" class="btn btn-success w-100">
                                <i class="fas fa-syringe me-2"></i>
                                Manage Vaccines
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="<?php echo url('/appointments'); ?>" class="btn btn-info w-100">
                                <i class="fas fa-calendar-check me-2"></i>
                                View Appointments
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>