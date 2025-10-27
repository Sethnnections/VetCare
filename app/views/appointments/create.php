<?php
// app/views/appointments/create.php
$current_page = 'appointments';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
           
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-plus me-2"></i>Schedule New Appointment
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

                    <form id="appointmentForm" method="POST" action="<?php echo url('/appointments/store'); ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="row">
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="client_id" class="form-label">Client *</label>
                                    <select class="form-control" id="client_id" name="client_id" required>
                                        <option value="">Select Client</option>
                                        <?php foreach ($clients as $client): ?>
                                            <option value="<?php echo $client['client_id']; ?>" 
                                                <?php echo (isset($old['client_id']) && $old['client_id'] == $client['client_id']) ? 'selected' : ''; ?>>
                                                <?php echo $client['first_name'] . ' ' . $client['last_name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['client_id'])): ?>
                                        <div class="text-danger small"><?php echo $errors['client_id']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="animal_id" class="form-label">Animal *</label>
                                    <select class="form-control" id="animal_id" name="animal_id" required>
                                        <option value="">Select Animal</option>
                                        <?php foreach ($animals as $animal): ?>
                                            <option value="<?php echo $animal['animal_id']; ?>"
                                                <?php echo (isset($old['animal_id']) && $old['animal_id'] == $animal['animal_id']) ? 'selected' : ''; ?>>
                                                <?php echo $animal['name']; ?> (<?php echo $animal['species']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['animal_id'])): ?>
                                        <div class="text-danger small"><?php echo $errors['animal_id']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="veterinary_id" class="form-label">Veterinary</label>
                                    <select class="form-control" id="veterinary_id" name="veterinary_id">
                                        <option value="">Any Available</option>
                                        <?php foreach ($veterinarians as $vet): ?>
                                            <option value="<?php echo $vet['user_id']; ?>"
                                                <?php echo (isset($old['veterinary_id']) && $old['veterinary_id'] == $vet['user_id']) ? 'selected' : ''; ?>>
                                                <?php echo $vet['first_name'] . ' ' . $vet['last_name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="appointment_type" class="form-label">Appointment Type *</label>
                                    <select class="form-control" id="appointment_type" name="appointment_type" required>
                                        <option value="consultation" <?php echo (isset($old['appointment_type']) && $old['appointment_type'] == 'consultation') ? 'selected' : ''; ?>>Consultation</option>
                                        <option value="vaccination" <?php echo (isset($old['appointment_type']) && $old['appointment_type'] == 'vaccination') ? 'selected' : ''; ?>>Vaccination</option>
                                        <option value="checkup" <?php echo (isset($old['appointment_type']) && $old['appointment_type'] == 'checkup') ? 'selected' : ''; ?>>Routine Checkup</option>
                                        <option value="grooming" <?php echo (isset($old['appointment_type']) && $old['appointment_type'] == 'grooming') ? 'selected' : ''; ?>>Grooming</option>
                                        <option value="surgery" <?php echo (isset($old['appointment_type']) && $old['appointment_type'] == 'surgery') ? 'selected' : ''; ?>>Surgery</option>
                                        <option value="emergency" <?php echo (isset($old['appointment_type']) && $old['appointment_type'] == 'emergency') ? 'selected' : ''; ?>>Emergency</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="appointment_date" class="form-label">Date *</label>
                                    <input type="date" class="form-control" id="appointment_date" name="appointment_date" 
                                           min="<?php echo date('Y-m-d'); ?>" 
                                           value="<?php echo $old['appointment_date'] ?? ''; ?>" required>
                                    <?php if (isset($errors['appointment_date'])): ?>
                                        <div class="text-danger small"><?php echo $errors['appointment_date']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="appointment_time" class="form-label">Time *</label>
                                    <select class="form-control" id="appointment_time" name="appointment_time" required>
                                        <option value="">Select Time</option>
                                        <!-- Time slots will be populated by JavaScript -->
                                    </select>
                                    <?php if (isset($errors['appointment_time'])): ?>
                                        <div class="text-danger small"><?php echo $errors['appointment_time']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration" class="form-label">Duration (minutes)</label>
                                    <select class="form-control" id="duration" name="duration">
                                        <option value="30" <?php echo (isset($old['duration']) && $old['duration'] == 30) ? 'selected' : ''; ?>>30 minutes</option>
                                        <option value="45" <?php echo (isset($old['duration']) && $old['duration'] == 45) ? 'selected' : ''; ?>>45 minutes</option>
                                        <option value="60" <?php echo (isset($old['duration']) && $old['duration'] == 60) ? 'selected' : ''; ?>>60 minutes</option>
                                        <option value="90" <?php echo (isset($old['duration']) && $old['duration'] == 90) ? 'selected' : ''; ?>>90 minutes</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason for Visit *</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" 
                                      placeholder="Please describe the reason for this appointment..." required><?php echo $old['reason'] ?? ''; ?></textarea>
                            <?php if (isset($errors['reason'])): ?>
                                <div class="text-danger small"><?php echo $errors['reason']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" 
                                      placeholder="Any additional information..."><?php echo $old['notes'] ?? ''; ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo url('/appointments'); ?>" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Schedule Appointment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Appointment Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-clock me-2"></i>Clinic Hours</h6>
                        <p class="mb-1">Monday - Friday: 9:00 AM - 5:00 PM</p>
                        <p class="mb-1">Saturday: 9:00 AM - 1:00 PM</p>
                        <p class="mb-0">Sunday: Closed</p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Important Notes</h6>
                        <ul class="mb-0 ps-3">
                            <li>Please arrive 10 minutes before your appointment</li>
                            <li>Cancellations require 24 hours notice</li>
                            <li>Emergency cases will be prioritized</li>
                            <li>Bring any previous medical records</li>
                        </ul>
                    </div>

                    <div id="availabilityInfo" class="alert alert-success d-none">
                        <h6><i class="fas fa-check-circle me-2"></i>Availability</h6>
                        <p class="mb-0" id="availabilityText">Selected time slot is available</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');
    const veterinarySelect = document.getElementById('veterinary_id');
    const durationSelect = document.getElementById('duration');
    const availabilityInfo = document.getElementById('availabilityInfo');
    const availabilityText = document.getElementById('availabilityText');

    // Load time slots when date or veterinary changes
    function loadTimeSlots() {
        const date = dateInput.value;
        const veterinaryId = veterinarySelect.value;

        if (!date) {
            timeSelect.innerHTML = '<option value="">Select Date First</option>';
            availabilityInfo.classList.add('d-none');
            return;
        }

        timeSelect.innerHTML = '<option value="">Loading available slots...</option>';
        timeSelect.disabled = true;
        availabilityInfo.classList.add('d-none');

        fetch(`<?php echo url('/api/appointments/time-slots'); ?>?veterinary_id=${veterinaryId}&date=${date}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                timeSelect.disabled = false;
                
                if (data.success) {
                    timeSelect.innerHTML = '<option value="">Select Time</option>';
                    
                    if (data.slots && data.slots.length > 0) {
                        data.slots.forEach(slot => {
                            const option = document.createElement('option');
                            option.value = slot.time;
                            option.textContent = slot.formatted_time;
                            timeSelect.appendChild(option);
                        });
                    } else {
                        timeSelect.innerHTML = '<option value="">No available slots</option>';
                        availabilityInfo.classList.remove('d-none');
                        availabilityInfo.classList.remove('alert-success');
                        availabilityInfo.classList.add('alert-warning');
                        availabilityText.textContent = 'No available time slots for selected date' + (veterinaryId ? ' and veterinary' : '');
                    }
                } else {
                    timeSelect.innerHTML = '<option value="">Error loading slots</option>';
                    console.error('Server error:', data.error);
                }
            })
            .catch(error => {
                console.error('Error loading time slots:', error);
                timeSelect.disabled = false;
                timeSelect.innerHTML = '<option value="">Error loading slots</option>';
                
                // Fallback: generate basic time slots
                generateFallbackSlots(date);
            });
    }

    // Fallback function to generate basic time slots
    function generateFallbackSlots(date) {
        const dayOfWeek = new Date(date).getDay();
        
        // No slots on Sunday (0) or if invalid date
        if (dayOfWeek === 0 || isNaN(dayOfWeek)) {
            timeSelect.innerHTML = '<option value="">No slots available</option>';
            return;
        }
        
        // Saturday: 9 AM - 1 PM
        const isSaturday = dayOfWeek === 6;
        const startHour = 9;
        const endHour = isSaturday ? 13 : 17;
        
        timeSelect.innerHTML = '<option value="">Select Time</option>';
        
        for (let hour = startHour; hour < endHour; hour++) {
            for (let minute = 0; minute < 60; minute += 30) {
                const timeString = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}:00`;
                const displayTime = `${hour > 12 ? hour - 12 : hour}:${minute.toString().padStart(2, '0')} ${hour >= 12 ? 'PM' : 'AM'}`;
                
                const option = document.createElement('option');
                option.value = timeString;
                option.textContent = displayTime;
                timeSelect.appendChild(option);
            }
        }
        
        availabilityInfo.classList.remove('d-none');
        availabilityInfo.classList.remove('alert-success', 'alert-warning');
        availabilityInfo.classList.add('alert-info');
        availabilityText.textContent = 'Showing basic time slots (availability not verified)';
    }

    // Check availability when time is selected
    function checkAvailability() {
        const date = dateInput.value;
        const time = timeSelect.value;
        const veterinaryId = veterinarySelect.value;
        const duration = durationSelect.value;

        if (!date || !time || !veterinaryId) {
            availabilityInfo.classList.add('d-none');
            return;
        }

        fetch(`<?php echo url('/api/appointments/check-availability'); ?>?veterinary_id=${veterinaryId}&date=${date}&time=${time}&duration=${duration}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    availabilityInfo.classList.remove('d-none');
                    if (data.available) {
                        availabilityInfo.classList.remove('alert-warning', 'alert-info');
                        availabilityInfo.classList.add('alert-success');
                        availabilityText.textContent = 'Selected time slot is available';
                    } else {
                        availabilityInfo.classList.remove('alert-success', 'alert-info');
                        availabilityInfo.classList.add('alert-warning');
                        availabilityText.textContent = 'Selected time slot is not available';
                    }
                }
            })
            .catch(error => {
                console.error('Error checking availability:', error);
            });
    }

    dateInput.addEventListener('change', loadTimeSlots);
    veterinarySelect.addEventListener('change', loadTimeSlots);
    timeSelect.addEventListener('change', checkAvailability);
    durationSelect.addEventListener('change', checkAvailability);

    // Load time slots if date is already set (form validation failed)
    if (dateInput.value) {
        loadTimeSlots();
    }
});
</script>