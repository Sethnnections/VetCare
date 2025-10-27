<?php
$current_page = 'treatments_edit';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
         
            <div class="col-auto">
                <a href="<?php echo url('/treatments/' . $treatment['treatment_id']); ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Treatment
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Treatment Information
                    </h5>
                </div>
                <div class="card-body">
                    <?php 
                    $flash = getFlashMessage();
                    if ($flash): ?>
                        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show">
                            <?php echo $flash['message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo url('/treatments/' . $treatment['treatment_id'] . '/update'); ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="animal_id" class="form-label">Animal *</label>
                                    <select class="form-control" id="animal_id" name="animal_id" required disabled>
                                        <option value="<?php echo $treatment['animal_id']; ?>" selected>
                                            <?php echo $treatment['animal_name']; ?> (<?php echo $treatment['species']; ?>)
                                        </option>
                                    </select>
                                    <input type="hidden" name="animal_id" value="<?php echo $treatment['animal_id']; ?>">
                                    <small class="text-muted">Animal cannot be changed after treatment creation</small>
                                </div>
                            </div>
                            
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="veterinary_id" class="form-label">Veterinary *</label>
                                    <select class="form-control" id="veterinary_id" name="veterinary_id" required>
                                        <option value="">Select Veterinary</option>
                                        <?php foreach ($veterinarians as $vet): ?>
                                            <option value="<?php echo $vet['user_id']; ?>" 
                                                <?php echo ($treatment['veterinary_id'] == $vet['user_id']) ? 'selected' : ''; ?>>
                                                <?php echo $vet['first_name'] . ' ' . $vet['last_name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['veterinary_id'])): ?>
                                        <div class="text-danger small"><?php echo $errors['veterinary_id']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php else: ?>
                                <input type="hidden" name="veterinary_id" value="<?php echo $treatment['veterinary_id']; ?>">
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="treatment_date" class="form-label">Treatment Date *</label>
                                    <input type="date" class="form-control" id="treatment_date" name="treatment_date" 
                                           max="<?php echo date('Y-m-d'); ?>" 
                                           value="<?php echo $treatment['treatment_date']; ?>" required>
                                    <?php if (isset($errors['treatment_date'])): ?>
                                        <div class="text-danger small"><?php echo $errors['treatment_date']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="follow_up_date" class="form-label">Follow-up Date</label>
                                    <input type="date" class="form-control" id="follow_up_date" name="follow_up_date" 
                                           min="<?php echo date('Y-m-d'); ?>" 
                                           value="<?php echo $treatment['follow_up_date'] ?? ''; ?>">
                                    <?php if (isset($errors['follow_up_date'])): ?>
                                        <div class="text-danger small"><?php echo $errors['follow_up_date']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="diagnosis" class="form-label">Diagnosis *</label>
                            <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" 
                                      placeholder="Enter the diagnosis..." required><?php echo $treatment['diagnosis']; ?></textarea>
                            <?php if (isset($errors['diagnosis'])): ?>
                                <div class="text-danger small"><?php echo $errors['diagnosis']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="treatment_details" class="form-label">Treatment Details *</label>
                            <textarea class="form-control" id="treatment_details" name="treatment_details" rows="4" 
                                      placeholder="Describe the treatment provided..." required><?php echo $treatment['treatment_details']; ?></textarea>
                            <?php if (isset($errors['treatment_details'])): ?>
                                <div class="text-danger small"><?php echo $errors['treatment_details']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="medication_prescribed" class="form-label">Medication Prescribed</label>
                            <textarea class="form-control" id="medication_prescribed" name="medication_prescribed" rows="2" 
                                      placeholder="List any medications prescribed..."><?php echo $treatment['medication_prescribed'] ?? ''; ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cost" class="form-label">Cost (MK)</label>
                                    <input type="number" class="form-control" id="cost" name="cost" 
                                           step="0.01" min="0" 
                                           value="<?php echo $treatment['cost'] ?? '0.00'; ?>">
                                    <?php if (isset($errors['cost'])): ?>
                                        <div class="text-danger small"><?php echo $errors['cost']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="ongoing" <?php echo $treatment['status'] == 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                                        <option value="completed" <?php echo $treatment['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="follow_up" <?php echo $treatment['status'] == 'follow_up' ? 'selected' : ''; ?>>Follow-up Required</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" 
                                      placeholder="Any additional notes or observations..."><?php echo $treatment['notes'] ?? ''; ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo url('/treatments/' . $treatment['treatment_id']); ?>" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Treatment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Treatment Summary -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Treatment Summary
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th>Treatment ID:</th>
                            <td>#<?php echo $treatment['treatment_id']; ?></td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td><?php echo formatDateTime($treatment['created_at']); ?></td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td><?php echo formatDateTime($treatment['updated_at']); ?></td>
                        </tr>
                        <tr>
                            <th>Current Status:</th>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $treatment['status'] == 'completed' ? 'success' : 
                                         ($treatment['status'] == 'ongoing' ? 'warning' : 'info'); 
                                ?>">
                                    <?php echo ucfirst($treatment['status']); ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Update Guidelines -->
            <div class="dashboard-card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Update Guidelines
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6>Best Practices</h6>
                        <ul class="mb-0 ps-3 small">
                            <li>Update status when treatment progresses</li>
                            <li>Add follow-up dates for ongoing care</li>
                            <li>Record accurate costs for billing</li>
                            <li>Keep notes updated with new observations</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>