<?php
$animals = $animals ?? [];
$stats = $stats ?? [];
$current_page = 'client_animals';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-paw me-2"></i>My Animals
                    </h4>
                    <a href="<?php echo url('/client/animals/add'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Add New Animal
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

                    <!-- Statistics Cards -->
                    <div class="stats-grid mb-4">
                        <div class="stat-card primary">
                            <div class="stat-icon">
                                <i class="fas fa-paw"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?php echo $stats['total'] ?? 0; ?></div>
                                <div class="stat-label">Total Animals</div>
                            </div>
                        </div>
                        <div class="stat-card success">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?php echo $stats['active'] ?? 0; ?></div>
                                <div class="stat-label">Active Animals</div>
                            </div>
                        </div>
                    </div>

                    <!-- Animals List -->
                    <?php if (empty($animals)): ?>
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-paw fa-4x text-muted mb-3"></i>
                                <h4>No Animals Registered</h4>
                                <p class="text-muted">You haven't registered any animals yet.</p>
                                <a href="<?php echo url('/client/animals/add'); ?>" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-1"></i>Add Your First Animal
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($animals as $animal): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card animal-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="card-title mb-0">
                                                <a href="<?php echo url('/client/animals/' . $animal['animal_id']); ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($animal['name']); ?>
                                                </a>
                                            </h5>
                                            <span class="badge <?php echo $animal['status'] == 'active' ? 'bg-success' : 'bg-warning'; ?>">
                                                <?php echo ucfirst($animal['status']); ?>
                                            </span>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted">Species</small>
                                                <div><?php echo htmlspecialchars(ucfirst($animal['species'])); ?></div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Breed</small>
                                                <div><?php echo !empty($animal['breed']) ? htmlspecialchars($animal['breed']) : 'Mixed'; ?></div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted">Age</small>
                                                <div>
                                                    <?php
                                                    if (!empty($animal['birth_date'])) {
                                                        echo calculateAge($animal['birth_date']);
                                                    } else {
                                                        echo 'Unknown';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Gender</small>
                                                <div><?php echo ucfirst($animal['gender'] ?? 'Unknown'); ?></div>
                                            </div>
                                        </div>

                                        <div class="btn-group w-100">
                                            <a href="<?php echo url('/client/animals/' . $animal['animal_id']); ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>View
                                            </a>
                                            <a href="<?php echo url('/client/animals/' . $animal['animal_id'] . '/edit'); ?>" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>
                                            <a href="<?php echo url('/client/animals/' . $animal['animal_id'] . '/medical-history'); ?>" class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-file-medical me-1"></i>Records
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.animal-card {
    transition: transform 0.2s;
    border-left: 4px solid var(--primary-blue);
}

.animal-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.empty-state {
    padding: 40px 20px;
}

.empty-state i {
    opacity: 0.5;
}
</style>