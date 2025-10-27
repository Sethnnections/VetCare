<?php
// app/views/appointments/calendar.php
$current_page = 'appointments';
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
        
            <div class="col-auto">
                <div class="row g-2 align-items-center">
                    <?php if (in_array($_SESSION['role'], ['admin', 'veterinary'])): ?>
                    <div class="col-auto">
                        <select class="form-select" id="veterinaryFilter" onchange="filterCalendar()">
                            <option value="">All Veterinarians</option>
                            <?php foreach ($veterinarians as $vet): ?>
                                <option value="<?php echo $vet['user_id']; ?>" 
                                        <?php echo ($selectedVeterinary == $vet['user_id']) ? 'selected' : ''; ?>>
                                    <?php echo $vet['first_name'] . ' ' . $vet['last_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div class="col-auto">
                        <a href="<?php echo url('/appointments/create'); ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Schedule Appointment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div class="dashboard-card">
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<style>
.fc-event {
    cursor: pointer;
    border: none;
    font-size: 0.85em;
    padding: 2px 4px;
}

.appointment-scheduled {
    background-color: #ffc107;
    border-color: #ffc107;
}

.appointment-confirmed {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.appointment-in_progress {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
}

.appointment-completed {
    background-color: #198754;
    border-color: #198754;
}

.appointment-cancelled {
    background-color: #dc3545;
    border-color: #dc3545;
}

.appointment-no_show {
    background-color: #6c757d;
    border-color: #6c757d;
}

.fc-toolbar {
    flex-wrap: wrap;
}

.fc-header-toolbar {
    margin-bottom: 1.5em !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        events: <?php echo $calendarEvents; ?>,
        eventClick: function(info) {
            window.location.href = `<?php echo url('/appointments/'); ?>${info.event.id}`;
        },
        eventDidMount: function(info) {
            // Add tooltip
            if (info.event.extendedProps) {
                const props = info.event.extendedProps;
                const tooltip = `
                    <strong>Type:</strong> ${props.type}<br>
                    <strong>Status:</strong> ${props.status}<br>
                    <strong>Client:</strong> ${props.client}<br>
                    <strong>Veterinary:</strong> ${props.veterinary}
                `;
                $(info.el).tooltip({
                    title: tooltip,
                    placement: 'top',
                    trigger: 'hover',
                    html: true
                });
            }
        },
        editable: false,
        selectable: false,
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5],
            startTime: '09:00',
            endTime: '17:00',
        },
        slotMinTime: '08:00',
        slotMaxTime: '18:00',
        allDaySlot: false,
        nowIndicator: true,
        navLinks: true,
        dayMaxEvents: true,
        views: {
            timeGrid: {
                dayMaxEvents: 6
            }
        },
        height: 'auto',
        contentHeight: 'auto'
    });
    
    calendar.render();
});

function filterCalendar() {
    const veterinaryId = document.getElementById('veterinaryFilter').value;
    const params = new URLSearchParams();
    if (veterinaryId) {
        params.append('veterinary_id', veterinaryId);
    }
    window.location.href = `<?php echo url('/appointments/calendar'); ?>?${params.toString()}`;
}
</script>