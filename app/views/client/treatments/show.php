<?php
$current_page = 'client_treatments';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
           
            <div class="col-auto">
                <a href="<?php echo url('/treatments'); ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

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
                                    <th>Veterinary:</th>
                                    <td><?php echo $treatment['vet_full_name']; ?></td>
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
                            <h6 class="border-bottom pb-2">Veterinary Notes</h6>
                            <p><?php echo nl2br(htmlspecialchars($treatment['treatment_notes'])); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
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
                    </table>
                    
                    <div class="text-center mt-3">
                        <a href="<?php echo url('/client/animals/' . $treatment['animal_id']); ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>View Animal Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- Veterinary Information -->
            <div class="dashboard-card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-md me-2"></i>Veterinary Information
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="avatar-lg bg-success rounded-circle mx-auto d-flex align-items-center justify-content-center mb-2">
                        <i class="fas fa-user-md text-white fa-2x"></i>
                    </div>
                    <h6><?php echo $treatment['vet_full_name']; ?></h6>
                    <p class="text-muted">Veterinary Doctor</p>
                    
                    <div class="mt-3">
                        <a href="mailto:<?php echo $treatment['vet_email']; ?>" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-envelope me-1"></i>Contact Veterinary
                        </a>
                    </div>
                </div>
            </div>

            <!-- Important Notes -->
            <?php if ($treatment['treatment_status'] == 'ongoing' || $treatment['treatment_status'] == 'follow_up'): ?>
            <div class="dashboard-card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>Important Notes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Treatment in Progress</strong>
                        <p class="mb-0 mt-2">Please follow all instructions provided by the veterinary and contact the clinic if you have any concerns.</p>
                    </div>
                    
                    <?php if ($treatment['follow_up_date']): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <strong>Follow-up Scheduled</strong>
                        <p class="mb-0 mt-2">Your next appointment is scheduled for <strong><?php echo formatDate($treatment['follow_up_date']); ?></strong>.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>