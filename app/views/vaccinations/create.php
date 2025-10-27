<?php
$current_page = 'vaccinations_create';
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
        <div class="col-lg-8">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-syringe me-2"></i>Vaccination Information
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

                    <form method="POST" action="<?php echo url('/vaccinations/store'); ?>">
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
                                    <label for="batch_number" class="form-label">Batch Number</label>
                                    <input type="text" class="form-control" id="batch_number" name="batch_number" 
                                           value="<?php echo $old['batch_number'] ?? ''; ?>" 
                                           placeholder="Vaccine batch number">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="manufacturer" class="form-label">Manufacturer</label>
                                    <input type="text" class="form-control" id="manufacturer" name="manufacturer" 
                                           value="<?php echo $old['manufacturer'] ?? ''; ?>" 
                                           placeholder="Vaccine manufacturer">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vaccine_date" class="form-label">Vaccination Date *</label>
                                    <input type="date" class="form-control" id="vaccine_date" name="vaccine_date" 
                                           max="<?php echo date('Y-m-d'); ?>" 
                                           value="<?php echo $old['vaccine_date'] ?? date('Y-m-d'); ?>" required>
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
                                        <span class="badge bg-success">Completed</span>
                                        <small class="text-muted ms-2">Vaccination will be recorded as completed</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Any additional notes about the vaccination..."><?php echo $old['notes'] ?? ''; ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo url('/vaccinations'); ?>" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Record Vaccination
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Form Guidelines -->
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Vaccination Guidelines
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb me-2"></i>Vaccination Best Practices</h6>
                        <ul class="mb-0 ps-3">
                            <li>Record all required vaccine information</li>
                            <li>Verify batch numbers for traceability</li>
                            <li>Set appropriate next due dates</li>
                            <li>Include any adverse reactions in notes</li>
                            <li>Ensure proper storage conditions</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Required Fields</h6>
                        <ul class="mb-0 ps-3">
                            <li>Animal selection</li>
                            <li>Vaccine name</li>
                            <li>Vaccination date</li>
                            <li>Next due date</li>
                        </ul>
                    </div>

                    <div class="alert alert-success">
                        <h6><i class="fas fa-clock me-2"></i>Common Vaccination Schedules</h6>
                        <ul class="mb-0 ps-3">
                            <li><strong>Rabies:</strong> 1 year then every 3 years</li>
                            <li><strong>DHPP:</strong> Annually for dogs</li>
                            <li><strong>FVRCP:</strong> Annually for cats</li>
                            <li><strong>Leptospirosis:</strong> Annually</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Animal Information Preview -->
            <div class="card dashboard-card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-paw me-2"></i>Animal Information
                    </h5>
                </div>
                <div class="card-body">
                    <div id="animalInfo" class="text-center text-muted">
                        <i class="fas fa-paw fa-2x mb-2"></i>
                        <p>Select an animal to view details</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const animalSelect = document.getElementById('animal_id');
    const animalInfo = document.getElementById('animalInfo');
    
    // Load animal information when selected
    animalSelect.addEventListener('change', function() {
        const animalId = this.value;
        
        if (!animalId) {
            animalInfo.innerHTML = `
                <i class="fas fa-paw fa-2x mb-2"></i>
                <p>Select an animal to view details</p>
            `;
            return;
        }
        
        animalInfo.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading animal information...</p>
        `;
        
        fetch(`<?php echo url('/api/animals/'); ?>${animalId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.animal) {
                    const animal = data.animal;
                    animalInfo.innerHTML = `
                        <div class="text-center">
                            <div class="avatar-lg bg-primary rounded-circle mx-auto d-flex align-items-center justify-content-center mb-2">
                                <i class="fas fa-paw text-white fa-2x"></i>
                            </div>
                            <h6>${animal.animal_name}</h6>
                            <p class="text-muted">${animal.species} • ${animal.breed || 'Unknown breed'}</p>
                        </div>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th>Age:</th>
                                <td>${animal.age_years || 0} years, ${animal.age_months || 0} months</td>
                            </tr>
                            <tr>
                                <th>Gender:</th>
                                <td>${animal.gender || 'Unknown'}</td>
                            </tr>
                            <tr>
                                <th>Color:</th>
                                <td>${animal.color || 'Unknown'}</td>
                            </tr>
                            <tr>
                                <th>Weight:</th>
                                <td>${animal.weight ? animal.weight + ' kg' : 'Unknown'}</td>
                            </tr>
                            <tr>
                                <th>Owner:</th>
                                <td>${animal.client_full_name || 'Unknown'}</td>
                            </tr>
                        </table>
                    `;
                } else {
                    animalInfo.innerHTML = `
                        <i class="fas fa-exclamation-triangle text-warning fa-2x mb-2"></i>
                        <p>Unable to load animal information</p>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                animalInfo.innerHTML = `
                    <i class="fas fa-exclamation-triangle text-danger fa-2x mb-2"></i>
                    <p>Error loading animal information</p>
                `;
            });
    });
    
    // Trigger change event if animal is pre-selected (form validation failed)
    if (animalSelect.value) {
        animalSelect.dispatchEvent(new Event('change'));
    }

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
});
</script>