<?php
$current_page = 'dashboard';
?>
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle fa-2x me-3"></i>
                        <div>
                            <h4 class="card-title mb-1">Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'Client'); ?>!</h4>
                            <p class="mb-0">Here's your pet health overview</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php 
                    $flash = getFlashMessage();
                    if ($flash): ?>
                        <div class="alert alert-<?php echo $flash['type']; ?>">
                            <?php echo $flash['message']; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        Welcome to your dashboard! Your profile is complete.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mt-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">My Animals</p>
                            <h4 class="mb-0"><?php echo $stats['my_animals'] ?? 0; ?></h4>
                            <small class="text-muted">Registered pets</small>
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
                            <p class="text-muted fw-medium mb-2">Total Treatments</p>
                            <h4 class="mb-0"><?php echo $stats['total_treatments'] ?? 0; ?></h4>
                            <small class="text-info"><?php echo $stats['ongoing_treatments'] ?? 0; ?> ongoing</small>
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
                            <p class="text-muted fw-medium mb-2">Vaccinations</p>
                            <h4 class="mb-0"><?php echo $stats['total_vaccinations'] ?? 0; ?></h4>
                            <small class="text-success">Completed</small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-success rounded-circle">
                                    <i class="fas fa-syringe fs-4"></i>
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
                            <p class="text-muted fw-medium mb-2">Total Spent</p>
                            <h4 class="mb-0">MK<?php echo number_format($stats['total_spent'] ?? 0, 2); ?></h4>
                            <small class="text-muted">All-time total</small>
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

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo url('/client/animals'); ?>" class="btn btn-primary w-100 action-btn">
                                <i class="fas fa-paw me-2"></i>
                                My Animals
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo url('/client/profile'); ?>" class="btn btn-success w-100 action-btn">
                                <i class="fas fa-user me-2"></i>
                                My Profile
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo url('/appointments/book'); ?>" class="btn btn-info w-100 action-btn">
                                <i class="fas fa-calendar-plus me-2"></i>
                                Book Appointment
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo url('/client/vaccinations'); ?>" class="btn btn-warning w-100 action-btn">
                                <i class="fas fa-syringe me-2"></i>
                                Vaccinations
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row mt-4">
        <!-- Left Column - My Animals -->
        <div class="col-xl-6 col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">My Animals</h5>
                    <a href="<?php echo url('/client/animals/add'); ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus me-1"></i>Add Animal
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($myAnimals)): ?>
                        <div class="row">
                            <?php foreach ($myAnimals as $animal): ?>
                            <div class="col-md-6 mb-3">
                                <div class="animal-card card h-100">
                                    <div class="card-body text-center">
                                        <div class="avatar-lg mx-auto mb-3">
                                            <span class="avatar-title bg-light rounded-circle text-primary">
                                                <i class="fas fa-paw fa-2x"></i>
                                            </span>
                                        </div>
                                        <h5 class="card-title"><?php echo htmlspecialchars($animal['name']); ?></h5>
                                        <p class="text-muted mb-2">
                                            <span class="badge bg-info"><?php echo ucfirst($animal['species']); ?></span>
                                            <?php if (!empty($animal['breed'])): ?>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($animal['breed']); ?></span>
                                            <?php endif; ?>
                                        </p>
                                        <p class="small text-muted mb-3">
                                            <i class="fas fa-birthday-cake me-1"></i>
                                            <?php 
                                            $age = '';
                                            if (!empty($animal['birth_date'])) {
                                                $birthDate = new DateTime($animal['birth_date']);
                                                $today = new DateTime();
                                                $age = $today->diff($birthDate);
                                                if ($age->y > 0) {
                                                    echo $age->y . ' year' . ($age->y > 1 ? 's' : '');
                                                } else {
                                                    echo $age->m . ' month' . ($age->m > 1 ? 's' : '');
                                                }
                                            } else {
                                                echo 'Age unknown';
                                            }
                                            ?>
                                        </p>
                                        <a href="<?php echo url('/client/animals/' . $animal['animal_id']); ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-paw fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Animals Registered</h5>
                            <p class="text-muted mb-3">Add your first animal to get started</p>
                            <a href="<?php echo url('/client/animals/add'); ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Add Your First Animal
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Treatment History Chart -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Treatment History</h5>
                </div>
                <div class="card-body">
                    <canvas id="treatmentHistoryChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Right Column - Appointments & Reminders -->
        <div class="col-xl-6 col-lg-6">
            <!-- Upcoming Appointments -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Upcoming Appointments</h5>
                    <a href="<?php echo url('/appointments'); ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($upcomingAppointments)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($upcomingAppointments as $appointment): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs mt-1">
                                            <span class="avatar-title bg-primary rounded-circle">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($appointment['animal_name']); ?></h6>
                                        <p class="mb-1 text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?php echo date('M j, Y g:i A', strtotime($appointment['appointment_date'] . ' ' . $appointment['appointment_time'])); ?>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-info"><?php echo ucfirst($appointment['appointment_type']); ?></span>
                                            <small class="text-muted">With: <?php echo htmlspecialchars($appointment['vet_name'] ?? 'TBA'); ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No upcoming appointments</p>
                            <a href="<?php echo url('/appointments/book'); ?>" class="btn btn-sm btn-primary mt-2">Book Appointment</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Vaccination Reminders -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Vaccination Reminders</h5>
                    <a href="<?php echo url('/client/vaccinations'); ?>" class="btn btn-sm btn-outline-warning">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($vaccinationReminders)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($vaccinationReminders as $vaccine): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs mt-1">
                                            <span class="avatar-title bg-warning rounded-circle">
                                                <i class="fas fa-syringe"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($vaccine['animal_name']); ?></h6>
                                        <p class="mb-1"><?php echo htmlspecialchars($vaccine['vaccine_name']); ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                Due: <?php echo date('M j, Y', strtotime($vaccine['next_due_date'])); ?>
                                            </small>
                                            <span class="badge bg-<?php echo $vaccine['days_until_due'] <= 7 ? 'danger' : 'warning'; ?>">
                                                <?php echo $vaccine['days_until_due']; ?> days
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="text-muted mb-0">No upcoming vaccinations</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Treatments -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Treatments</h5>
                    <a href="<?php echo url('/client/treatments'); ?>" class="btn btn-sm btn-outline-info">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentTreatments)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recentTreatments as $treatment): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs mt-1">
                                            <span class="avatar-title bg-info rounded-circle">
                                                <i class="fas fa-stethoscope"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($treatment['animal_name']); ?></h6>
                                        <p class="mb-1 text-muted small"><?php echo htmlspecialchars(substr($treatment['diagnosis'], 0, 60)); ?>...</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <?php echo date('M j, Y', strtotime($treatment['treatment_date'])); ?>
                                            </small>
                                            <span class="badge bg-<?php echo $treatment['status'] === 'completed' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($treatment['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-stethoscope fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No recent treatments</p>
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
    loadClientAnalytics();
});

