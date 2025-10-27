<?php
$current_page = 'dashboard';
?>
<div class="container-fluid">

    <!-- System Alerts -->
    <?php if (!empty($systemAlerts)): ?>
    <div class="row">
        <div class="col-12">
            <?php foreach ($systemAlerts as $alert): ?>
            <div class="alert alert-<?php echo $alert['type']; ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo $alert['message']; ?>
                <?php if (isset($alert['link'])): ?>
                <a href="<?php echo url($alert['link']); ?>" class="alert-link">View details</a>
                <?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Total Users</p>
                            <h4 class="mb-0"><?php echo $stats['total_users'] ?? 0; ?></h4>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>
                                <?php echo number_format($stats['user_growth'] ?? 0, 1); ?>% growth
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-primary rounded-circle">
                                    <i class="fas fa-users fs-4"></i>
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
                            <p class="text-muted fw-medium mb-2">Total Animals</p>
                            <h4 class="mb-0"><?php echo $stats['total_animals'] ?? 0; ?></h4>
                            <small class="text-muted">Active patients</small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-success rounded-circle">
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
                            <p class="text-muted fw-medium mb-2">Total Treatments</p>
                            <h4 class="mb-0"><?php echo $stats['total_treatments'] ?? 0; ?></h4>
                            <small class="text-info">
                                <?php echo $stats['active_treatments'] ?? 0; ?> active
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-info rounded-circle">
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
                            <p class="text-muted fw-medium mb-2">Total Revenue</p>
                            <h4 class="mb-0">MK<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></h4>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>
                                <?php echo number_format($stats['revenue_growth'] ?? 0, 1); ?>% growth
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-warning rounded-circle">
                                    <i class="fas fa-dollar-sign fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts -->
    <div class="row mt-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Revenue Analytics</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Last 6 Months
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="changeChartPeriod('3m')">Last 3 Months</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeChartPeriod('6m')">Last 6 Months</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeChartPeriod('1y')">Last Year</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Treatment Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="treatmentDistributionChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Charts -->
    <div class="row mt-4">
        <div class="col-xl-6 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">User Growth</h5>
                </div>
                <div class="card-body">
                    <canvas id="userGrowthChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Animal Species Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="speciesChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row mt-4">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Users</h5>
                    <a href="<?php echo url('/admin/users'); ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentUsers as $user): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-xs">
                                                    <span class="avatar-title bg-light rounded-circle">
                                                        <?php echo strtoupper(substr($user['first_name'] ?? $user['username'], 0, 1)); ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'veterinary' ? 'info' : 'success'); ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <span class="badge bg-success">Active</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Treatments</h5>
                    <a href="<?php echo url('/treatments'); ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Animal</th>
                                    <th>Client</th>
                                    <th>Veterinary</th>
                                    <th>Date</th>
                                    <th>Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentTreatments as $treatment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($treatment['animal_name']); ?></td>
                                    <td><?php echo htmlspecialchars($treatment['client_name']); ?></td>
                                    <td><?php echo htmlspecialchars($treatment['vet_name']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($treatment['treatment_date'])); ?></td>
                                    <td>$<?php echo number_format($treatment['cost'] ?? 0, 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Appointments -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Upcoming Appointments</h5>
                    <a href="<?php echo url('/appointments'); ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($upcomingAppointments)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Animal</th>
                                    <th>Client</th>
                                    <th>Type</th>
                                    <th>Veterinary</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcomingAppointments as $appointment): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo date('M j, Y', strtotime($appointment['appointment_date'])); ?></strong><br>
                                        <small class="text-muted"><?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($appointment['animal_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['client_name']); ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo ucfirst($appointment['appointment_type']); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($appointment['vet_name'] ?? 'Not Assigned'); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $appointment['status'] === 'confirmed' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $appointment['status'])); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No upcoming appointments</p>
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
    loadAdminAnalytics();
});

function loadAdminAnalytics() {
    fetch('/api/analytics/admin')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                initializeCharts(data.data);
            }
        })
        .catch(error => console.error('Error loading analytics:', error));
}

function initializeCharts(analyticsData) {
    const primaryColor = '#1e40af'; // Dark blue
    const secondaryColor = '#fd742a'; // Orange
    const lightBlue = '#3b82f6';
    const lightOrange = '#fb923c';
    const colors = [primaryColor, secondaryColor, lightBlue, lightOrange, '#10b981', '#8b5cf6'];

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = analyticsData.revenue_analytics || [];
    
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: revenueData.map(item => item.month),
            datasets: [{
                label: 'Revenue (MK)',
                data: revenueData.map(item => item.total_revenue || 0),
                backgroundColor: primaryColor,
                borderRadius: 6,
                order: 2
            }, {
                label: 'Paid Revenue (MK)',
                data: revenueData.map(item => item.paid_revenue || 0),
                backgroundColor: secondaryColor,
                borderRadius: 6,
                order: 1
            }]
        },
        options: {
            responsive: true,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += 'MK' + context.parsed.y.toLocaleString();
                            return label;
                        }
                    }
                }
            }
        }
    });

    // Treatment Distribution Chart
    const treatmentDistCtx = document.getElementById('treatmentDistributionChart').getContext('2d');
    const treatmentData = analyticsData.treatment_distribution || [];
    
    new Chart(treatmentDistCtx, {
        type: 'doughnut',
        data: {
            labels: treatmentData.map(item => {
                const status = item.treatment_status;
                return status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ');
            }),
            datasets: [{
                data: treatmentData.map(item => item.count),
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    const userData = analyticsData.user_growth || [];
    
    new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: userData.map(item => item.month),
            datasets: [{
                label: 'Total Users',
                data: userData.map(item => item.total_users),
                borderColor: primaryColor,
                backgroundColor: primaryColor + '20',
                tension: 0.4,
                fill: true
            }, {
                label: 'New Clients',
                data: userData.map(item => item.client_users),
                borderColor: secondaryColor,
                backgroundColor: secondaryColor + '20',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Species Distribution Chart
    const speciesCtx = document.getElementById('speciesChart').getContext('2d');
    const speciesData = analyticsData.species_distribution || [];
    
    new Chart(speciesCtx, {
        type: 'pie',
        data: {
            labels: speciesData.map(item => item.species),
            datasets: [{
                data: speciesData.map(item => item.animal_count),
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
}

function changeChartPeriod(period) {
    // Implement period change functionality
    console.log('Changing period to:', period);
    // You can reload charts with different data based on period
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

.table th {
    border-top: none;
    font-weight: 600;
    color: #6c757d;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
</style>