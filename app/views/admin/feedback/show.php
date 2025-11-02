<?php
$feedback = $feedback ?? [];
$current_role = $_SESSION['role'] ?? 'client';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-12 mx-auto">
            <div class="dashboard-card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-comment me-2"></i>Feedback Details
                    </h4>
                    <a href="<?php echo url('/' . $current_role . '/feedback'); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Feedback
                    </a>
                </div>
                <div class="card-body">
                    <!-- Feedback Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Client Information</h6>
                            <p class="mb-1">
                                <strong><?php echo htmlspecialchars($feedback['client_full_name']); ?></strong>
                            </p>
                            <p class="text-muted small">
                                Submitted: <?php echo formatDateTime($feedback['created_at']); ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Status</h6>
                            <?php
                            $statusBadge = [
                                'submitted' => 'bg-primary',
                                'reviewed' => 'bg-info',
                                'responded' => 'bg-success'
                            ];
                            ?>
                            <span class="badge <?php echo $statusBadge[$feedback['status']] ?? 'bg-secondary'; ?> fs-6">
                                <?php echo ucfirst($feedback['status']); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Rating -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted">Rating</h6>
                            <div class="star-rating fs-3">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $feedback['rating']) {
                                        echo '<i class="fas fa-star text-warning"></i>';
                                    } else {
                                        echo '<i class="far fa-star text-muted"></i>';
                                    }
                                }
                                ?>
                                <span class="ms-2">(<?php echo $feedback['rating']; ?>/5)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Related Information -->
                    <div class="row mb-4">
                        <?php if ($feedback['vet_full_name']): ?>
                        <div class="col-md-6">
                            <h6 class="text-muted">Veterinary</h6>
                            <p><?php echo htmlspecialchars($feedback['vet_full_name']); ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($feedback['animal_name']): ?>
                        <div class="col-md-6">
                            <h6 class="text-muted">Animal</h6>
                            <p><?php echo htmlspecialchars($feedback['animal_name']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Comments -->
                    <div class="mb-4">
                        <h6 class="text-muted">Comments</h6>
                        <div class="border rounded p-3 bg-light">
                            <?php if ($feedback['comments']): ?>
                                <?php echo nl2br(htmlspecialchars($feedback['comments'])); ?>
                            <?php else: ?>
                                <p class="text-muted fst-italic">No comments provided.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Response (if any) -->
                    <?php if ($feedback['response']): ?>
                    <div class="mb-4">
                        <h6 class="text-muted">
                            <i class="fas fa-reply me-1"></i>Response
                            <?php if ($feedback['vet_full_name']): ?>
                                <small class="text-muted">from <?php echo htmlspecialchars($feedback['vet_full_name']); ?></small>
                            <?php endif; ?>
                        </h6>
                        <div class="border rounded p-3 bg-light">
                            <?php echo nl2br(htmlspecialchars($feedback['response'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Admin Notes (Admin only) -->
                    <?php if ($current_role == 'admin' && $feedback['admin_notes']): ?>
                    <div class="mb-4">
                        <h6 class="text-muted">
                            <i class="fas fa-sticky-note me-1"></i>Admin Notes
                        </h6>
                        <div class="border rounded p-3 bg-light">
                            <?php echo nl2br(htmlspecialchars($feedback['admin_notes'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.star-rating {
    font-size: 1.5rem;
}
</style>