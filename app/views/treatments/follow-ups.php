<?php
$current_page = 'treatment_followups';
$userRole = $_SESSION['role'];
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            
            <div class="col-auto">
                <a href="<?php echo url('/treatments'); ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Treatments
                </a>
            </div>
        </div>
    </div>

    <!-- Overdue Follow-ups -->
    <?php if (!empty($overdueFollowUps)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="dashboard-card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Overdue Follow-ups
                        <span class="badge bg-light text-danger ms-2"><?php echo count($overdueFollowUps); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Animal & Owner</th>
                                    <th>Diagnosis</th>
                                    <th>Veterinary</th>
                                    <th>Follow-up Date</th>
                                    <th>Days Overdue</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($overdueFollowUps as $treatment): ?>
                                <tr class="table-danger">
                                    <td>
                                        <strong><?php echo $treatment['animal_name']; ?></strong>
                                        <br><small class="text-muted"><?php echo $treatment['client_full_name']; ?></small>
                                    </td>
                                    <td><?php echo strLimit($treatment['diagnosis'], 50); ?></td>
                                    <td><?php echo $treatment['vet_full_name']; ?></td>
                                    <td>
                                        <span class="badge bg-danger">
                                            <?php echo formatDate($treatment['follow_up_date']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-danger">
                                            <?php echo $treatment['days_since_treatment']; ?> days
                                        </strong>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo url('/treatments/' . $treatment['treatment_id']); ?>" 
                                               class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo url('/treatments/' . $treatment['treatment_id'] . '/edit'); ?>" 
                                               class="btn btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?php echo url('/appointments/create?animal_id=' . $treatment['animal_id'] . '&treatment_id=' . $treatment['treatment_id']); ?>" 
                                               class="btn btn-outline-success">
                                                <i class="fas fa-calendar-plus"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Upcoming Follow-ups -->
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-check me-2"></i>Upcoming Follow-ups
                        <span class="badge bg-primary ms-2"><?php echo count($upcomingFollowUps); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($upcomingFollowUps)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                            <h5>No upcoming follow-ups</h5>
                            <p class="text-muted">All follow-ups are properly scheduled or completed.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Animal & Owner</th>
                                        <th>Diagnosis</th>
                                        <th>Veterinary</th>
                                        <th>Treatment Date</th>
                                        <th>Follow-up Date</th>
                                        <th>Days Until</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcomingFollowUps as $treatment): 
                                        $daysUntil = floor((strtotime($treatment['follow_up_date']) - time()) / (60 * 60 * 24));
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $treatment['animal_name']; ?></strong>
                                            <br><small class="text-muted"><?php echo $treatment['client_full_name']; ?></small>
                                        </td>
                                        <td><?php echo strLimit($treatment['diagnosis'], 50); ?></td>
                                        <td><?php echo $treatment['vet_full_name']; ?></td>
                                        <td><?php echo formatDate($treatment['treatment_date']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $daysUntil <= 3 ? 'warning' : 'info'; ?>">
                                                <?php echo formatDate($treatment['follow_up_date']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-<?php echo $daysUntil <= 3 ? 'warning' : 'success'; ?>">
                                                <strong><?php echo $daysUntil; ?> days</strong>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo url('/treatments/' . $treatment['treatment_id']); ?>" 
                                                   class="btn btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo url('/treatments/' . $treatment['treatment_id'] . '/edit'); ?>" 
                                                   class="btn btn-outline-secondary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?php echo url('/appointments/create?animal_id=' . $treatment['animal_id'] . '&treatment_id=' . $treatment['treatment_id']); ?>" 
                                                   class="btn btn-outline-success">
                                                    <i class="fas fa-calendar-plus"></i>
                                                </a>
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