<?php
$current_page = 'client_treatments';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
         
            <div class="col-auto">
                <div class="stats-grid">
                    <div class="stat-card primary">
                        <div class="stat-icon">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <div>
                            <div class="stat-value"><?php echo $stats['total_treatments'] ?? 0; ?></div>
                            <div class="stat-label">Total Treatments</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Treatment History
                    </h5>
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

                    <?php if (empty($treatments)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-stethoscope fa-3x text-muted mb-3"></i>
                            <h5>No treatments found</h5>
                            <p class="text-muted">Your animals don't have any treatments yet.</p>
                            <a href="<?php echo url('/client/animals'); ?>" class="btn btn-primary">
                                <i class="fas fa-paw me-2"></i>View My Animals
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Search -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form method="GET" class="d-flex">
                                    <input type="text" name="search" class="form-control me-2" 
                                           placeholder="Search treatments..." value="<?php echo $search ?? ''; ?>">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="btn-group">
                                    <a href="<?php echo url('/treatments?status=ongoing'); ?>" class="btn btn-outline-warning btn-sm">
                                        Ongoing
                                    </a>
                                    <a href="<?php echo url('/treatments?status=completed'); ?>" class="btn btn-outline-success btn-sm">
                                        Completed
                                    </a>
                                    <a href="<?php echo url('/treatments'); ?>" class="btn btn-outline-secondary btn-sm">
                                        All
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Animal</th>
                                        <th>Diagnosis</th>
                                        <th>Veterinary</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Cost</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($treatments as $treatment): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-paw text-white"></i>
                                                </div>
                                                <div>
                                                    <strong><?php echo $treatment['animal_name']; ?></strong>
                                                    <br><small class="text-muted"><?php echo $treatment['species']; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span title="<?php echo $treatment['diagnosis']; ?>">
                                                <?php echo strLimit($treatment['diagnosis'], 50); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $treatment['vet_full_name']; ?></td>
                                        <td><?php echo formatDate($treatment['treatment_date']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $treatment['treatment_status'] == 'completed' ? 'success' : 
                                                     ($treatment['treatment_status'] == 'ongoing' ? 'warning' : 'info'); 
                                            ?>">
                                                <?php echo ucfirst($treatment['treatment_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong class="text-primary">MK<?php echo number_format($treatment['cost'], 2); ?></strong>
                                        </td>
                                        <td>
                                            <a href="<?php echo url('/treatments/' . $treatment['treatment_id']); ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($pagination['total_pages'] > 1): ?>
                        <nav aria-label="Treatment pagination">
                            <ul class="pagination justify-content-center mt-4">
                                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                    <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo url('/treatments?page=' . $i . ($search ? '&search=' . urlencode($search) : '') . ($status ? '&status=' . $status : '')); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>