<?php
$animal = $animal ?? [];
$clients = $clients ?? [];
$errors = $errors ?? [];
$old = $old ?? [];
$current_page = 'animals_edit';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="dashboard-card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Animal: <?php echo htmlspecialchars($animal['animal_name'] ?? ''); ?>
                    </h4>
                    <a href="<?php echo url('/animals/' . $animal['animal_id']); ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back to Animal
                    </a>
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

                    <form action="<?php echo url('/animals/' . $animal['animal_id']); ?>" method="POST" id="animalForm">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Animal Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                           id="name" 
                                           name="name" 
                                           value="<?php echo htmlspecialchars($old['name'] ?? $animal['animal_name'] ?? ''); ?>" 
                                           required 
                                           placeholder="Enter animal name">
                                    <?php if (isset($errors['name'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="species" class="form-label">Species <span class="text-danger">*</span></label>
                                    <select class="form-control <?php echo isset($errors['species']) ? 'is-invalid' : ''; ?>" 
                                            id="species" 
                                            name="species" 
                                            required>
                                        <option value="">Select Species</option>
                                        <option value="dog" <?php echo ($old['species'] ?? $animal['species'] ?? '') == 'dog' ? 'selected' : ''; ?>>Dog</option>
                                        <option value="cat" <?php echo ($old['species'] ?? $animal['species'] ?? '') == 'cat' ? 'selected' : ''; ?>>Cat</option>
                                        <option value="bird" <?php echo ($old['species'] ?? $animal['species'] ?? '') == 'bird' ? 'selected' : ''; ?>>Bird</option>
                                        <option value="rabbit" <?php echo ($old['species'] ?? $animal['species'] ?? '') == 'rabbit' ? 'selected' : ''; ?>>Rabbit</option>
                                        <option value="horse" <?php echo ($old['species'] ?? $animal['species'] ?? '') == 'horse' ? 'selected' : ''; ?>>Horse</option>
                                        <option value="other" <?php echo ($old['species'] ?? $animal['species'] ?? '') == 'other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                    <?php if (isset($errors['species'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['species']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="breed" class="form-label">Breed</label>
                                    <input type="text" 
                                           class="form-control <?php echo isset($errors['breed']) ? 'is-invalid' : ''; ?>" 
                                           id="breed" 
                                           name="breed" 
                                           value="<?php echo htmlspecialchars($old['breed'] ?? $animal['breed'] ?? ''); ?>" 
                                           placeholder="Enter breed">
                                    <?php if (isset($errors['breed'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['breed']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select class="form-control <?php echo isset($errors['gender']) ? 'is-invalid' : ''; ?>" 
                                            id="gender" 
                                            name="gender" 
                                            required>
                                        <option value="">Select Gender</option>
                                        <option value="male" <?php echo ($old['gender'] ?? $animal['gender'] ?? '') == 'male' ? 'selected' : ''; ?>>Male</option>
                                        <option value="female" <?php echo ($old['gender'] ?? $animal['gender'] ?? '') == 'female' ? 'selected' : ''; ?>>Female</option>
                                        <option value="unknown" <?php echo ($old['gender'] ?? $animal['gender'] ?? '') == 'unknown' ? 'selected' : ''; ?>>Unknown</option>
                                    </select>
                                    <?php if (isset($errors['gender'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['gender']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="birth_date" class="form-label">Birth Date</label>
                                    <input type="date" 
                                           class="form-control <?php echo isset($errors['birth_date']) ? 'is-invalid' : ''; ?>" 
                                           id="birth_date" 
                                           name="birth_date" 
                                           value="<?php echo htmlspecialchars($old['birth_date'] ?? $animal['birth_date'] ?? ''); ?>">
                                    <?php if (isset($errors['birth_date'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['birth_date']; ?></div>
                                    <?php endif; ?>
                                    <small class="form-text text-muted">Approximate date if unknown</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="weight" class="form-label">Weight (kg)</label>
                                    <input type="number" 
                                           step="0.1" 
                                           class="form-control <?php echo isset($errors['weight']) ? 'is-invalid' : ''; ?>" 
                                           id="weight" 
                                           name="weight" 
                                           value="<?php echo htmlspecialchars($old['weight'] ?? $animal['weight'] ?? ''); ?>" 
                                           placeholder="e.g., 25.5">
                                    <?php if (isset($errors['weight'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['weight']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="color" class="form-label">Color/Markings</label>
                                    <input type="text" 
                                           class="form-control <?php echo isset($errors['color']) ? 'is-invalid' : ''; ?>" 
                                           id="color" 
                                           name="color" 
                                           value="<?php echo htmlspecialchars($old['color'] ?? $animal['color'] ?? ''); ?>" 
                                           placeholder="e.g., Black and White">
                                    <?php if (isset($errors['color'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['color']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="microchip" class="form-label">Microchip Number</label>
                                    <input type="text" 
                                           class="form-control <?php echo isset($errors['microchip']) ? 'is-invalid' : ''; ?>" 
                                           id="microchip" 
                                           name="microchip" 
                                           value="<?php echo htmlspecialchars($old['microchip'] ?? $animal['microchip'] ?? ''); ?>" 
                                           placeholder="15-digit microchip number">
                                    <?php if (isset($errors['microchip'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['microchip']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Client selection (admin only) -->
                        <?php if (getCurrentUserRole() === ROLE_ADMIN): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="client_id" class="form-label">Owner <span class="text-danger">*</span></label>
                                    <select class="form-control <?php echo isset($errors['client_id']) ? 'is-invalid' : ''; ?>" 
                                            id="client_id" 
                                            name="client_id" 
                                            required>
                                        <option value="">Select Owner</option>
                                        <?php foreach ($clients as $client): ?>
                                        <option value="<?php echo $client['client_id']; ?>" 
                                                <?php echo ($old['client_id'] ?? $animal['client_id'] ?? '') == $client['client_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($client['first_name'] . ' ' . $client['last_name'] . ' (' . $client['email'] . ')'); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['client_id'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['client_id']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control <?php echo isset($errors['status']) ? 'is-invalid' : ''; ?>" 
                                            id="status" 
                                            name="status">
                                        <option value="active" <?php echo ($old['status'] ?? $animal['animal_status'] ?? '') == 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo ($old['status'] ?? $animal['animal_status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                    <?php if (isset($errors['status'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['status']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="form-group mb-4">
                            <label for="notes" class="form-label">Special Notes/Medical History</label>
                            <textarea class="form-control <?php echo isset($errors['notes']) ? 'is-invalid' : ''; ?>" 
                                      id="notes" 
                                      name="notes" 
                                      rows="4" 
                                      placeholder="Any special care instructions, allergies, behavioral notes, or medical history..."><?php echo htmlspecialchars($old['notes'] ?? $animal['animal_notes'] ?? $animal['notes'] ?? ''); ?></textarea>
                            <?php if (isset($errors['notes'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['notes']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo url('/animals/' . $animal['animal_id']); ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Update Animal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('animalForm');
    
    // Client-side validation
    form.addEventListener('submit', function(e) {
        let valid = true;
        
        // Clear previous errors
        const errorElements = form.querySelectorAll('.is-invalid');
        errorElements.forEach(el => el.classList.remove('is-invalid'));
        
        // Validate required fields
        const requiredFields = ['name', 'species', 'gender'];
        requiredFields.forEach(field => {
            const input = document.getElementById(field);
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                valid = false;
            }
        });
        
        // Validate birth date (if provided)
        const birthDate = document.getElementById('birth_date').value;
        if (birthDate) {
            const selectedDate = new Date(birthDate);
            const today = new Date();
            if (selectedDate > today) {
                document.getElementById('birth_date').classList.add('is-invalid');
                valid = false;
                alert('Birth date cannot be in the future');
            }
        }
        
        // Validate weight (if provided)
        const weight = document.getElementById('weight').value;
        if (weight && (isNaN(weight) || weight <= 0)) {
            document.getElementById('weight').classList.add('is-invalid');
            valid = false;
            alert('Weight must be a positive number');
        }
        
        if (!valid) {
            e.preventDefault();
            alert('Please fix the errors in the form before submitting.');
        }
    });
});
</script>