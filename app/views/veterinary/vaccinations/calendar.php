<?php
$current_page = 'vaccinations_calendar';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
           
            <div class="col-auto">
                <div class="btn-group">
                    <a href="<?php echo url('/vaccinations/create'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Record Vaccination
                    </a>
                    <a href="<?php echo url('/vaccinations'); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-list me-2"></i>List View
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>My Vaccination Schedule
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Calendar Filters -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="calendarView" class="form-label">Calendar View</label>
                            <select class="form-control" id="calendarView">
                                <option value="dayGridMonth">Month</option>
                                <option value="timeGridWeek">Week</option>
                                <option value="timeGridDay">Day</option>
                                <option value="listMonth">List</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="calendarStatusFilter" class="form-label">Filter by Status</label>
                            <select class="form-control" id="calendarStatusFilter">
                                <option value="">All Statuses</option>
                                <option value="scheduled">Scheduled</option>
                                <option value="completed">Completed</option>
                                <option value="overdue">Overdue</option>
                            </select>
                        </div>
                    </div>

                    <!-- Calendar -->
                    <div id="vaccinationCalendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include FullCalendar -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>

<style>
.fc-event {
    cursor: pointer;
    border: none;
    padding: 2px 4px;
    font-size: 0.85em;
}
.fc-event.scheduled {
    background-color: #ffc107;
    border-color: #ffc107;
}
.fc-event.completed {
    background-color: #28a745;
    border-color: #28a745;
}
.fc-event.overdue {
    background-color: #dc3545;
    border-color: #dc3545;
}
.fc-day-today {
    background-color: rgba(253, 116, 42, 0.1) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('vaccinationCalendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            // AJAX call to get events
            fetch(`<?php echo url('/api/vaccinations/calendar-events'); ?>?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`)
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(error => failureCallback(error));
        },
        eventClick: function(info) {
            if (info.event.id) {
                window.location.href = `<?php echo url('/vaccinations'); ?>/${info.event.id}`;
            }
        },
        eventDidMount: function(info) {
            // Add status-based styling
            const status = info.event.extendedProps.status;
            if (status) {
                info.el.classList.add(status);
            }
            
            // Add tooltip
            const tooltip = [
                info.event.title,
                `Status: ${info.event.extendedProps.status || 'N/A'}`,
                `Date: ${info.event.start.toLocaleDateString()}`,
                `Animal: ${info.event.extendedProps.animal_name || 'N/A'}`
            ].join('\n');
            
            info.el.setAttribute('title', tooltip);
        }
    });

    calendar.render();

    // Filter functionality
    const statusFilter = document.getElementById('calendarStatusFilter');
    const viewSelector = document.getElementById('calendarView');

    function applyFilters() {
        const status = statusFilter.value;

        calendar.getEvents().forEach(function(event) {
            let showEvent = true;

            if (status && event.extendedProps.status !== status) {
                showEvent = false;
            }

            event.setProp('display', showEvent ? 'auto' : 'none');
        });
    }

    statusFilter.addEventListener('change', applyFilters);

    // View change
    viewSelector.addEventListener('change', function() {
        calendar.changeView(this.value);
    });
});
</script>