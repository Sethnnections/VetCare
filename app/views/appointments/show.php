<?php
// app/views/appointments/show.php
$current_page = 'appointments';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
        
            <div class="col-auto">
                <a href="<?php echo url('/appointments'); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <?php if (!empty($appointment)): ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>Appointment Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Date & Time:</th>
                                    <td><?php echo $appointment['formatted_date']; ?> at <?php echo $appointment['formatted_time']; ?></td>
                                </tr>
                                <tr>
                                    <th>Duration:</th>
                                    <td><?php echo $appointment['duration']; ?> minutes</td>
                                </tr>
                                <tr>
                                    <th>Type:</th>
                                    <td>
                                        <span class="badge bg-info"><?php echo ucfirst($appointment['appointment_type']); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <?php 
                                        $statusClass = [
                                            'scheduled' => 'bg-warning',
                                            'confirmed' => 'bg-primary',
                                            'in_progress' => 'bg-info',
                                            'completed' => 'bg-success',
                                            'cancelled' => 'bg-danger',
                                            'no_show' => 'bg-dark'
                                        ];
                                        ?>
                                        <span class="badge <?php echo $statusClass[$appointment['status']] ?? 'bg-secondary'; ?>">
                                            <?php echo $appointment['status_text']; ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Animal:</th>
                                    <td><?php echo $appointment['animal_name']; ?> (<?php echo $appointment['species']; ?>)</td>
                                </tr>
                                <tr>
                                    <th>Client:</th>
                                    <td><?php echo $appointment['client_full_name']; ?></td>
                                </tr>
                                <tr>
                                    <th>Veterinary:</th>
                                    <td><?php echo $appointment['vet_full_name'] ?? 'Unassigned'; ?></td>
                                </tr>
                                <tr>
                                    <th>Created By:</th>
                                    <td><?php echo $appointment['created_by_full_name']; ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Reason for Visit:</h6>
                            <p class="border p-3 rounded bg-light"><?php echo nl2br(htmlspecialchars($appointment['reason'])); ?></p>
                        </div>
                    </div>

                    <?php if (!empty($appointment['notes'])): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Additional Notes:</h6>
                            <p class="border p-3 rounded bg-light"><?php echo nl2br(htmlspecialchars($appointment['notes'])); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cogs me-2"></i>Actions
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (in_array($_SESSION['role'], ['admin', 'veterinary'])): ?>
                    <div class="d-grid gap-2">
                        <?php if ($appointment['status'] == 'scheduled'): ?>
                            <button class="btn btn-success" onclick="updateStatus(<?php echo $appointment['appointment_id']; ?>, 'confirmed')">
                                <i class="fas fa-check me-1"></i> Confirm Appointment
                            </button>
                        <?php elseif ($appointment['status'] == 'confirmed'): ?>
                            <button class="btn btn-info" onclick="updateStatus(<?php echo $appointment['appointment_id']; ?>, 'in_progress')">
                                <i class="fas fa-play me-1"></i> Mark In Progress
                            </button>
                        <?php elseif ($appointment['status'] == 'in_progress'): ?>
                            <button class="btn btn-primary" onclick="updateStatus(<?php echo $appointment['appointment_id']; ?>, 'completed')">
                                <i class="fas fa-check-double me-1"></i> Mark Completed
                            </button>
                        <?php endif; ?>
                        
                        <?php if (in_array($appointment['status'], ['scheduled', 'confirmed'])): ?>
                            <button class="btn btn-danger" onclick="updateStatus(<?php echo $appointment['appointment_id']; ?>, 'cancelled')">
                                <i class="fas fa-times me-1"></i> Cancel Appointment
                            </button>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Created: <?php echo date('M j, Y g:i A', strtotime($appointment['created_at'])); ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Appointment not found or you don't have permission to view it.
    </div>
    <?php endif; ?>
</div>

<script>
function updateStatus(appointmentId, status) {
    if (!confirm('Are you sure you want to update this appointment status?')) {
        return;
    }
    
    fetch(`<?php echo url('/appointments/'); ?>${appointmentId}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `csrf_token=<?php echo generateCsrfToken(); ?>&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating status');
    });
}
</script>