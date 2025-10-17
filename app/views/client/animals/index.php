<?php
// app/views/client/animals/index.php
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
                    <a href="<?php echo url('/client/animals/add'); ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Add New Animal
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

                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="stat-card bg-primary text-white">
                                <div class="stat-card-body">
                                    <h3><?php echo $stats['total'] ?? 0; ?></h3>
                                    <p>Total Animals</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card bg-success text-white">
                                <div class="stat-card-body">
                                    <h3><?php echo $stats['active'] ?? 0; ?></h3>
                                    <p>Active Animals</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (empty($animals)): ?>
                        <!-- No Animals State -->
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-paw fa-4x text-muted mb-3"></i>
                                <h4>No Animals Yet</h4>
                                <p class="text-muted">You haven't added any animals to your profile yet.</p>
                                <a href="<?php echo url('/client/animals/add'); ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add Your First Animal
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Animals Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="animalsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Species</th>
                                        <th>Breed</th>
                                        <th>Gender</th>
                                        <th>Age</th>
                                        <th>Weight</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($animals as $animal): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($animal['name']); ?></strong>
                                            <?php if (!empty($animal['microchip'])): ?>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-microchip me-1"></i>
                                                    <?php echo htmlspecialchars($animal['microchip']); ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars(ucfirst($animal['species'])); ?></td>
                                        <td><?php echo !empty($animal['breed']) ? htmlspecialchars($animal['breed']) : 'N/A'; ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $animal['gender'] == 'male' ? 'primary' : 
                                                     ($animal['gender'] == 'female' ? 'danger' : 'secondary'); 
                                            ?>">
                                                <?php echo htmlspecialchars(ucfirst($animal['gender'])); ?>
                                            </span>
                                        </td>
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
                                        <td>
                                            <?php echo !empty($animal['weight']) ? $animal['weight'] . ' kg' : 'N/A'; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $animal['status'] == 1 ? 'success' : 'secondary'; ?>">
                                                <?php echo $animal['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo url('/client/animals/' . $animal['animal_id']); ?>" 
                                                   class="btn btn-info" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo url('/client/animals/' . $animal['animal_id'] . '/edit'); ?>" 
                                                   class="btn btn-warning" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-danger delete-animal" 
                                                        data-animal-id="<?php echo $animal['animal_id']; ?>"
                                                        data-animal-name="<?php echo htmlspecialchars($animal['name']); ?>"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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

<style>
.stat-card {
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.stat-card h3 {
    font-size: 2.5rem;
    margin-bottom: 5px;
    font-weight: bold;
}

.stat-card p {
    margin-bottom: 0;
    opacity: 0.9;
}

.empty-state {
    padding: 40px 20px;
}

.empty-state i {
    opacity: 0.5;
}

.table th {
    border-top: none;
    font-weight: 600;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
}
</style>

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

    // Initialize DataTable if animals exist
    <?php if (!empty($animals)): ?>
        if (typeof $.fn.DataTable !== 'undefined') {
            $('#animalsTable').DataTable({
                pageLength: 10,
                responsive: true,
                order: [[0, 'asc']],
                language: {
                    search: "Search animals:",
                    lengthMenu: "Show _MENU_ animals per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ animals",
                    infoEmpty: "No animals to show",
                    infoFiltered: "(filtered from _MAX_ total animals)"
                }
            });
        }
    <?php endif; ?>
});
</script>