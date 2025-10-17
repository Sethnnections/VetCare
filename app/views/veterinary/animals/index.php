<?php
$animals = $data['animals'] ?? [];
$stats = $data['stats'] ?? [];
$search = $data['search'] ?? '';
$species = $data['species'] ?? '';
?>



<div class="row mb-4 mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3><?php echo $stats['total'] ?? 0; ?></h3>
                        <p>Total Animals</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-paw fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3><?php echo $stats['active'] ?? 0; ?></h3>
                        <p>Active</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3><?php echo ($stats['total'] ?? 0) - ($stats['active'] ?? 0); ?></h3>
                        <p>Inactive</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-pause-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3><?php echo count(array_filter($animals, function($animal) {
                            return !empty($animal['last_treatment_date']);
                        })); ?></h3>
                        <p>Recently Treated</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-stethoscope fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">My Assigned Animals</h4>
                <p class="card-text">Animals assigned to you for veterinary care</p>
            </div>
            <div class="card-body">
                <?php if (empty($animals)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-paw fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Animals Assigned</h4>
                        <p class="text-muted">You don't have any animals assigned to you yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Microchip</th>
                                    <th>Species</th>
                                    <th>Breed</th>
                                    <th>Owner</th>
                                    <th>Gender</th>
                                    <th>Age</th>
                                    <th>Status</th>
                                    <th>Last Treatment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($animals as $animal): ?>
                                    <?php
                                    $age = calculateAge($animal['birth_date']);
                                    $lastTreatment = $this->animalModel->getLastTreatment($animal['animal_id']);
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($animal['name']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($animal['microchip'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo ucfirst($animal['species']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($animal['breed'] ?? 'Mixed'); ?></td>
                                        <td>
                                            <?php 
                                            $ownerName = '';
                                            if (!empty($animal['client_first_name']) || !empty($animal['client_last_name'])) {
                                                $ownerName = trim($animal['client_first_name'] . ' ' . $animal['client_last_name']);
                                            } else {
                                                $ownerName = 'Client #' . $animal['client_id'];
                                            }
                                            echo htmlspecialchars($ownerName);
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $animal['gender'] == 'male' ? 'primary' : ($animal['gender'] == 'female' ? 'danger' : 'secondary'); ?>">
                                                <?php echo ucfirst($animal['gender'] ?? 'Unknown'); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $age ?: 'Unknown'; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $animal['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($animal['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($lastTreatment): ?>
                                                <small class="text-muted">
                                                    <?php echo formatDate($lastTreatment['treatment_date']); ?><br>
                                                    <span class="text-info"><?php echo htmlspecialchars(substr($lastTreatment['diagnosis'], 0, 30)) . '...'; ?></span>
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted">No treatments</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo url('/veterinary/animals/' . $animal['animal_id']); ?>" 
                                                   class="btn btn-sm btn-info" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo url('/veterinary/animals/' . $animal['animal_id'] . '/edit'); ?>" 
                                                   class="btn btn-sm btn-warning" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?php echo url('/treatments/create?animal_id=' . $animal['animal_id']); ?>" 
                                                   class="btn btn-sm btn-success" 
                                                   title="Add Treatment">
                                                    <i class="fas fa-stethoscope"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.btn-group .btn {
    margin-right: 2px;
}

.badge {
    font-size: 0.75em;
}
</style>