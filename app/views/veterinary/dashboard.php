<?php
$current_page = 'dashboard';
?>
<div class="container-fluid">
    <!-- Welcome Alert -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-md fa-2x me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'Veterinary'); ?>!</h5>
                        <p class="mb-0">You have <strong><?php echo $stats['today_treatments'] ?? 0; ?> appointments</strong> today and <strong><?php echo $stats['ongoing_treatments'] ?? 0; ?> ongoing treatments</strong>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">My Patients</p>
                            <h4 class="mb-0"><?php echo $stats['my_patients'] ?? 0; ?></h4>
                            <small class="text-muted">Assigned animals</small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-primary rounded-circle">
                                    <i class="fas fa-paw fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Today's Treatments</p>
                            <h4 class="mb-0"><?php echo $stats['today_treatments'] ?? 0; ?></h4>
                            <small class="text-success">Scheduled for today</small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-success rounded-circle">
                                    <i class="fas fa-stethoscope fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Follow-ups</p>
                            <h4 class="mb-0"><?php echo $stats['follow_up_treatments'] ?? 0; ?></h4>
                            <small class="text-warning">Require attention</small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-warning rounded-circle">
                                    <i class="fas fa-clock fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Total Revenue</p>
                            <h4 class="mb-0">MK<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></h4>
                            <small class="text-info">From your treatments</small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-info rounded-circle">
                                    <i class="fas fa-dollar-sign fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row mt-4">
        <!-- Left Column - Charts -->
        <div class="col-xl-8 col-lg-7">
            <!-- Treatment Trends -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">My Treatment Trends</h5>
                </div>
                <div class="card-body">
                    <canvas id="treatmentTrendsChart" height="250"></canvas>
                </div>
            </div>

            <!-- Patient Distribution -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Patient Species Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="patientDistributionChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Right Column - Lists -->
        <div class="col-xl-4 col-lg-5">
            <!-- Today's Appointments -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Today's Appointments</h5>
                    <a href="<?php echo url('/appointments/today'); ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($myAppointments)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($myAppointments as $appointment): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs">
                                            <span class="avatar-title bg-light rounded-circle text-primary">
                                                <i class="fas fa-paw"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($appointment['animal_name']); ?></h6>
                                        <p class="mb-1 text-muted small">
                                            <i class="fas fa-clock me-1"></i>
                                            <?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?>
                                        </p>
                                        <span class="badge bg-<?php echo $appointment['appointment_type'] === 'emergency' ? 'danger' : 'info'; ?>">
                                            <?php echo ucfirst($appointment['appointment_type']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-check fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No appointments today</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Follow-up Treatments -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Follow-up Required</h5>
                    <a href="<?php echo url('/treatments/follow-ups'); ?>" class="btn btn-sm btn-outline-warning">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($followUpTreatments)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($followUpTreatments as $treatment): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs">
                                            <span class="avatar-title bg-warning rounded-circle">
                                                <i class="fas fa-exclamation"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($treatment['animal_name']); ?></h6>
                                        <p class="mb-1 text-muted small">
                                            <i class="fas fa-calendar me-1"></i>
                                            Due: <?php echo date('M j, Y', strtotime($treatment['follow_up_date'])); ?>
                                        </p>
                                        <small class="text-truncate"><?php echo htmlspecialchars(substr($treatment['diagnosis'], 0, 50)); ?>...</small>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="text-muted mb-0">No follow-ups required</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Treatments -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Treatments</h5>
                    <a href="<?php echo url('/veterinary/treatments'); ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentTreatments)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Animal</th>
                                    <th>Client</th>
                                    <th>Diagnosis</th>
                                    <th>Status</th>
                                    <th>Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentTreatments as $treatment): ?>
                                <tr>
                                    <td><?php echo date('M j, Y', strtotime($treatment['treatment_date'])); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-paw text-muted me-2"></i>
                                            <?php echo htmlspecialchars($treatment['animal_name']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($treatment['client_name']); ?></td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;" 
                                              title="<?php echo htmlspecialchars($treatment['diagnosis']); ?>">
                                            <?php echo htmlspecialchars(substr($treatment['diagnosis'], 0, 50)); ?>
                                            <?php if (strlen($treatment['diagnosis']) > 50): ?>...<?php endif; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $treatment['status'] === 'completed' ? 'success' : 
                                                 ($treatment['status'] === 'ongoing' ? 'warning' : 'info'); 
                                        ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $treatment['status'])); ?>
                                        </span>
                                    </td>
                                    <td>MK<?php echo number_format($treatment['cost'] ?? 0, 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-stethoscope fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No recent treatments found</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadVeterinaryAnalytics();
});

function loadVeterinaryAnalytics() {
    fetch('/api/analytics/veterinary')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                initializeVeterinaryCharts(data.data);
            }
        })
        .catch(error => console.error('Error loading analytics:', error));
}

function initializeVeterinaryCharts(analyticsData) {
    const primaryColor = '#1e40af';
    const secondaryColor = '#fd742a';
    const colors = [primaryColor, secondaryColor, '#3b82f6', '#fb923c', '#10b981', '#8b5cf6'];

    // Treatment Trends Chart
    const trendsCtx = document.getElementById('treatmentTrendsChart').getContext('2d');
    const trendsData = analyticsData.treatment_trends || [];
    
    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: trendsData.map(item => item.month),
            datasets: [{
                label: 'Treatments',
                data: trendsData.map(item => item.treatment_count),
                borderColor: primaryColor,
                backgroundColor: primaryColor + '20',
                tension: 0.4,
                fill: true,
                yAxisID: 'y'
            }, {
                label: 'Revenue (MK)',
                data: trendsData.map(item => item.monthly_revenue || 0),
                borderColor: secondaryColor,
                backgroundColor: secondaryColor + '20',
                tension: 0.4,
                fill: true,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Number of Treatments'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Revenue ($)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });

    // Patient Distribution Chart
    const patientCtx = document.getElementById('patientDistributionChart').getContext('2d');
    const patientData = analyticsData.patient_distribution || [];
    
    new Chart(patientCtx, {
        type: 'doughnut',
        data: {
            labels: patientData.map(item => item.species),
            datasets: [{
                data: patientData.map(item => item.patient_count),
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}
</script>

<style>
.card-hover {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: none;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.avatar-title {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
}

.list-group-item {
    border: none;
    padding: 1rem 0;
}

.list-group-item:first-child {
    padding-top: 0;
}

.list-group-item:last-child {
    padding-bottom: 0;
}
</style>