<?php
// app/views/client/animals/show.php
$animal = $animal ?? [];
$treatments = $treatments ?? [];
$vaccines = $vaccines ?? [];
$lastTreatment = $lastTreatment ?? [];
$nextVaccination = $nextVaccination ?? [];
$current_page = 'client_animals';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-paw me-2"></i><?php echo htmlspecialchars($animal['name'] ?? 'Animal Details'); ?>
                    </h4>
                    <div>
                        <a href="<?php echo url('/client/animals/' . ($animal['animal_id'] ?? '') . '/edit'); ?>" class="btn btn-warning btn-sm me-2">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="<?php echo url('/client/animals'); ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to Animals
                        </a>
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

                    <div class="row">
                        <div class="col-md-6">
                            <h6>Basic Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td><?php echo htmlspecialchars($animal['name'] ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Species:</strong></td>
                                    <td><?php echo htmlspecialchars(ucfirst($animal['species'] ?? 'N/A')); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Breed:</strong></td>
                                    <td><?php echo !empty($animal['breed']) ? htmlspecialchars($animal['breed']) : 'N/A'; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Gender:</strong></td>
                                    <td><?php echo htmlspecialchars(ucfirst($animal['gender'] ?? 'N/A')); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Age:</strong></td>
                                    <td>
                                        <?php
                                        if (!empty($animal['birth_date'])) {
                                            $birthDate = new DateTime($animal['birth_date']);
                                            $today = new DateTime();
                                            $age = $today->diff($birthDate);
                                            
                                            if ($age->y > 0) {
                                                echo $age->y . ' year' . ($age->y > 1 ? 's' : '');
                                                if ($age->m > 0) {
                                                    echo ', ' . $age->m . ' month' . ($age->m > 1 ? 's' : '');
                                                }
                                            } else {
                                                echo $age->m . ' month' . ($age->m > 1 ? 's' : '');
                                            }
                                        } else {
                                            echo 'Unknown';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Additional Details</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Weight:</strong></td>
                                    <td><?php echo !empty($animal['weight']) ? $animal['weight'] . ' kg' : 'N/A'; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Color:</strong></td>
                                    <td><?php echo !empty($animal['color']) ? htmlspecialchars($animal['color']) : 'N/A'; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Microchip:</strong></td>
                                    <td><?php echo !empty($animal['microchip']) ? htmlspecialchars($animal['microchip']) : 'Not chipped'; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-<?php echo ($animal['status'] ?? 0) == 1 ? 'success' : 'secondary'; ?>">
                                            <?php echo ($animal['status'] ?? 0) == 1 ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <?php if (!empty($animal['notes'])): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Special Notes</h6>
                            <div class="bg-light p-3 rounded">
                                <?php echo nl2br(htmlspecialchars($animal['notes'])); ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Treatments Section -->
            <div class="dashboard-card fade-in mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-stethoscope me-2"></i>Recent Treatments
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (empty($treatments)): ?>
                        <p class="text-muted">No treatments recorded yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($treatments, 0, 5) as $treatment): ?>
                                    <tr>
                                        <td><?php echo date('M j, Y', strtotime($treatment['treatment_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($treatment['treatment_type']); ?></td>
                                        <td><?php echo htmlspecialchars($treatment['description']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $treatment['status'] == 'completed' ? 'success' : 
                                                     ($treatment['status'] == 'ongoing' ? 'warning' : 'secondary'); 
                                            ?>">
                                                <?php echo ucfirst($treatment['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if (count($treatments) > 5): ?>
                            <div class="text-center mt-2">
                                <a href="<?php echo url('/client/animals/' . ($animal['animal_id'] ?? '') . '/medical-history'); ?>" class="btn btn-sm btn-outline-primary">
                                    View All Treatments
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="dashboard-card fade-in">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Quick Stats
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Total Treatments
                            <span class="badge bg-primary rounded-pill"><?php echo count($treatments); ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Total Vaccines
                            <span class="badge bg-success rounded-pill"><?php echo count($vaccines); ?></span>
                        </div>
                        <?php if ($lastTreatment): ?>
                        <div class="list-group-item">
                            <small class="text-muted">Last Treatment</small><br>
                            <strong><?php echo date('M j, Y', strtotime($lastTreatment['treatment_date'])); ?></strong>
                        </div>
                        <?php endif; ?>
                        <?php if ($nextVaccination): ?>
                        <div class="list-group-item">
                            <small class="text-muted">Next Vaccine Due</small><br>
                            <strong><?php echo date('M j, Y', strtotime($nextVaccination['vaccine_date'])); ?></strong>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="dashboard-card fade-in mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-cogs me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo url('/client/animals/' . ($animal['animal_id'] ?? '') . '/edit'); ?>" class="btn btn-outline-warning">
                            <i class="fas fa-edit me-2"></i>Edit Animal
                        </a>
                        <a href="<?php echo url('/client/animals/' . ($animal['animal_id'] ?? '') . '/medical-history'); ?>" class="btn btn-outline-info">
                            <i class="fas fa-history me-2"></i>View Full History
                        </a>
                        <button type="button" 
                                class="btn btn-outline-danger delete-animal" 
                                data-animal-id="<?php echo $animal['animal_id'] ?? ''; ?>"
                                data-animal-name="<?php echo htmlspecialchars($animal['name'] ?? ''); ?>">
                            <i class="fas fa-trash me-2"></i>Delete Animal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteAnimalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteAnimalName"></strong>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteAnimalForm" method="POST" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    <button type="submit" class="btn btn-danger">Delete Animal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete animal confirmation
    const deleteButtons = document.querySelectorAll('.delete-animal');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteAnimalModal'));
    const deleteAnimalName = document.getElementById('deleteAnimalName');
    const deleteAnimalForm = document.getElementById('deleteAnimalForm');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const animalId = this.getAttribute('data-animal-id');
            const animalName = this.getAttribute('data-animal-name');
            
            deleteAnimalName.textContent = animalName;
            deleteAnimalForm.action = '<?php echo url('/client/animals'); ?>/' + animalId + '/delete';
            
            deleteModal.show();
        });
    });
});
</script>