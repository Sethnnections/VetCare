<?php
$workloadStats = $workloadStats ?? [];
$assignmentStats = $assignmentStats ?? [];
$current_page = 'admin_animals_workload';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Veterinary Workload Report
                    </h4>
                    <div class="btn-group">
                        <a href="<?php echo url('/admin/animals'); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Animals
                        </a>
                        <a href="<?php echo url('/admin/animal-assignments'); ?>" class="btn btn-primary">
                            <i class="fas fa-user-md me-1"></i>Assignment Management
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Summary Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-primary"><?php echo $assignmentStats['total_veterinarians'] ?? 0; ?></h3>
                                    <p class="text-muted mb-0">Active Veterinarians</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-success"><?php echo $assignmentStats['assigned_animals'] ?? 0; ?></h3>
                                    <p class="text-muted mb-0">Assigned Animals</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-warning"><?php echo $assignmentStats['unassigned_animals'] ?? 0; ?></h3>
                                    <p class="text-muted mb-0">Unassigned Animals</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-info"><?php echo $assignmentStats['total_animals'] ?? 0; ?></h3>
                                    <p class="text-muted mb-0">Total Active Animals</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Workload Details -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Veterinary Workload Details</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($workloadStats)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-user-md fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No veterinary workload data available</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Veterinary</th>
                                                <th>Assigned Animals</th>
                                                <th>Active Treatments</th>
                                                <th>Workload Level</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($workloadStats as $vet): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="user-avatar me-3">
                                                            <?php 
                                                            $initials = strtoupper(
                                                                substr($vet['first_name'], 0, 1) . 
                                                                substr($vet['last_name'], 0, 1)
                                                            );
                                                            echo $initials;
                                                            ?>
                                                        </div>
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($vet['first_name'] . ' ' . $vet['last_name']); ?></strong>
                                                            <br>
                                                            <small class="text-muted">Veterinary</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-center">
                                                        <h4 class="mb-0 
                                                            <?php echo ($vet['assigned_animals'] ?? 0) > 10 ? 'text-danger' : 
                                                                  (($vet['assigned_animals'] ?? 0) > 5 ? 'text-warning' : 'text-success'); ?>">
                                                            <?php echo $vet['assigned_animals'] ?? 0; ?>
                                                        </h4>
                                                        <small class="text-muted">Animals</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-center">
                                                        <h4 class="mb-0 text-info"><?php echo $vet['active_treatments'] ?? 0; ?></h4>
                                                        <small class="text-muted">Active Treatments</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $workloadLevel = 'Low';
                                                    $workloadClass = 'success';
                                                    $animalCount = $vet['assigned_animals'] ?? 0;
                                                    
                                                    if ($animalCount > 10) {
                                                        $workloadLevel = 'High';
                                                        $workloadClass = 'danger';
                                                    } elseif ($animalCount > 5) {
                                                        $workloadLevel = 'Medium';
                                                        $workloadClass = 'warning';
                                                    }
                                                    ?>
                                                    <span class="badge bg-<?php echo $workloadClass; ?>">
                                                        <?php echo $workloadLevel; ?> Workload
                                                    </span>
                                                    <div class="progress mt-2" style="height: 8px;">
                                                        <?php 
                                                        $percentage = min(100, ($animalCount / 15) * 100);
                                                        ?>
                                                        <div class="progress-bar bg-<?php echo $workloadClass; ?>" 
                                                             style="width: <?php echo $percentage; ?>%">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted"><?php echo round($percentage); ?>% capacity</small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?php echo url('/admin/animals?veterinary=' . $vet['user_id']); ?>" 
                                                           class="btn btn-outline-primary" title="View Assigned Animals">
                                                            <i class="fas fa-paw"></i>
                                                        </a>
                                                        <a href="<?php echo url('/treatments?veterinary_id=' . $vet['user_id']); ?>" 
                                                           class="btn btn-outline-info" title="View Treatments">
                                                            <i class="fas fa-stethoscope"></i>
                                                        </a>
                                                        <a href="<?php echo url('/users/' . $vet['user_id']); ?>" 
                                                           class="btn btn-outline-secondary" title="View Profile">
                                                            <i class="fas fa-user"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Workload Distribution Chart -->
                                <div class="row mt-4">
                                    <div class="col-md-8">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">Workload Distribution</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="workload-chart">
                                                    <?php foreach ($workloadStats as $vet): ?>
                                                    <div class="workload-bar mb-2">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span class="vet-name"><?php echo htmlspecialchars($vet['first_name'] . ' ' . $vet['last_name']); ?></span>
                                                            <span class="animal-count"><?php echo $vet['assigned_animals'] ?? 0; ?> animals</span>
                                                        </div>
                                                        <?php 
                                                        $percentage = min(100, (($vet['assigned_animals'] ?? 0) / 15) * 100);
                                                        $barClass = ($vet['assigned_animals'] ?? 0) > 10 ? 'high' : 
                                                                   (($vet['assigned_animals'] ?? 0) > 5 ? 'medium' : 'low');
                                                        ?>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar workload-<?php echo $barClass; ?>" 
                                                                 style="width: <?php echo $percentage; ?>%">
                                                                <span class="progress-text"><?php echo round($percentage); ?>%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">Workload Guidelines</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="workload-guidelines">
                                                    <div class="guideline-item mb-3">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <div class="guideline-color low"></div>
                                                            <strong>Low Workload (0-5 animals)</strong>
                                                        </div>
                                                        <small class="text-muted">Ideal for new assignments</small>
                                                    </div>
                                                    <div class="guideline-item mb-3">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <div class="guideline-color medium"></div>
                                                            <strong>Medium Workload (6-10 animals)</strong>
                                                        </div>
                                                        <small class="text-muted">Manageable workload</small>
                                                    </div>
                                                    <div class="guideline-item">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <div class="guideline-color high"></div>
                                                            <strong>High Workload (11+ animals)</strong>
                                                        </div>
                                                        <small class="text-muted">Consider redistributing</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--primary-red);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
}

.workload-low {
    background-color: #28a745;
}

.workload-medium {
    background-color: #ffc107;
}

.workload-high {
    background-color: #dc3545;
}

.workload-bar .progress {
    background-color: #f8f9fa;
    border-radius: 10px;
    overflow: hidden;
}

.workload-bar .progress-bar {
    border-radius: 10px;
    position: relative;
}

.progress-text {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: white;
    font-weight: bold;
    font-size: 0.8rem;
}

.guideline-color {
    width: 20px;
    height: 20px;
    border-radius: 4px;
    margin-right: 10px;
}

.guideline-color.low {
    background-color: #28a745;
}

.guideline-color.medium {
    background-color: #ffc107;
}

.guideline-color.high {
    background-color: #dc3545;
}

.workload-chart {
    max-height: 400px;
    overflow-y: auto;
}

.vet-name {
    font-weight: 500;
}

.animal-count {
    color: #6c757d;
    font-size: 0.9rem;
}
</style>