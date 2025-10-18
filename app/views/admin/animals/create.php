<?php
$clients = $clients ?? [];
$old = $old ?? [];
$errors = $errors ?? [];
$current_page = 'animals_create';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Add New Animal
                    </h4>
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

                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <form action="<?php echo url('/animals/store'); ?>" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="name" class="form-label">Animal Name *</label>
                                                    <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                                           id="name" name="name" value="<?php echo htmlspecialchars($old['name'] ?? ''); ?>" required>
                                                    <?php if (isset($errors['name'])): ?>
                                                        <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="client_id" class="form-label">Owner *</label>
                                                    <select class="form-control <?php echo isset($errors['client_id']) ? 'is-invalid' : ''; ?>" 
                                                            id="client_id" name="client_id" required>
                                                        <option value="">Select Owner...</option>
                                                        <?php foreach ($clients as $client): ?>
                                                        <option value="<?php echo $client['client_id']; ?>" 
                                                            <?php echo ($old['client_id'] ?? '') == $client['client_id'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($client['first_name'] . ' ' . $client['last_name']); ?>
                                                            (<?php echo htmlspecialchars($client['email']); ?>)
                                                        </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <?php if (isset($errors['client_id'])): ?>
                                                        <div class="invalid-feedback"><?php echo $errors['client_id']; ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="species" class="form-label">Species *</label>
                                                    <select class="form-control <?php echo isset($errors['species']) ? 'is-invalid' : ''; ?>" 
                                                            id="species" name="species" required>
                                                        <option value="">Select Species...</option>
                                                        <option value="dog" <?php echo ($old['species'] ?? '') == 'dog' ? 'selected' : ''; ?>>Dog</option>
                                                        <option value="cat" <?php echo ($old['species'] ?? '') == 'cat' ? 'selected' : ''; ?>>Cat</option>
                                                        <option value="bird" <?php echo ($old['species'] ?? '') == 'bird' ? 'selected' : ''; ?>>Bird</option>
                                                        <option value="rabbit" <?php echo ($old['species'] ?? '') == 'rabbit' ? 'selected' : ''; ?>>Rabbit</option>
                                                        <option value="hamster" <?php echo ($old['species'] ?? '') == 'hamster' ? 'selected' : ''; ?>>Hamster</option>
                                                        <option value="other" <?php echo ($old['species'] ?? '') == 'other' ? 'selected' : ''; ?>>Other</option>
                                                    </select>
                                                    <?php if (isset($errors['species'])): ?>
                                                        <div class="invalid-feedback"><?php echo $errors['species']; ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="breed" class="form-label">Breed</label>
                                                    <input type="text" class="form-control" id="breed" name="breed" 
                                                           value="<?php echo htmlspecialchars($old['breed'] ?? ''); ?>" 
                                                           placeholder="e.g., Labrador, Siamese, etc.">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="gender" class="form-label">Gender</label>
                                                    <select class="form-control" id="gender" name="gender">
                                                        <option value="unknown">Unknown</option>
                                                        <option value="male" <?php echo ($old['gender'] ?? '') == 'male' ? 'selected' : ''; ?>>Male</option>
                                                        <option value="female" <?php echo ($old['gender'] ?? '') == 'female' ? 'selected' : ''; ?>>Female</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="birth_date" class="form-label">Birth Date</label>
                                                    <input type="date" class="form-control" id="birth_date" name="birth_date" 
                                                           value="<?php echo htmlspecialchars($old['birth_date'] ?? ''); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="weight" class="form-label">Weight (kg)</label>
                                                    <input type="number" step="0.1" class="form-control" id="weight" name="weight" 
                                                           value="<?php echo htmlspecialchars($old['weight'] ?? ''); ?>" 
                                                           placeholder="e.g., 5.5">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="color" class="form-label">Color/Markings</label>
                                                    <input type="text" class="form-control" id="color" name="color" 
                                                           value="<?php echo htmlspecialchars($old['color'] ?? ''); ?>" 
                                                           placeholder="e.g., Black, Brown/White, etc.">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="microchip" class="form-label">Microchip Number</label>
                                                    <input type="text" class="form-control" id="microchip" name="microchip" 
                                                           value="<?php echo htmlspecialchars($old['microchip'] ?? ''); ?>" 
                                                           placeholder="Optional">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-4">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                                      placeholder="Any special notes about the animal..."><?php echo htmlspecialchars($old['notes'] ?? ''); ?></textarea>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <a href="<?php echo url('/animals'); ?>" class="btn btn-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary">Add Animal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add any client-side validation here if needed
});
</script>