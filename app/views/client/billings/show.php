<?php
$billing = $billing ?? [];
$billing_items = $billing_items ?? [];
$current_page = 'client_billings';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Bill Details #<?php echo $billing['billing_id']; ?>
                    </h4>
                    <div class="btn-group">
                        <a href="<?php echo url('/billings/' . $billing['billing_id'] . '/invoice'); ?>" 
                           class="btn btn-outline-primary" target="_blank">
                            <i class="fas fa-download me-1"></i>Download Invoice
                        </a>
                        <a href="<?php echo url('/client/billings'); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Bills
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
                        <!-- Billing Information -->
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Billing Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>Bill Date:</strong><br>
                                            <?php echo formatDate($billing['billing_date'], 'F j, Y'); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Due Date:</strong><br>
                                            <?php echo $billing['due_date'] ? formatDate($billing['due_date'], 'F j, Y') : 'Not specified'; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>Animal:</strong><br>
                                            <?php echo htmlspecialchars($billing['animal_name']); ?>
                                            (<?php echo htmlspecialchars($billing['species']); ?>)
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Veterinary:</strong><br>
                                            <?php echo $billing['vet_name'] ? htmlspecialchars($billing['vet_name']) : 'Not assigned'; ?>
                                        </div>
                                    </div>

                                    <?php if ($billing['treatment_id']): ?>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <strong>Treatment:</strong><br>
                                            <?php echo htmlspecialchars($billing['diagnosis'] ?? 'N/A'); ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if ($billing['notes']): ?>
                                    <div class="row">
                                        <div class="col-12">
                                            <strong>Notes:</strong><br>
                                            <?php echo nl2br(htmlspecialchars($billing['notes'])); ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Payment Details -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Payment Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Base Amount:</strong><br>
                                            MWK <?php echo number_format($billing['amount'], 2); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Tax Amount:</strong><br>
                                            MWK <?php echo number_format($billing['tax_amount'] ?? 0, 2); ?>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <strong>Discount:</strong><br>
                                            MWK <?php echo number_format($billing['discount'] ?? 0, 2); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Total Amount:</strong><br>
                                            <h4 class="text-primary">MWK <?php echo number_format($billing['total_amount'], 2); ?></h4>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row">
                                        <div class="col-12">
                                            <strong>Payment Status:</strong>
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
                                        </div>
                                    </div>

                                    <?php if ($billing['payment_status'] === 'paid' || $billing['payment_status'] === 'verified'): ?>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <strong>Payment Method:</strong><br>
                                            <?php echo ucfirst(str_replace('_', ' ', $billing['payment_method'])); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Payment Date:</strong><br>
                                            <?php echo formatDate($billing['payment_date'], 'F j, Y'); ?>
                                        </div>
                                    </div>

                                    <?php if ($billing['verified_by']): ?>
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <strong>Verified By:</strong><br>
                                            <?php echo htmlspecialchars($billing['verified_by_first_name'] . ' ' . $billing['verified_by_last_name']); ?>
                                            on <?php echo formatDate($billing['verified_at'], 'F j, Y \a\t g:i A'); ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Actions & Deposit Slip -->
                        <div class="col-md-4">
                            <!-- Payment Action -->
                            <?php if ($billing['payment_status'] === 'pending'): ?>
                            <div class="card mb-4">
                                <div class="card-header bg-warning text-white">
                                    <h5 class="card-title mb-0">Payment Required</h5>
                                </div>
                                <div class="card-body text-center">
                                    <p class="mb-3">This bill is pending payment. Please make payment to avoid late fees.</p>
                                    <a href="<?php echo url('/client/billings/' . $billing['billing_id'] . '/payment'); ?>" 
                                       class="btn btn-success btn-lg">
                                        <i class="fas fa-credit-card me-2"></i>Make Payment
                                    </a>
                                    
                                    <?php 
                                    $isOverdue = $billing['due_date'] && strtotime($billing['due_date']) < time();
                                    if ($isOverdue): ?>
                                    <div class="alert alert-danger mt-3">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        This bill is overdue! Please pay immediately.
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Deposit Slip -->
                            <?php if ($billing['deposit_slip']): ?>
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Deposit Slip</h5>
                                </div>
                                <div class="card-body text-center">
                                    <p class="mb-3">Your deposit slip has been uploaded and is awaiting verification.</p>
                                    <a href="<?php echo url('/billings/' . $billing['billing_id'] . '/download-slip'); ?>" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-download me-2"></i>Download Slip
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Bill Summary -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Quick Summary</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Bill Number:</td>
                                            <td><strong>#<?php echo $billing['billing_id']; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td>Status:</td>
                                            <td>
                                                <span class="badge <?php echo $statusBadge[$billing['payment_status']] ?? 'bg-secondary'; ?>">
                                                    <?php echo ucfirst($billing['payment_status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Total Due:</td>
                                            <td><strong>MWK <?php echo number_format($billing['total_amount'], 2); ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td>Days Outstanding:</td>
                                            <td>
                                                <?php
                                                $days = floor((time() - strtotime($billing['billing_date'])) / (60 * 60 * 24));
                                                echo $days . ' days';
                                                ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}
</style>