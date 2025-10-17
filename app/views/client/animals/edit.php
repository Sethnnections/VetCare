<?php
// app/views/client/animals/edit.php
$animal = $animal ?? [];
$errors = $errors ?? [];
$old = $old ?? $animal;
$current_page = 'client_animals';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="dashboard-card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Animal: <?php echo htmlspecialchars($animal['name'] ?? ''); ?>
                    </h4>
                    <a href="<?php echo url('/client/animals'); ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back to Animals
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

                    <form action="<?php echo url('/client/animals/' . ($animal['animal_id'] ?? '') . '/update'); ?>" method="POST" id="animalForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Animal Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                           id="name" 
                                           name="name" 
                                           value="<?php echo htmlspecialchars($old['name'] ?? ''); ?>" 
                                           required>
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
                                        <option value="dog" <?php echo ($old['species'] ?? '') == 'dog' ? 'selected' : ''; ?>>Dog</option>
                                        <option value="cat" <?php echo ($old['species'] ?? '') == 'cat' ? 'selected' : ''; ?>>Cat</option>
                                        <option value="bird" <?php echo ($old['species'] ?? '') == 'bird' ? 'selected' : ''; ?>>Bird</option>
                                        <option value="rabbit" <?php echo ($old['species'] ?? '') == 'rabbit' ? 'selected' : ''; ?>>Rabbit</option>
                                        <option value="horse" <?php echo ($old['species'] ?? '') == 'horse' ? 'selected' : ''; ?>>Horse</option>
                                        <option value="other" <?php echo ($old['species'] ?? '') == 'other' ? 'selected' : ''; ?>>Other</option>
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
                                           value="<?php echo htmlspecialchars($old['breed'] ?? ''); ?>">
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
                                        <option value="male" <?php echo ($old['gender'] ?? '') == 'male' ? 'selected' : ''; ?>>Male</option>
                                        <option value="female" <?php echo ($old['gender'] ?? '') == 'female' ? 'selected' : ''; ?>>Female</option>
                                        <option value="unknown" <?php echo ($old['gender'] ?? '') == 'unknown' ? 'selected' : ''; ?>>Unknown</option>
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
                                           value="<?php echo htmlspecialchars($old['birth_date'] ?? ''); ?>">
                                    <?php if (isset($errors['birth_date'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['birth_date']; ?></div>
                                    <?php endif; ?>
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
                                           value="<?php echo htmlspecialchars($old['weight'] ?? ''); ?>">
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
                                           value="<?php echo htmlspecialchars($old['color'] ?? ''); ?>">
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
                                           value="<?php echo htmlspecialchars($old['microchip'] ?? ''); ?>">
                                    <?php if (isset($errors['microchip'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['microchip']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="notes" class="form-label">Special Notes/Medical History</label>
                            <textarea class="form-control <?php echo isset($errors['notes']) ? 'is-invalid' : ''; ?>" 
                                      id="notes" 
                                      name="notes" 
                                      rows="4"><?php echo htmlspecialchars($old['notes'] ?? ''); ?></textarea>
                            <?php if (isset($errors['notes'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['notes']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo url('/client/animals'); ?>" class="btn btn-secondary">Cancel</a>
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
        
        if (!valid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
});
</script>