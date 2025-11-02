<?php
$feedbacks = $feedbacks ?? [];
$stats = $stats ?? [];
$current_page = $current_page ?? 1;
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-comment-medical me-2"></i>Client Feedback
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
                        <div class="stat-card success">
                            <div class="stat-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?php echo number_format($stats['avg_rating'] ?? 0, 1); ?></div>
                                <div class="stat-label">Average Rating</div>
                            </div>
                        </div>
                        <div class="stat-card info">
                            <div class="stat-icon">
                                <i class="fas fa-star text-warning"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?php echo $stats['five_star'] ?? 0; ?></div>
                                <div class="stat-label">5-Star Ratings</div>
                            </div>
                        </div>
                    </div>

                    <!-- Feedback List -->
                    <?php if (empty($feedbacks)): ?>
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-comment-slash fa-4x text-muted mb-3"></i>
                                <h4>No Feedback Yet</h4>
                                <p class="text-muted">You haven't received any feedback from clients yet.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Client</th>
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
                                                <?php if ($feedback['status'] == 'submitted'): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#responseModal"
                                                            data-feedback-id="<?php echo $feedback['feedback_id']; ?>">
                                                        <i class="fas fa-reply"></i>
                                                    </button>
                                                <?php endif; ?>
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

<!-- Response Modal -->
<div class="modal fade" id="responseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Respond to Feedback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo url('/feedback/{id}/respond'); ?>" id="responseForm">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="response" class="form-label">Your Response</label>
                        <textarea class="form-control" id="response" name="response" rows="4" 
                                  placeholder="Enter your response to the client's feedback..." required></textarea>
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
    const responseModal = document.getElementById('responseModal');
    const responseForm = document.getElementById('responseForm');
    
    responseModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const feedbackId = button.getAttribute('data-feedback-id');
        
        // Update form action with the correct feedback ID
        responseForm.action = responseForm.action.replace('{id}', feedbackId);
    });
    
    responseModal.addEventListener('hidden.bs.modal', function () {
        // Reset form when modal is closed
        responseForm.reset();
        responseForm.action = responseForm.action.replace(/\d+/, '{id}');
    });
});
</script>