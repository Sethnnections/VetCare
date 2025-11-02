<?php
$feedbacks = $feedbacks ?? [];
$stats = $stats ?? [];
$current_page = $current_page ?? 1;
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-comments me-2"></i>My Feedback
                    </h4>
                    <a href="<?php echo url('/feedback/create'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Submit New Feedback
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
                    </div>

                    <!-- Feedback List -->
                    <?php if (empty($feedbacks)): ?>
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                                <h4>No Feedback Submitted</h4>
                                <p class="text-muted">You haven't submitted any feedback yet.</p>
                                <a href="<?php echo url('/feedback/create'); ?>" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-1"></i>Submit Your First Feedback
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
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