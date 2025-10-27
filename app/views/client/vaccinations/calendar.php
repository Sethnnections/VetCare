<?php
$current_page = 'vaccinations_calendar';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
           
            <div class="col-auto">
                <a href="<?php echo url('/client/vaccinations'); ?>" class="btn btn-outline-primary">
                    <i class="fas fa-list me-2"></i>List View
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>My Animals' Vaccination Schedule
                    </h5>
                </div>
                <div class="card-body">
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
.fc-event.completed {
    background-color: #28a745;
    border-color: #28a745;
}
.fc-event.overdue {
    background-color: #dc3545;
    border-color: #dc3545;
}
.fc-event.scheduled {
    background-color: #ffc107;
    border-color: #ffc107;
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
            const status = info.event.extendedProps.status;
            if (status) {
                info.el.classList.add(status);
            }
            
            const tooltip = [
                info.event.title,
                `Status: ${info.event.extendedProps.status || 'N/A'}`,
                `Date: ${info.event.start.toLocaleDateString()}`,
                `Due: ${info.event.end ? info.event.end.toLocaleDateString() : 'N/A'}`
            ].join('\n');
            
            info.el.setAttribute('title', tooltip);
        }
    });

    calendar.render();
});
</script>