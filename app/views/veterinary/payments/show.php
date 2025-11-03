<?php
$billing = $billing ?? [];
$billing_items = $billing_items ?? [];
$current_page = 'veterinary_payments';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>Payment Details #<?php echo $billing['billing_id']; ?>
                    </h4>
                    <div class="btn-group">
                        <a href="<?php echo url('/veterinary/payments'); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Payments
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
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Payment Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>Client:</strong><br>
                                            <?php echo htmlspecialchars($billing['client_name']); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Animal:</strong><br>
                                            <?php echo htmlspecialchars($billing['animal_name']); ?>
                                            (<?php echo htmlspecialchars($billing['species']); ?>)
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>Payment Date:</strong><br>
                                            <?php echo formatDate($billing['payment_date'], 'F j, Y'); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Payment Method:</strong><br>
                                            <?php echo ucfirst(str_replace('_', ' ', $billing['payment_method'])); ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <strong>Total Amount:</strong><br>
                                            <h4 class="text-primary">MWK <?php echo number_format($billing['total_amount'], 2); ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Deposit Slip Section -->
                            <?php if ($billing['deposit_slip']): ?>
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Deposit Slip</h5>
                                </div>
                                <div class="card-body text-center">
                                    <p class="mb-3">Client has uploaded a deposit slip for verification.</p>
                                    <a href="<?php echo url('/billings/' . $billing['billing_id'] . '/download-slip'); ?>" 
                                       class="btn btn-outline-primary" target="_blank">
                                        <i class="fas fa-download me-2"></i>View Slip
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Status Section -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Payment Status</h5>
                                </div>
                                <div class="card-body">
                                    <?php
                                    $statusBadge = [
                                        'pending' => 'bg-warning',
                                        'paid' => 'bg-success', 
                                        'verified' => 'bg-info',
                                        'cancelled' => 'bg-danger'
                                    ];
                                    ?>
                                    <span class="badge <?php echo $statusBadge[$billing['payment_status']] ?? 'bg-secondary'; ?> fs-6">
                                        <?php echo ucfirst($billing['payment_status']); ?>
                                    </span>
                                    
                                    <?php if ($billing['verified_by']): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            Verified by: <?php echo htmlspecialchars($billing['verified_by_first_name'] . ' ' . $billing['verified_by_last_name']); ?><br>
                                            on <?php echo formatDate($billing['verified_at'], 'M j, Y'); ?>
                                        </small>
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