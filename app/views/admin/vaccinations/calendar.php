<?php
$current_page = 'admin_vaccinations_calendar';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
          
            <div class="col-auto">
                <div class="btn-group">
                    <a href="<?php echo url('/vaccinations'); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i>List View
                    </a>
                    <a href="<?php echo url('/vaccinations/create'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Record Vaccination
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card dashboard-card stat-card primary">
                <div class="card-body text-center py-3">
                    <h3 class="stat-value mb-1"><?php echo $stats['total_vaccinations'] ?? 0; ?></h3>
                    <p class="stat-label mb-0">Total</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card dashboard-card stat-card success">
                <div class="card-body text-center py-3">
                    <h3 class="stat-value mb-1"><?php echo $stats['completed_vaccinations'] ?? 0; ?></h3>
                    <p class="stat-label mb-0">Completed</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card dashboard-card stat-card warning">
                <div class="card-body text-center py-3">
                    <h3 class="stat-value mb-1"><?php echo $stats['scheduled_vaccinations'] ?? 0; ?></h3>
                    <p class="stat-label mb-0">Scheduled</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card dashboard-card stat-card danger">
                <div class="card-body text-center py-3">
                    <h3 class="stat-value mb-1"><?php echo $stats['overdue_vaccinations'] ?? 0; ?></h3>
                    <p class="stat-label mb-0">Overdue</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card dashboard-card stat-card info">
                <div class="card-body text-center py-3">
                    <h3 class="stat-value mb-1"><?php echo $stats['animals_vaccinated'] ?? 0; ?></h3>
                    <p class="stat-label mb-0">Animals</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card dashboard-card stat-card secondary">
                <div class="card-body text-center py-3">
                    <h3 class="stat-value mb-1"><?php echo $stats['upcoming_vaccinations'] ?? 0; ?></h3>
                    <p class="stat-label mb-0">This Month</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div class="card dashboard-card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-calendar me-2"></i>Vaccination Schedule
            </h5>
        </div>
        <div class="card-body">
            <div id="vaccinationCalendar"></div>
        </div>
    </div>
</div>

<style>
.fc-event {
    cursor: pointer;
    border: none;
    padding: 2px 4px;
    font-size: 0.85em;
}
.fc-day-today {
    background-color: rgba(253, 116, 42, 0.1) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('vaccinationCalendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: [ 'dayGrid', 'timeGrid', 'interaction' ],
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        defaultView: 'dayGridMonth',
        navLinks: true,
        editable: false,
        eventLimit: true,
        events: {
            url: '<?php echo url('/api/vaccinations/calendar-events'); ?>',
            failure: function() {
                alert('There was an error while fetching events!');
            }
        },
        eventClick: function(info) {
            if (info.event.extendedProps.type === 'vaccination') {
                window.location.href = '<?php echo url('/vaccinations'); ?>/' + info.event.id;
            }
        },
        eventRender: function(info) {
            // Add custom styling based on event type
            if (info.event.extendedProps.type === 'vaccination') {
                info.el.title = info.event.extendedProps.description;
            }
        },
        loading: function(bool) {
            if (bool) {
                $('#loading').show();
            } else {
                $('#loading').hide();
            }
        }
    });

    calendar.render();
});
</script>