<?php
$current_page = 'vaccinations_index';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
          
            <div class="col-auto">
                <div class="btn-group">
                    <a href="<?php echo url('/vaccinations/create'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Record Vaccination
                    </a>
                    <a href="<?php echo url('/vaccinations/calendar'); ?>" class="btn btn-info">
                        <i class="fas fa-calendar me-2"></i>Calendar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Quick Stats -->
            <div class="row mb-4">
                <div class="col-xl-2 col-md-4">
                    <div class="card dashboard-card card-stats">
                        <div class="card-body text-center">
                            <h3 class="text-primary"><?php echo $stats['total_vaccinations'] ?? 0; ?></h3>
                            <p class="text-muted mb-0">Total</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4">
                    <div class="card dashboard-card card-stats">
                        <div class="card-body text-center">
                            <h3 class="text-success"><?php echo $stats['completed'] ?? 0; ?></h3>
                            <p class="text-muted mb-0">Completed</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4">
                    <div class="card dashboard-card card-stats">
                        <div class="card-body text-center">
                            <h3 class="text-warning"><?php echo $stats['scheduled'] ?? 0; ?></h3>
                            <p class="text-muted mb-0">Scheduled</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4">
                    <div class="card dashboard-card card-stats">
                        <div class="card-body text-center">
                            <h3 class="text-danger"><?php echo $stats['overdue'] ?? 0; ?></h3>
                            <p class="text-muted mb-0">Overdue</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4">
                    <div class="card dashboard-card card-stats">
                        <div class="card-body text-center">
                            <h3 class="text-info"><?php echo $stats['due_soon'] ?? 0; ?></h3>
                            <p class="text-muted mb-0">Due Soon</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4">
                    <div class="card dashboard-card card-stats">
                        <div class="card-body text-center">
                            <h3 class="text-purple"><?php echo $stats['this_month'] ?? 0; ?></h3>
                            <p class="text-muted mb-0">This Month</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Vaccinations -->
            <?php
            $upcomingVaccinations = array_filter($vaccinations, function($v) {
                $status = $v['vaccine_status'] ?? $v['status'];
                return $status === 'scheduled' && strtotime($v['vaccine_date']) >= time();
            });
            ?>
            
            <?php if (!empty($upcomingVaccinations)): ?>
            <div class="card dashboard-card mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>Upcoming Vaccinations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php 
                        $upcomingCount = 0;
                        foreach ($upcomingVaccinations as $vaccination): 
                            if ($upcomingCount >= 6) break;
                        ?>
                        <div class="col-md-4 mb-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo $vaccination['vaccine_name']; ?></h6>
                                    <p class="card-text mb-1">
                                        <small class="text-muted">
                                            <i class="fas fa-paw me-1"></i><?php echo $vaccination['animal_name']; ?>
                                        </small>
                                    </p>
                                    <p class="card-text mb-1">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i><?php echo formatDate($vaccination['vaccine_date']); ?>
                                        </small>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-warning">Scheduled</span>
                                        <a href="<?php echo url('/vaccinations/' . $vaccination['vaccine_id']); ?>" 
                                           class="btn btn-sm btn-outline-primary">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                        $upcomingCount++;
                        endforeach; 
                        ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- All Vaccinations -->
            <div class="card dashboard-card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>All Vaccination Records
                            </h5>
                        </div>
                        <div class="col-auto">
                            <form method="GET" class="d-flex">
                                <input type="text" class="form-control me-2" name="search" 
                                       value="<?php echo $search ?? ''; ?>" placeholder="Search...">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($vaccinations)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-syringe fa-3x text-muted mb-3"></i>
                            <h5>No vaccination records found</h5>
                            <p class="text-muted">You haven't recorded any vaccinations yet.</p>
                            <a href="<?php echo url('/vaccinations/create'); ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Record Your First Vaccination
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Vaccine</th>
                                        <th>Animal</th>
                                        <th>Date</th>
                                        <th>Next Due</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($vaccinations as $vaccination): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary rounded-circle me-3 flex-shrink-0">
                                                    <i class="fas fa-syringe text-white"></i>
                                                </div>
                                                <div>
                                                    <strong><?php echo $vaccination['vaccine_name']; ?></strong>
                                                    <?php if ($vaccination['batch_number']): ?>
                                                    <br><small class="text-muted">Batch: <?php echo $vaccination['batch_number']; ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong><?php echo $vaccination['animal_name']; ?></strong>
                                            <br><small class="text-muted"><?php echo $vaccination['species']; ?></small>
                                        </td>
                                        <td><?php echo formatDate($vaccination['vaccine_date']); ?></td>
                                        <td>
                                            <span class="<?php echo strtotime($vaccination['next_due_date']) < time() ? 'text-danger' : 'text-success'; ?>">
                                                <?php echo formatDate($vaccination['next_due_date']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $statusBadge = [
                                                'completed' => 'success',
                                                'scheduled' => 'warning',
                                                'overdue' => 'danger'
                                            ];
                                            $status = $vaccination['vaccine_status'] ?? $vaccination['status'];
                                            $badgeClass = $statusBadge[$status] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?php echo $badgeClass; ?>">
                                                <?php echo ucfirst($status); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo url('/vaccinations/' . $vaccination['vaccine_id']); ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo url('/vaccinations/' . $vaccination['vaccine_id'] . '/edit'); ?>" 
                                                   class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if ($status === 'scheduled'): ?>
                                                <form method="POST" action="<?php echo url('/vaccinations/' . $vaccination['vaccine_id'] . '/complete'); ?>" 
                                                      class="d-inline" onsubmit="return confirm('Mark this vaccination as completed?')">
                                                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>