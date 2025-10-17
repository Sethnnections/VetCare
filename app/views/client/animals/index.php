<?php
// app/views/veterinary/animals/index.php
$animals = $animals ?? [];
$stats = $stats ?? [];
$search = $search ?? '';
$current_page = 'veterinary_animals';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-md me-2"></i>My Assigned Animals
                    </h4>
                    <div class="text-muted">
                        <small>Animals assigned to you for care</small>
                    </div>
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

                    <!-- Search Box -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form method="GET" class="row g-3">
                                <div class="col-md-8">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search my assigned animals..." value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">Search</button>
                                    <?php if ($search): ?>
                                        <a href="<?php echo url('/veterinary/animals'); ?>" class="btn btn-secondary w-100 mt-2">Clear</a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="stat-card bg-primary text-white">
                                <div class="stat-card-body">
                                    <h3><?php echo $stats['total'] ?? 0; ?></h3>
                                    <p>Assigned Animals</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card bg-info text-white">
                                <div class="stat-card-body">
                                    <h3>
                                        <?php
                                        $animalsWithTreatments = 0;
                                        foreach ($animals as $animal) {
                                            $lastTreatment = $this->animalModel->getLastTreatment($animal['animal_id']);
                                            if ($lastTreatment) $animalsWithTreatments++;
                                        }
                                        echo $animalsWithTreatments;
                                        ?>
                                    </h3>
                                    <p>With Treatments</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card bg-warning text-white">
                                <div class="stat-card-body">
                                    <h3>
                                        <?php
                                        $animalsNeedingVaccines = 0;
                                        foreach ($animals as $animal) {
                                            $nextVaccine = $this->animalModel->getNextVaccination($animal['animal_id']);
                                            if (!$nextVaccine) $animalsNeedingVaccines++;
                                        }
                                        echo $animalsNeedingVaccines;
                                        ?>
                                    </h3>
                                    <p>Need Vaccines</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (empty($animals)): ?>
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-user-md fa-4x text-muted mb-3"></i>
                                <h4>No Animals Assigned</h4>
                                <p class="text-muted">
                                    <?php echo $search ? 
                                        'No assigned animals match your search.' : 
                                        'You don\'t have any animals assigned to you yet.'; ?>
                                </p>
                                <?php if (!$search): ?>
                                    <p class="text-muted small">Contact administrator to get animals assigned to you.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Animals Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="animalsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Animal</th>
                                        <th>Species/Breed</th>
                                        <th>Owner</th>
                                        <th>Contact</th>
                                        <th>Last Treatment</th>
                                        <th>Health Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($animals as $animal): ?>
                                    <tr>
                                        <td>
                                            <strong>
                                                <a href="<?php echo url('/veterinary/animals/' . $animal['animal_id']); ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($animal['name']); ?>
                                                </a>
                                            </strong>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars(ucfirst($animal['gender'])); ?> • 
                                                <?php
                                                if (!empty($animal['birth_date'])) {
                                                    $birthDate = new DateTime($animal['birth_date']);
                                                    $today = new DateTime();
                                                    $age = $today->diff($birthDate);
                                                    echo $age->y > 0 ? $age->y . 'y' : $age->m . 'm';
                                                } else {
                                                    echo 'Age unknown';
                                                }
                                                ?>
                                            </small>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars(ucfirst($animal['species'])); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo !empty($animal['breed']) ? htmlspecialchars($animal['breed']) : 'Mixed breed'; ?></small>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($animal['client_first_name'] . ' ' . $animal['client_last_name']); ?>
                                        </td>
                                        <td>
                                            <small><?php echo !empty($animal['client_phone']) ? htmlspecialchars($animal['client_phone']) : 'No phone'; ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $lastTreatment = $this->animalModel->getLastTreatment($animal['animal_id']);
                                            if ($lastTreatment) {
                                                echo '<span class="text-success">';
                                                echo date('M j, Y', strtotime($lastTreatment['treatment_date']));
                                                echo '</span>';
                                                echo '<br><small class="text-muted">' . htmlspecialchars($lastTreatment['treatment_type']) . '</small>';
                                            } else {
                                                echo '<span class="text-warning">No treatments</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $nextVaccine = $this->animalModel->getNextVaccination($animal['animal_id']);
                                            if ($nextVaccine) {
                                                echo '<span class="badge bg-success">Up to date</span>';
                                            } else {
                                                echo '<span class="badge bg-warning">Vaccine needed</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo url('/veterinary/animals/' . $animal['animal_id']); ?>" 
                                                   class="btn btn-info" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo url('/veterinary/animals/' . $animal['animal_id'] . '/edit'); ?>" 
                                                   class="btn btn-warning" 
                                                   title="Edit Animal">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?php echo url('/treatments/create?animal_id=' . $animal['animal_id']); ?>" 
                                                   class="btn btn-success" 
                                                   title="Add Treatment">
                                                    <i class="fas fa-stethoscope"></i>
                                                </a>
                                                <a href="<?php echo url('/vaccines/create?animal_id=' . $animal['animal_id']); ?>" 
                                                   class="btn btn-primary" 
                                                   title="Add Vaccine">
                                                    <i class="fas fa-syringe"></i>
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
</div>

<style>
.stat-card {
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.stat-card h3 {
    font-size: 2rem;
    margin-bottom: 5px;
    font-weight: bold;
}

.stat-card p {
    margin-bottom: 0;
    opacity: 0.9;
    font-size: 0.9rem;
}

.empty-state {
    padding: 40px 20px;
}

.empty-state i {
    opacity: 0.5;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable if animals exist
    <?php if (!empty($animals)): ?>
        if (typeof $.fn.DataTable !== 'undefined') {
            $('#animalsTable').DataTable({
                pageLength: 25,
                responsive: true,
                order: [[0, 'asc']],
                language: {
                    search: "Search assigned animals:",
                    lengthMenu: "Show _MENU_ animals per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ assigned animals",
                    infoEmpty: "No assigned animals to show",
                    infoFiltered: "(filtered from _MAX_ total assigned animals)"
                }
            });
        }
    <?php endif; ?>
});
</script>