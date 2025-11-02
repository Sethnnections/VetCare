<?php
$feedbacks = $feedbacks ?? [];
$stats = $stats ?? [];
$veterinarians = $veterinarians ?? [];
$filters = $filters ?? [];
$current_page = $current_page ?? 1;
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-comments me-2"></i>Manage Feedback
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

                    <!-- Statistics Cards -->
                    <div class="stats-grid mb-4">
                        <div class="stat-card primary">
                            <div class="stat-icon">
                                <i class="fas fa-comment"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?php echo $stats['total'] ?? 0; ?></div>
                                <div class="stat-label">Total Feedback</div>
                            </div>
                        </div>
                        <div class="stat-card warning">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?php echo $stats['submitted'] ?? 0; ?></div>
                                <div class="stat-label">Pending Review</div>
                            </div>
                        </div>
                        <div class="stat-card success">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?php echo $stats['responded'] ?? 0; ?></div>
                                <div class="stat-label">Responded</div>
                            </div>
                        </div>
                        <div class="stat-card info">
                            <div class="stat-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?php echo number_format($stats['avg_rating'] ?? 0, 1); ?></div>
                                <div class="stat-label">Avg Rating</div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">All Status</option>
                                        <option value="submitted" <?php echo ($filters['status'] ?? '') == 'submitted' ? 'selected' : ''; ?>>Submitted</option>
                                        <option value="reviewed" <?php echo ($filters['status'] ?? '') == 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                                        <option value="responded" <?php echo ($filters['status'] ?? '') == 'responded' ? 'selected' : ''; ?>>Responded</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="rating" class="form-label">Rating</label>
                                    <select class="form-select" id="rating" name="rating">
                                        <option value="">All Ratings</option>
                                        <option value="5" <?php echo ($filters['rating'] ?? '') == '5' ? 'selected' : ''; ?>>5 Stars</option>
                                        <option value="4" <?php echo ($filters['rating'] ?? '') == '4' ? 'selected' : ''; ?>>4 Stars</option>
                                        <option value="3" <?php echo ($filters['rating'] ?? '') == '3' ? 'selected' : ''; ?>>3 Stars</option>
                                        <option value="2" <?php echo ($filters['rating'] ?? '') == '2' ? 'selected' : ''; ?>>2 Stars</option>
                                        <option value="1" <?php echo ($filters['rating'] ?? '') == '1' ? 'selected' : ''; ?>>1 Star</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="veterinary_id" class="form-label">Veterinary</label>
                                    <select class="form-select" id="veterinary_id" name="veterinary_id">
                                        <option value="">All Veterinarians</option>
                                        <?php foreach ($veterinarians as $vet): ?>
                                            <option value="<?php echo $vet['user_id']; ?>" 
                                                    <?php echo ($filters['veterinary_id'] ?? '') == $vet['user_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($vet['first_name'] . ' ' . $vet['last_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-filter me-1"></i>Filter
                                    </button>
                                    <a href="<?php echo url('/admin/feedback'); ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Clear
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Feedback List -->
                    <?php if (empty($feedbacks)): ?>
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                                <h4>No Feedback Found</h4>
                                <p class="text-muted">No feedback matches your current filters.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Client</th>
                                        <th>Veterinary</th>
                                        <th>Animal</th>
                                        <th>Rating</th>
                                        <th>Comments</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($feedbacks as $feedback): ?>
                                    <tr>
                                        <td><?php echo formatDate($feedback['created_at'], 'M j, Y'); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($feedback['client_first_name'] . ' ' . $feedback['client_last_name']); ?>
                                        </td>
                                        <td>
                                            <?php if ($feedback['vet_first_name']): ?>
                                                <?php echo htmlspecialchars($feedback['vet_first_name'] . ' ' . $feedback['vet_last_name']); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Not specified</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($feedback['animal_name']): ?>
                                                <?php echo htmlspecialchars($feedback['animal_name']); ?>
                                            <?php else: ?>
                                                <span class="text-muted">General</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="star-rating">
                                                <?php
                                                for ($i = 1; $i <= 5; $i++) {
                                                    if ($i <= $feedback['rating']) {
                                                        echo '<i class="fas fa-star text-warning"></i>';
                                                    } else {
                                                        echo '<i class="far fa-star text-muted"></i>';
                                                    }
                                                }
                                                ?>
                                                <small class="text-muted ms-1">(<?php echo $feedback['rating']; ?>/5)</small>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($feedback['comments']): ?>
                                                <span class="d-inline-block text-truncate" style="max-width: 200px;" 
                                                      title="<?php echo htmlspecialchars($feedback['comments']); ?>">
                                                    <?php echo htmlspecialchars($feedback['comments']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">No comments</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusBadge = [
                                                'submitted' => 'bg-primary',
                                                'reviewed' => 'bg-info',
                                                'responded' => 'bg-success'
                                            ];
                                            ?>
                                            <span class="badge <?php echo $statusBadge[$feedback['status']] ?? 'bg-secondary'; ?>">
                                                <?php echo ucfirst($feedback['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo url('/feedback/' . $feedback['feedback_id']); ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#adminResponseModal"
                                                        data-feedback-id="<?php echo $feedback['feedback_id']; ?>"
                                                        data-feedback-status="<?php echo $feedback['status']; ?>">
                                                    <i class="fas fa-reply"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#statusModal"
                                                        data-feedback-id="<?php echo $feedback['feedback_id']; ?>"
                                                        data-feedback-status="<?php echo $feedback['status']; ?>">
                                                    <i class="fas fa-edit"></i>
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

<!-- Admin Response Modal -->
<div class="modal fade" id="adminResponseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Admin Response</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo url('/feedback/{id}/respond'); ?>" id="adminResponseForm">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="admin_response" class="form-label">Response</label>
                        <textarea class="form-control" id="admin_response" name="response" rows="4" 
                                  placeholder="Enter your response to the feedback..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Response</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Feedback Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo url('/feedback/{id}/update-status'); ?>" id="statusForm">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="submitted">Submitted</option>
                            <option value="reviewed">Reviewed</option>
                            <option value="responded">Responded</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" 
                                  placeholder="Add any internal notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.star-rating {
    font-size: 0.9rem;
}
.empty-state {
    padding: 40px 20px;
}
.empty-state i {
    opacity: 0.5;
}
.table td {
    vertical-align: middle;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Admin Response Modal
    const adminResponseModal = document.getElementById('adminResponseModal');
    const adminResponseForm = document.getElementById('adminResponseForm');
    
    adminResponseModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const feedbackId = button.getAttribute('data-feedback-id');
        
        adminResponseForm.action = adminResponseForm.action.replace('{id}', feedbackId);
    });
    
    adminResponseModal.addEventListener('hidden.bs.modal', function () {
        adminResponseForm.reset();
        adminResponseForm.action = adminResponseForm.action.replace(/\d+/, '{id}');
    });
    
    // Status Modal
    const statusModal = document.getElementById('statusModal');
    const statusForm = document.getElementById('statusForm');
    
    statusModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const feedbackId = button.getAttribute('data-feedback-id');
        const currentStatus = button.getAttribute('data-feedback-status');
        
        statusForm.action = statusForm.action.replace('{id}', feedbackId);
        document.getElementById('status').value = currentStatus;
    });
    
    statusModal.addEventListener('hidden.bs.modal', function () {
        statusForm.reset();
        statusForm.action = statusForm.action.replace(/\d+/, '{id}');
    });
});
</script>