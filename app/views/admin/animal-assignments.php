<?php
$unassignedAnimals = $unassignedAnimals ?? [];
$veterinarians = $veterinarians ?? [];
$currentAssignments = $currentAssignments ?? [];
$current_page = 'admin_assignments';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-md me-2"></i>Animal Assignment Management
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

                    <!-- Assignment Form -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Assign Animal to Veterinary</h5>
                                </div>
                                <div class="card-body">
                                    <form action="<?php echo url('/admin/animal-assignments/assign'); ?>" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                        
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="animal_id" class="form-label">Select Animal</label>
                                                    <select class="form-control" id="animal_id" name="animal_id" required>
                                                        <option value="">Choose Animal...</option>
                                                        <?php foreach ($unassignedAnimals as $animal): ?>
                                                        <option value="<?php echo $animal['animal_id']; ?>">
                                                            <?php echo htmlspecialchars($animal['name']); ?> 
                                                            (<?php echo htmlspecialchars($animal['species']); ?>)
                                                            - <?php echo htmlspecialchars($animal['client_first_name'] . ' ' . $animal['client_last_name']); ?>
                                                        </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="veterinary_id" class="form-label">Select Veterinary</label>
                                                    <select class="form-control" id="veterinary_id" name="veterinary_id" required>
                                                        <option value="">Choose Veterinary...</option>
                                                        <?php foreach ($veterinarians as $vet): ?>
                                                        <option value="<?php echo $vet['user_id']; ?>">
                                                            <?php echo htmlspecialchars($vet['first_name'] . ' ' . $vet['last_name']); ?>
                                                            (<?php echo htmlspecialchars($vet['email']); ?>)
                                                        </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="form-label">&nbsp;</label>
                                                    <button type="submit" class="btn btn-primary w-100">Assign</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Assignments -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Current Assignments</h5>
                                    <span class="badge bg-primary"><?php echo count($currentAssignments); ?></span>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($currentAssignments)): ?>
                                        <p class="text-muted">No current assignments</p>
                                    <?php else: ?>
                                        <div class="list-group">
                                            <?php foreach ($currentAssignments as $assignment): ?>
                                            <div class="list-group-item">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($assignment['name']); ?></h6>
                                                    <form action="<?php echo url('/admin/animal-assignments/unassign/' . $assignment['animal_id']); ?>" method="POST" class="d-inline">
                                                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Unassign this animal?')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                <p class="mb-1">
                                                    <small class="text-muted">
                                                        Species: <?php echo htmlspecialchars($assignment['species']); ?> | 
                                                        Owner: <?php echo htmlspecialchars($assignment['client_first_name'] . ' ' . $assignment['client_last_name']); ?>
                                                    </small>
                                                </p>
                                                <small class="text-primary">
                                                    Assigned to: <?php echo htmlspecialchars($assignment['vet_first_name'] . ' ' . $assignment['vet_last_name']); ?>
                                                </small>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Unassigned Animals</h5>
                                    <span class="badge bg-warning"><?php echo count($unassignedAnimals); ?></span>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($unassignedAnimals)): ?>
                                        <p class="text-muted">All animals are assigned</p>
                                    <?php else: ?>
                                        <div class="list-group">
                                            <?php foreach ($unassignedAnimals as $animal): ?>
                                            <div class="list-group-item">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($animal['name']); ?></h6>
                                                <p class="mb-1">
                                                    <small class="text-muted">
                                                        Species: <?php echo htmlspecialchars($animal['species']); ?> | 
                                                        Owner: <?php echo htmlspecialchars($animal['client_first_name'] . ' ' . $animal['client_last_name']); ?>
                                                    </small>
                                                </p>
                                                <small class="text-warning">Not assigned to any veterinary</small>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>