<?php
$current_page = 'vaccinations_schedule';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Schedule Vaccination</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo url('/dashboard'); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo url('/vaccinations'); ?>">Vaccinations</a></li>
                        <li class="breadcrumb-item active">Schedule</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <a href="<?php echo url('/vaccinations'); ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-plus me-2"></i>Schedule Future Vaccination
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

                    <form method="POST" action="<?php echo url('/vaccinations/schedule'); ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="animal_id" class="form-label">Animal *</label>
                                    <select class="form-control" id="animal_id" name="animal_id" required>
                                        <option value="">Select Animal</option>
                                        <?php foreach ($animals as $animal): ?>
                                            <option value="<?php echo $animal['animal_id']; ?>" 
                                                <?php echo (isset($old['animal_id']) && $old['animal_id'] == $animal['animal_id']) ? 'selected' : ''; ?>>
                                                <?php echo $animal['name']; ?> (<?php echo $animal['species']; ?>)
                                                <?php if ($animal['client_name'] ?? false): ?> - <?php echo $animal['client_name']; ?><?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['animal_id'])): ?>
                                        <div class="text-danger small"><?php echo $errors['animal_id']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vaccine_name" class="form-label">Vaccine Name *</label>
                                    <input type="text" class="form-control" id="vaccine_name" name="vaccine_name" 
                                           value="<?php echo $old['vaccine_name'] ?? ''; ?>" 
                                           placeholder="e.g., Rabies Vaccine, DHPP, etc." required>
                                    <?php if (isset($errors['vaccine_name'])): ?>
                                        <div class="text-danger small"><?php echo $errors['vaccine_name']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vaccine_type" class="form-label">Vaccine Type</label>
                                    <select class="form-control" id="vaccine_type" name="vaccine_type">
                                        <option value="">Select Type</option>
                                        <option value="Core" <?php echo ($old['vaccine_type'] ?? '') == 'Core' ? 'selected' : ''; ?>>Core Vaccine</option>
                                        <option value="Non-Core" <?php echo ($old['vaccine_type'] ?? '') == 'Non-Core' ? 'selected' : ''; ?>>Non-Core Vaccine</option>
                                        <option value="Rabies" <?php echo ($old['vaccine_type'] ?? '') == 'Rabies' ? 'selected' : ''; ?>>Rabies</option>
                                        <option value="Combination" <?php echo ($old['vaccine_type'] ?? '') == 'Combination' ? 'selected' : ''; ?>>Combination</option>
                                        <option value="Other" <?php echo ($old['vaccine_type'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vaccine_date" class="form-label">Scheduled Date *</label>
                                    <input type="date" class="form-control" id="vaccine_date" name="vaccine_date" 
                                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" 
                                           value="<?php echo $old['vaccine_date'] ?? ''; ?>" required>
                                    <small class="text-muted">Select a future date for scheduling</small>
                                    <?php if (isset($errors['vaccine_date'])): ?>
                                        <div class="text-danger small"><?php echo $errors['vaccine_date']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="next_due_date" class="form-label">Next Due Date *</label>
                                    <input type="date" class="form-control" id="next_due_date" name="next_due_date" 
                                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" 
                                           value="<?php echo $old['next_due_date'] ?? ''; ?>" required>
                                    <small class="text-muted">When is the next vaccination due?</small>
                                    <?php if (isset($errors['next_due_date'])): ?>
                                        <div class="text-danger small"><?php echo $errors['next_due_date']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-control bg-light">
                                        <span class="badge bg-warning">Scheduled</span>
                                        <small class="text-muted ms-2">Vaccination will be scheduled for future date</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Schedule Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Any notes about the scheduled vaccination..."><?php echo $old['notes'] ?? ''; ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo url('/vaccinations'); ?>" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-calendar-plus me-2"></i>Schedule Vaccination
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Schedule Guidelines -->
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Scheduling Guidelines
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb me-2"></i>Scheduling Best Practices</h6>
                        <ul class="mb-0 ps-3">
                            <li>Schedule vaccinations at least 24 hours in advance</li>
                            <li>Consider animal's health status before scheduling</li>
                            <li>Set appropriate follow-up dates</li>
                            <li>Notify clients of scheduled appointments</li>
                            <li>Review vaccine protocols for correct timing</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Required Fields</h6>
                        <ul class="mb-0 ps-3">
                            <li>Animal selection</li>
                            <li>Vaccine name</li>
                            <li>Scheduled date</li>
                            <li>Next due date</li>
                        </ul>
                    </div>

                    <div class="alert alert-success">
                        <h6><i class="fas fa-clock me-2"></i>Common Vaccination Intervals</h6>
                        <ul class="mb-0 ps-3">
                            <li><strong>Initial Series:</strong> 3-4 week intervals</li>
                            <li><strong>Booster Shots:</strong> Annually</li>
                            <li><strong>Rabies:</strong> 1-3 years based on vaccine type</li>
                            <li><strong>Core Vaccines:</strong> Annual boosters</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default next due date (1 year from today)
    const vaccineDateInput = document.getElementById('vaccine_date');
    const nextDueDateInput = document.getElementById('next_due_date');
    
    vaccineDateInput.addEventListener('change', function() {
        if (this.value && !nextDueDateInput.value) {
            const vaccineDate = new Date(this.value);
            const nextDueDate = new Date(vaccineDate);
            nextDueDate.setFullYear(nextDueDate.getFullYear() + 1);
            nextDueDateInput.value = nextDueDate.toISOString().split('T')[0];
        }
    });

    // Set minimum date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    vaccineDateInput.min = tomorrow.toISOString().split('T')[0];
});
</script>