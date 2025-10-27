<?php
$current_page = 'treatments_create';
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
        <div class="col-lg-8">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Treatment Information
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

                    <form id="treatmentForm" method="POST" action="<?php echo url('/treatments/store'); ?>">
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
                                                <?php if ($animal['client_name']): ?> - <?php echo $animal['client_name']; ?><?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['animal_id'])): ?>
                                        <div class="text-danger small"><?php echo $errors['animal_id']; ?></div>
                                    <?php endif; ?>
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
                                                <?php echo (isset($old['veterinary_id']) && $old['veterinary_id'] == $vet['user_id']) ? 'selected' : ''; ?>>
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
                                <input type="hidden" name="veterinary_id" value="<?php echo $_SESSION['user_id']; ?>">
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="treatment_date" class="form-label">Treatment Date *</label>
                                    <input type="date" class="form-control" id="treatment_date" name="treatment_date" 
                                           max="<?php echo date('Y-m-d'); ?>" 
                                           value="<?php echo $old['treatment_date'] ?? date('Y-m-d'); ?>" required>
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
                                           value="<?php echo $old['follow_up_date'] ?? ''; ?>">
                                    <?php if (isset($errors['follow_up_date'])): ?>
                                        <div class="text-danger small"><?php echo $errors['follow_up_date']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="diagnosis" class="form-label">Diagnosis *</label>
                            <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" 
                                      placeholder="Enter the diagnosis..." required><?php echo $old['diagnosis'] ?? ''; ?></textarea>
                            <?php if (isset($errors['diagnosis'])): ?>
                                <div class="text-danger small"><?php echo $errors['diagnosis']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="treatment_details" class="form-label">Treatment Details *</label>
                            <textarea class="form-control" id="treatment_details" name="treatment_details" rows="4" 
                                      placeholder="Describe the treatment provided..." required><?php echo $old['treatment_details'] ?? ''; ?></textarea>
                            <?php if (isset($errors['treatment_details'])): ?>
                                <div class="text-danger small"><?php echo $errors['treatment_details']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="medication_prescribed" class="form-label">Medication Prescribed</label>
                            <textarea class="form-control" id="medication_prescribed" name="medication_prescribed" rows="2" 
                                      placeholder="List any medications prescribed..."><?php echo $old['medication_prescribed'] ?? ''; ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cost" class="form-label">Cost (MK)</label>
                                    <input type="number" class="form-control" id="cost" name="cost" 
                                           step="0.01" min="0" 
                                           value="<?php echo $old['cost'] ?? '0.00'; ?>">
                                    <?php if (isset($errors['cost'])): ?>
                                        <div class="text-danger small"><?php echo $errors['cost']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="ongoing" <?php echo ($old['status'] ?? 'ongoing') == 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                                        <option value="completed" <?php echo ($old['status'] ?? '') == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="follow_up" <?php echo ($old['status'] ?? '') == 'follow_up' ? 'selected' : ''; ?>>Follow-up Required</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" 
                                      placeholder="Any additional notes or observations..."><?php echo $old['notes'] ?? ''; ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo url('/treatments'); ?>" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Treatment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Form Guidelines -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Form Guidelines
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb me-2"></i>Tips for Complete Treatment Records</h6>
                        <ul class="mb-0 ps-3">
                            <li>Be specific in diagnosis and treatment details</li>
                            <li>Include all medications and dosages prescribed</li>
                            <li>Schedule follow-ups when necessary</li>
                            <li>Record accurate costs for billing purposes</li>
                            <li>Add relevant notes for future reference</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Required Fields</h6>
                        <ul class="mb-0 ps-3">
                            <li>Animal selection</li>
                            <li>Veterinary assignment</li>
                            <li>Treatment date</li>
                            <li>Diagnosis</li>
                            <li>Treatment details</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Animal Information Preview -->
            <div class="dashboard-card mt-4">
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
});
</script>