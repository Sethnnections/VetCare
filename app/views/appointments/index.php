<?php
// app/views/appointments/index.php
$current_page = 'appointments';
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            
            <div class="col-auto">
                <a href="<?php echo url('/appointments/calendar'); ?>" class="btn btn-outline-primary me-2">
                    <i class="fas fa-calendar-alt me-1"></i> Calendar View
                </a>
                <a href="<?php echo url('/appointments/create'); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Schedule Appointment
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['total'] ?? 0; ?></div>
                    <div class="stat-label">Total</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['scheduled'] ?? 0; ?></div>
                    <div class="stat-label">Scheduled</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['confirmed'] ?? 0; ?></div>
                    <div class="stat-label">Confirmed</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['today'] ?? 0; ?></div>
                    <div class="stat-label">Today</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-check-double"></i>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['completed'] ?? 0; ?></div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card danger">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['cancelled'] ?? 0; ?></div>
                    <div class="stat-label">Cancelled</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="dashboard-card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Appointment List
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

            <?php if (empty($appointments)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5>No appointments found</h5>
                    <p class="text-muted">Schedule your first appointment to get started.</p>
                    <a href="<?php echo url('/appointments/create'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Schedule Appointment
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Animal</th>
                                <th>Client</th>
                                <th>Veterinary</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $appointment): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo $appointment['formatted_date'] ?? $appointment['appointment_date']; ?></div>
                                        <small class="text-muted"><?php echo $appointment['formatted_time'] ?? $appointment['appointment_time']; ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?php echo $appointment['animal_name'] ?? 'N/A'; ?></div>
                                        <small class="text-muted"><?php echo $appointment['species'] ?? ''; ?></small>
                                    </td>
                                    <td><?php echo $appointment['client_full_name'] ?? 'N/A'; ?></td>
                                    <td><?php echo $appointment['vet_full_name'] ?? 'Unassigned'; ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo ucfirst($appointment['appointment_type']); ?></span>
                                    </td>
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
                                        $statusText = [
                                            'scheduled' => 'Scheduled',
                                            'confirmed' => 'Confirmed', 
                                            'in_progress' => 'In Progress',
                                            'completed' => 'Completed',
                                            'cancelled' => 'Cancelled',
                                            'no_show' => 'No Show'
                                        ];
                                        ?>
                                        <span class="badge <?php echo $statusClass[$appointment['status']] ?? 'bg-secondary'; ?>">
                                            <?php echo $statusText[$appointment['status']] ?? ucfirst($appointment['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo url('/appointments/' . $appointment['appointment_id']); ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if (in_array($_SESSION['role'], ['admin', 'veterinary'])): ?>
                                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                        data-bs-toggle="dropdown">
                                                    <i class="fas fa-cog"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="#" 
                                                           onclick="updateStatus(<?php echo $appointment['appointment_id']; ?>, 'confirmed')">
                                                            <i class="fas fa-check text-success me-2"></i>Confirm
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" 
                                                           onclick="updateStatus(<?php echo $appointment['appointment_id']; ?>, 'completed')">
                                                            <i class="fas fa-check-double text-primary me-2"></i>Complete
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" 
                                                           onclick="updateStatus(<?php echo $appointment['appointment_id']; ?>, 'cancelled')">
                                                            <i class="fas fa-times me-2"></i>Cancel
                                                        </a>
                                                    </li>
                                                </ul>
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