function loadClientAnalytics() {
    fetch('/api/analytics/client')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                initializeClientCharts(data.data);
            }
        })
        .catch(error => console.error('Error loading analytics:', error));
}

function initializeClientCharts(analyticsData) {
    const primaryColor = '#1e40af';
    const secondaryColor = '#fd742a';

    // Treatment History Chart
    const historyCtx = document.getElementById('treatmentHistoryChart').getContext('2d');
    const historyData = analyticsData.treatment_history || [];
    
    new Chart(historyCtx, {
        type: 'bar',
        data: {
            labels: historyData.map(item => item.month),
            datasets: [{
                label: 'Treatment Cost ($)',
                data: historyData.map(item => item.monthly_cost || 0),
                backgroundColor: primaryColor,
                borderRadius: 6
            }, {
                label: 'Number of Treatments',
                data: historyData.map(item => item.treatment_count),
                backgroundColor: secondaryColor,
                borderRadius: 6,
                type: 'line',
                borderColor: secondaryColor,
                borderWidth: 2,
                fill: false
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
}
</script>

<style>
.dashboard-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

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

.avatar-lg {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.animal-card {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.animal-card:hover {
    border-color: #1e40af;
    transform: translateY(-2px);
}

.action-btn {
    transition: all 0.3s ease;
    padding: 12px;
    border-radius: 10px;
    font-weight: 500;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.list-group-item {
    border: none;
    padding: 1rem 0;
    transition: background-color 0.2s ease;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.list-group-item:first-child {
    padding-top: 0;
}

.list-group-item:last-child {
    padding-bottom: 0;
}

.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>