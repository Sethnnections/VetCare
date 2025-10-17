<?php
$animal = $data['animal'] ?? [];
$errors = $data['errors'] ?? [];
?>

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Edit Animal: <?php echo htmlspecialchars($animal['name']); ?></h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('/dashboard'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo url('/veterinary/animals'); ?>">My Animals</a></li>
                <li class="breadcrumb-item"><a href="<?php echo url('/veterinary/animals/' . $animal['animal_id']); ?>"><?php echo htmlspecialchars($animal['name']); ?></a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Update Animal Information</h5>
                <p class="card-text">As a veterinary, you can update weight and medical notes.</p>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo url('/veterinary/animals/' . $animal['animal_id'] . '/update'); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($animal['name']); ?>" readonly>
                                <small class="text-muted">Name cannot be changed by veterinary</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Species</label>
                                <input type="text" class="form-control" value="<?php echo ucfirst($animal['species']); ?>" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Breed</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($animal['breed'] ?? ''); ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Weight (kg) *</label>
                                <input type="number" step="0.01" class="form-control <?php echo isset($errors['weight']) ? 'is-invalid' : ''; ?>" 
                                       name="weight" value="<?php echo htmlspecialchars($animal['weight'] ?? ''); ?>" 
                                       placeholder="Enter weight in kilograms" required>
                                <?php if (isset($errors['weight'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['weight']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Medical Notes</label>
                        <textarea class="form-control <?php echo isset($errors['notes']) ? 'is-invalid' : ''; ?>" 
                                  name="notes" rows="4" placeholder="Add medical notes, observations, or recommendations..."><?php echo htmlspecialchars($animal['notes'] ?? ''); ?></textarea>
                        <?php if (isset($errors['notes'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['notes']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="<?php echo url('/veterinary/animals/' . $animal['animal_id']); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Details
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Animal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Animal Summary</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <i class="fas fa-paw fa-3x text-primary"></i>
                    <h5 class="mt-2"><?php echo htmlspecialchars($animal['name']); ?></h5>
                </div>
                
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Species:</span>
                        <strong><?php echo ucfirst($animal['species']); ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Breed:</span>
                        <strong><?php echo htmlspecialchars($animal['breed'] ?? 'Mixed'); ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Gender:</span>
                        <strong><?php echo ucfirst($animal['gender'] ?? 'Unknown'); ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Age:</span>
                        <strong><?php echo calculateAge($animal['birth_date']) ?: 'Unknown'; ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Status:</span>
                        <span class="badge bg-<?php echo $animal['status'] == 'active' ? 'success' : 'secondary'; ?>">
                            <?php echo ucfirst($animal['status']); ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?php echo url('/treatments/create?animal_id=' . $animal['animal_id']); ?>" class="btn btn-success">
                        <i class="fas fa-stethoscope"></i> Add Treatment
                    </a>
                    <a href="<?php echo url('/vaccines/create?animal_id=' . $animal['animal_id']); ?>" class="btn btn-info">
                        <i class="fas fa-syringe"></i> Record Vaccination
                    </a>
                    <a href="<?php echo url('/veterinary/animals/' . $animal['animal_id']); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>