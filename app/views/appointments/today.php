<?php
// app/views/appointments/today.php
$current_page = 'appointments';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
          
            <div class="col-auto">
                <div class="btn-group">
                    <a href="<?php echo url('/appointments/calendar'); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-calendar me-1"></i> Calendar View
                    </a>
                    <a href="<?php echo url('/appointments/create'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> New Appointment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <?php if (empty($appointments)): ?>
            <div class="col-12">
                <div class="dashboard-card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                        <h4>No Appointments Today</h4>
                        <p class="text-muted">You have no appointments scheduled for today.</p>
                        <a href="<?php echo url('/appointments/create'); ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Schedule Appointment
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php 
            // Group appointments by time
            $timeSlots = [];
            foreach ($appointments as $appointment) {
                $time = $appointment['appointment_time'];
                $timeSlots[$time][] = $appointment;
            }
            ksort($timeSlots);
            ?>

            <?php foreach ($timeSlots as $time => $slotAppointments): ?>
                <div class="col-12 mb-4">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-clock me-2"></i><?php echo date('g:i A', strtotime($time)); ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($slotAppointments as $appointment): ?>
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card h-100 border">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title mb-0"><?php echo $appointment['animal_name']; ?></h6>
                                                    <span class="badge bg-<?php 
                                                        echo $appointment['status'] == 'scheduled' ? 'warning' : 
                                                             ($appointment['status'] == 'confirmed' ? 'primary' : 
                                                             ($appointment['status'] == 'in_progress' ? 'info' : 'success')); 
                                                    ?>">
                                                        <?php echo ucfirst($appointment['status']); ?>
                                                    </span>
                                                </div>
                                                <p class="card-text text-muted small mb-2">
                                                    <i class="fas fa-user me-1"></i><?php echo $appointment['client_full_name']; ?>
                                                </p>
                                                <p class="card-text small mb-2">
                                                    <strong>Type:</strong> <?php echo ucfirst($appointment['appointment_type']); ?>
                                                </p>
                                                <p class="card-text small mb-3">
                                                    <strong>Reason:</strong> <?php echo $appointment['reason']; ?>
                                                </p>
                                                <div class="btn-group w-100">
                                                    <a href="<?php echo url('/appointments/' . $appointment['appointment_id']); ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    <?php if ($appointment['status'] == 'scheduled'): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                onclick="updateStatus(<?php echo $appointment['appointment_id']; ?>, 'confirmed')">
                                                            <i class="fas fa-check"></i> Confirm
                                                        </button>
                                                    <?php elseif ($appointment['status'] == 'confirmed'): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                                onclick="updateStatus(<?php echo $appointment['appointment_id']; ?>, 'in_progress')">
                                                            <i class="fas fa-play"></i> Start
                                                        </button>
                                                    <?php elseif ($appointment['status'] == 'in_progress'): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                onclick="updateStatus(<?php echo $appointment['appointment_id']; ?>, 'completed')">
                                                            <i class="fas fa-check-double"></i> Complete
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
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