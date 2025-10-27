<?php
// app/views/appointments/reports.php
$current_page = 'appointments';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
          
        </div>
    </div>

    <!-- Report Filters -->
    <div class="dashboard-card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Report Filters
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $startDate; ?>">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $endDate; ?>">
                </div>
                <div class="col-md-3">
                    <label for="veterinary_id" class="form-label">Veterinary</label>
                    <select class="form-control" id="veterinary_id" name="veterinary_id">
                        <option value="">All Veterinarians</option>
                        <?php foreach ($veterinarians as $vet): ?>
                            <option value="<?php echo $vet['user_id']; ?>" 
                                <?php echo ($selectedVeterinary == $vet['user_id']) ? 'selected' : ''; ?>>
                                <?php echo $vet['first_name'] . ' ' . $vet['last_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Generate Report
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['total'] ?? 0; ?></div>
                    <div class="stat-label">Total Appointments</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['daily_average'] ?? 0; ?></div>
                    <div class="stat-label">Daily Average</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['by_status']['completed'] ?? 0; ?></div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card danger">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['by_status']['cancelled'] ?? 0; ?></div>
                    <div class="stat-label">Cancelled</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Report -->
    <div class="dashboard-card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Appointment Details
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($appointments)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                    <h5>No appointments found</h5>
                    <p class="text-muted">No appointments match the selected criteria.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Animal</th>
                                <th>Client</th>
                                <th>Veterinary</th>
                                <th>Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $appointment): ?>
                                <tr>
                                    <td><?php echo $appointment['formatted_date']; ?></td>
                                    <td><?php echo $appointment['formatted_time']; ?></td>
                                    <td><?php echo $appointment['animal_name']; ?></td>
                                    <td><?php echo $appointment['client_full_name']; ?></td>
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
                                        ?>
                                        <span class="badge <?php echo $statusClass[$appointment['status']] ?? 'bg-secondary'; ?>">
                                            <?php echo $appointment['status_text']; ?>
                                        </span>
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