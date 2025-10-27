<?php
$current_page = 'veterinary_treatments';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Treatment Details</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo url('/veterinary/dashboard'); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo url('/treatments'); ?>">My Treatments</a></li>
                        <li class="breadcrumb-item active">#<?php echo $treatment['treatment_id']; ?></li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <div class="btn-group">
                    <a href="<?php echo url('/treatments/' . $treatment['treatment_id'] . '/edit'); ?>" class="btn btn-secondary">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <?php if ($treatment['treatment_status'] != 'completed'): ?>
                        <form method="POST" action="<?php echo url('/treatments/' . $treatment['treatment_id'] . '/complete'); ?>" 
                              class="d-inline" onsubmit="return confirm('Mark this treatment as completed?')">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-2"></i>Complete
                            </button>
                        </form>
                    <?php endif; ?>
                    <a href="<?php echo url('/treatments'); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php 
    $flash = getFlashMessage();
    if ($flash): ?>
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show">
            <?php echo $flash['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Treatment Details -->
        <div class="col-lg-8">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-stethoscope me-2"></i>Treatment Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Treatment ID:</th>
                                    <td>#<?php echo $treatment['treatment_id']; ?></td>
                                </tr>
                                <tr>
                                    <th>Animal:</th>
                                    <td>
                                        <strong><?php echo $treatment['animal_name']; ?></strong>
                                        <br><small class="text-muted"><?php echo $treatment['species']; ?></small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Owner:</th>
                                    <td><?php echo $treatment['client_full_name']; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Treatment Date:</th>
                                    <td><?php echo formatDateTime($treatment['treatment_date'] . ' 00:00:00'); ?></td>
                                </tr>
                                <tr>
                                    <th>Follow-up Date:</th>
                                    <td>
                                        <?php if ($treatment['follow_up_date']): ?>
                                            <span class="badge bg-<?php 
                                                echo $treatment['follow_up_status'] == 'overdue' ? 'danger' : 'warning'; 
                                            ?>">
                                                <?php echo formatDate($treatment['follow_up_date']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">Not scheduled</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $treatment['treatment_status'] == 'completed' ? 'success' : 
                                                 ($treatment['treatment_status'] == 'ongoing' ? 'warning' : 'info'); 
                                        ?>">
                                            <?php echo ucfirst($treatment['treatment_status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Cost:</th>
                                    <td><strong class="text-primary">MK<?php echo number_format($treatment['cost'], 2); ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2">Diagnosis</h6>
                            <p><?php echo nl2br(htmlspecialchars($treatment['diagnosis'])); ?></p>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2">Treatment Details</h6>
                            <p><?php echo nl2br(htmlspecialchars($treatment['treatment_details'])); ?></p>
                        </div>
                    </div>

                    <?php if (!empty($treatment['medication_prescribed'])): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2">Medication Prescribed</h6>
                            <p><?php echo nl2br(htmlspecialchars($treatment['medication_prescribed'])); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($treatment['treatment_notes'])): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2">Additional Notes</h6>
                            <p><?php echo nl2br(htmlspecialchars($treatment['treatment_notes'])); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Follow-up Section -->
            <?php if ($treatment['treatment_status'] != 'completed'): ?>
            <div class="dashboard-card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-plus me-2"></i>Schedule Follow-up
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo url('/treatments/' . $treatment['treatment_id'] . '/schedule-followup'); ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="follow_up_date" class="form-label">Follow-up Date *</label>
                                    <input type="date" class="form-control" id="follow_up_date" name="follow_up_date" 
                                           min="<?php echo date('Y-m-d'); ?>" 
                                           value="<?php echo $treatment['follow_up_date'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="1" 
                                              placeholder="Follow-up instructions..."><?php echo $treatment['treatment_notes'] ?? ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-calendar-check me-2"></i>Schedule Follow-up
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar Information -->
        <div class="col-lg-4">
            <!-- Animal Information -->
            <div class="dashboard-card">
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
                        <h5><?php echo $treatment['animal_name']; ?></h5>
                        <p class="text-muted"><?php echo $treatment['species']; ?> • <?php echo ucfirst($treatment['gender']); ?></p>
                    </div>
                    
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th>Breed:</th>
                            <td><?php echo $treatment['breed'] ?? 'Unknown'; ?></td>
                        </tr>
                        <tr>
                            <th>Color:</th>
                            <td><?php echo $treatment['color'] ?? 'Unknown'; ?></td>
                        </tr>
                        <tr>
                            <th>Weight:</th>
                            <td><?php echo $treatment['weight'] ? $treatment['weight'] . ' kg' : 'Unknown'; ?></td>
                        </tr>
                        <tr>
                            <th>Age:</th>
                            <td><?php echo calculateAge($treatment['birth_date']); ?></td>
                        </tr>
                        <tr>
                            <th>Microchip:</th>
                            <td><?php echo $treatment['microchip'] ?? 'Not chipped'; ?></td>
                        </tr>
                    </table>
                    
                    <div class="text-center mt-3">
                        <a href="<?php echo url('/veterinary/animals/' . $treatment['animal_id']); ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>View Animal
                        </a>
                    </div>
                </div>
            </div>

            <!-- Client Information -->
            <div class="dashboard-card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Client Information
                    </h5>
                </div>
                <div class="card-body">
                    <h6><?php echo $treatment['client_full_name']; ?></h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><i class="fas fa-phone text-muted me-2"></i></td>
                            <td><?php echo $treatment['client_phone'] ?? 'Not provided'; ?></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-envelope text-muted me-2"></i></td>
                            <td><?php echo $treatment['client_email']; ?></td>
                        </tr>
                    </table>
                    <div class="text-center mt-3">
                        <a href="tel:<?php echo $treatment['client_phone']; ?>" class="btn btn-outline-success btn-sm me-2">
                            <i class="fas fa-phone me-1"></i>Call
                        </a>
                        <a href="mailto:<?php echo $treatment['client_email']; ?>" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-envelope me-1"></i>Email
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo url('/treatments/create?animal_id=' . $treatment['animal_id']); ?>" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>New Treatment for Same Animal
                        </a>
                        <a href="<?php echo url('/appointments/create?animal_id=' . $treatment['animal_id']); ?>" class="btn btn-outline-success">
                            <i class="fas fa-calendar-plus me-2"></i>Schedule Appointment
                        </a>
                        <a href="<?php echo url('/vaccines/create?animal_id=' . $treatment['animal_id']); ?>" class="btn btn-outline-info">
                            <i class="fas fa-syringe me-2"></i>Record Vaccination
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>