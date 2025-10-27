<?php
$current_page = 'vaccinations_show';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
           
            <div class="col-auto">
                <div class="btn-group">
                    <a href="<?php echo url('/admin/vaccinations'); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                    <a href="<?php echo url('/vaccinations/' . $vaccination['vaccine_id'] . '/certificate'); ?>" 
                       class="btn btn-success" target="_blank">
                        <i class="fas fa-certificate me-2"></i>Certificate
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-syringe me-2"></i>Vaccination Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Vaccine Name:</th>
                                    <td><?php echo $vaccination['vaccine_name']; ?></td>
                                </tr>
                                <tr>
                                    <th>Vaccine Type:</th>
                                    <td>
                                        <span class="badge bg-info"><?php echo $vaccination['vaccine_type'] ?? 'Not specified'; ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Batch Number:</th>
                                    <td><?php echo $vaccination['batch_number'] ?? 'N/A'; ?></td>
                                </tr>
                                <tr>
                                    <th>Manufacturer:</th>
                                    <td><?php echo $vaccination['manufacturer'] ?? 'N/A'; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Vaccination Date:</th>
                                    <td><?php echo formatDate($vaccination['vaccine_date']); ?></td>
                                </tr>
                                <tr>
                                    <th>Next Due Date:</th>
                                    <td>
                                        <span class="<?php echo strtotime($vaccination['next_due_date']) < time() ? 'text-danger' : 'text-success'; ?>">
                                            <?php echo formatDate($vaccination['next_due_date']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <?php
                                        $statusBadge = [
                                            'completed' => 'success',
                                            'scheduled' => 'warning',
                                            'overdue' => 'danger',
                                            'verified' => 'info'
                                        ];
                                        $status = $vaccination['vaccine_status'] ?? $vaccination['status'];
                                        $badgeClass = $statusBadge[$status] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $badgeClass; ?>">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Administered By:</th>
                                    <td><?php echo $vaccination['administered_by_name'] ?? 'Unknown'; ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <?php if (!empty($vaccination['dosage']) || !empty($vaccination['route'])): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2">Medical Details</h6>
                            <div class="row">
                                <?php if (!empty($vaccination['dosage'])): ?>
                                <div class="col-md-3">
                                    <strong>Dosage:</strong><br>
                                    <span class="text-muted"><?php echo $vaccination['dosage']; ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($vaccination['route'])): ?>
                                <div class="col-md-3">
                                    <strong>Route:</strong><br>
                                    <span class="text-muted"><?php echo ucfirst($vaccination['route']); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($vaccination['site'])): ?>
                                <div class="col-md-3">
                                    <strong>Site:</strong><br>
                                    <span class="text-muted"><?php echo ucfirst($vaccination['site']); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($vaccination['notes'])): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2">Notes</h6>
                            <p class="text-muted"><?php echo nl2br(htmlspecialchars($vaccination['notes'])); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($vaccination['reaction_notes'])): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 text-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>Reaction Notes
                            </h6>
                            <div class="alert alert-warning">
                                <?php echo nl2br(htmlspecialchars($vaccination['reaction_notes'])); ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Animal Information -->
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-paw me-2"></i>Animal Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-lg bg-primary rounded-circle mx-auto d-flex align-items-center justify-content-center mb-2">
                            <i class="fas fa-paw text-white fa-2x"></i>
                        </div>
                        <h6><?php echo $vaccination['animal_name']; ?></h6>
                        <p class="text-muted"><?php echo $vaccination['species']; ?> • <?php echo $vaccination['breed'] ?? 'Unknown breed'; ?></p>
                    </div>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th>Age:</th>
                            <td>
                                <?php
                                $birthDate = $vaccination['birth_date'] ?? null;
                                if ($birthDate) {
                                    $age = date_diff(date_create($birthDate), date_create('today'))->y;
                                    echo $age . ' years';
                                } else {
                                    echo 'Unknown';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Gender:</th>
                            <td><?php echo ucfirst($vaccination['gender'] ?? 'Unknown'); ?></td>
                        </tr>
                        <tr>
                            <th>Color:</th>
                            <td><?php echo $vaccination['color'] ?? 'Unknown'; ?></td>
                        </tr>
                        <tr>
                            <th>Weight:</th>
                            <td><?php echo $vaccination['weight'] ? $vaccination['weight'] . ' kg' : 'Unknown'; ?></td>
                        </tr>
                    </table>
                    <div class="text-center mt-3">
                        <a href="<?php echo url('/admin/animals/' . $vaccination['animal_id']); ?>" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>View Animal
                        </a>
                    </div>
                </div>
            </div>

            <!-- Client Information -->
            <div class="card dashboard-card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Client Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-lg bg-success rounded-circle mx-auto d-flex align-items-center justify-content-center mb-2">
                            <i class="fas fa-user text-white fa-2x"></i>
                        </div>
                        <h6><?php echo $vaccination['client_full_name']; ?></h6>
                        <p class="text-muted">Animal Owner</p>
                    </div>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th>Phone:</th>
                            <td><?php echo $vaccination['client_phone'] ?? 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td><?php echo $vaccination['client_email'] ?? 'N/A'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card dashboard-card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo url('/vaccinations/' . $vaccination['vaccine_id'] . '/edit'); ?>" 
                           class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Vaccination
                        </a>
                        <?php if (($vaccination['vaccine_status'] ?? $vaccination['status']) === 'scheduled'): ?>
                        <form method="POST" action="<?php echo url('/vaccinations/' . $vaccination['vaccine_id'] . '/complete'); ?>" class="d-grid">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-2"></i>Mark as Completed
                            </button>
                        </form>
                        <?php endif; ?>
                        <a href="<?php echo url('/animals/' . $vaccination['animal_id'] . '/vaccination-report'); ?>" 
                           class="btn btn-info">
                            <i class="fas fa-file-medical me-2"></i>Vaccination Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>