<?php
$current_page = 'vaccinations_upcoming';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
         
            <div class="col-auto">
                <a href="<?php echo url('/vaccinations'); ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Overdue Vaccinations -->
        <?php if (!empty($overdueVaccinations)): ?>
        <div class="col-lg-6">
            <div class="card dashboard-card">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Overdue Vaccinations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Animal</th>
                                    <th>Vaccine</th>
                                    <th>Due Date</th>
                                    <th>Days Overdue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($overdueVaccinations as $vaccine): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo $vaccine['animal_name']; ?></strong>
                                        <br><small class="text-muted"><?php echo $vaccine['species']; ?></small>
                                    </td>
                                    <td><?php echo $vaccine['vaccine_name']; ?></td>
                                    <td>
                                        <span class="text-danger"><?php echo formatDate($vaccine['next_due_date']); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">
                                            <?php echo $vaccine['days_until_due'] * -1; ?> days
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Upcoming Vaccinations -->
        <div class="col-lg-6">
            <div class="card dashboard-card">
                <div class="card-header bg-warning">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>Upcoming Vaccinations (Next 30 Days)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($upcomingVaccinations)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Animal</th>
                                    <th>Vaccine</th>
                                    <th>Due Date</th>
                                    <th>Days Until Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcomingVaccinations as $vaccine): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo $vaccine['animal_name']; ?></strong>
                                        <br><small class="text-muted"><?php echo $vaccine['species']; ?></small>
                                    </td>
                                    <td><?php echo $vaccine['vaccine_name']; ?></td>
                                    <td><?php echo formatDate($vaccine['next_due_date']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $vaccine['days_until_due'] <= 7 ? 'warning' : 'info'; ?>">
                                            <?php echo $vaccine['days_until_due']; ?> days
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                        <h5>No Upcoming Vaccinations</h5>
                        <p class="text-muted">All vaccinations are up to date for the next 30 days.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card dashboard-card stat-card primary">
                <div class="card-body text-center py-3">
                    <h3 class="stat-value mb-1"><?php echo count($upcomingVaccinations); ?></h3>
                    <p class="stat-label mb-0">Upcoming</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-card stat-card danger">
                <div class="card-body text-center py-3">
                    <h3 class="stat-value mb-1"><?php echo count($overdueVaccinations); ?></h3>
                    <p class="stat-label mb-0">Overdue</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-card stat-card warning">
                <div class="card-body text-center py-3">
                    <h3 class="stat-value mb-1"><?php echo count(array_filter($upcomingVaccinations, function($v) { return $v['days_until_due'] <= 7; })); ?></h3>
                    <p class="stat-label mb-0">Due This Week</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-card stat-card success">
                <div class="card-body text-center py-3">
                    <h3 class="stat-value mb-1"><?php echo count($upcomingVaccinations) + count($overdueVaccinations); ?></h3>
                    <p class="stat-label mb-0">Total Actions</p>
                </div>
            </div>
        </div>
    </div>
</div>