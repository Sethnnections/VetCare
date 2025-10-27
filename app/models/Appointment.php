<?php
class Appointment extends Model {
    protected $table = 'appointments';
    protected $primaryKey = 'appointment_id';
    protected $fillable = [
        'animal_id', 'client_id', 'veterinary_id', 'appointment_date', 
        'appointment_time', 'duration', 'appointment_type', 'reason', 
        'status', 'notes', 'reminder_sent', 'created_by'
    ];
    
    // Business logic methods
    public function getAppointmentWithDetails($appointmentId) {
        $sql = "SELECT * FROM appointment_details_view WHERE appointment_id = :appointment_id";
        return fetchOne($sql, ['appointment_id' => $appointmentId]);
    }
    
    public function getAppointmentsByClient($clientId, $status = null) {
        $sql = "SELECT * FROM appointment_details_view WHERE client_id = :client_id";
        
        $params = ['client_id' => $clientId];
        
        if ($status) {
            $sql .= " AND status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY appointment_date DESC, appointment_time DESC";
        
        return fetchAll($sql, $params);
    }
    
    public function getAppointmentsByVeterinary($veterinaryId, $status = null) {
        $sql = "SELECT * FROM appointment_details_view WHERE veterinary_id = :veterinary_id";
        
        $params = ['veterinary_id' => $veterinaryId];
        
        if ($status) {
            $sql .= " AND status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY appointment_date ASC, appointment_time ASC";
        
        return fetchAll($sql, $params);
    }
    
    public function getUpcomingAppointments($days = 7) {
        $sql = "SELECT * FROM appointment_details_view 
                WHERE appointment_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
                AND status IN ('scheduled', 'confirmed')
                ORDER BY appointment_date ASC, appointment_time ASC";
        return fetchAll($sql, ['days' => $days]);
    }
    
    public function getTodayAppointments($veterinaryId = null) {
        $sql = "SELECT * FROM appointment_details_view 
                WHERE appointment_date = CURDATE() 
                AND status IN ('scheduled', 'confirmed')";
        
        $params = [];
        
        if ($veterinaryId) {
            $sql .= " AND veterinary_id = :veterinary_id";
            $params['veterinary_id'] = $veterinaryId;
        }
        
        $sql .= " ORDER BY appointment_time ASC";
        
        return fetchAll($sql, $params);
    }
    
    public function getAppointmentsByDateRange($startDate, $endDate, $veterinaryId = null) {
        $sql = "SELECT * FROM appointment_details_view 
                WHERE appointment_date BETWEEN :start_date AND :end_date";
        
        $params = ['start_date' => $startDate, 'end_date' => $endDate];
        
        if ($veterinaryId) {
            $sql .= " AND veterinary_id = :veterinary_id";
            $params['veterinary_id'] = $veterinaryId;
        }
        
        $sql .= " ORDER BY appointment_date ASC, appointment_time ASC";
        
        return fetchAll($sql, $params);
    }
    
    // Update this method in your Appointment model
    public function checkAvailability($veterinaryId, $date, $time, $duration = 30, $excludeAppointmentId = null) {
        // If no specific veterinary is selected, consider it available
        if (!$veterinaryId) {
            return true;
        }
        
        $sql = "SELECT COUNT(*) as conflict_count 
                FROM appointments 
                WHERE veterinary_id = :veterinary_id 
                AND appointment_date = :appointment_date 
                AND status IN ('scheduled', 'confirmed')
                AND (
                    (appointment_time <= :time AND DATE_ADD(appointment_time, INTERVAL duration MINUTE) > :time)
                    OR (appointment_time < DATE_ADD(:time, INTERVAL :duration MINUTE) AND DATE_ADD(appointment_time, INTERVAL duration MINUTE) >= DATE_ADD(:time, INTERVAL :duration MINUTE))
                    OR (appointment_time >= :time AND DATE_ADD(appointment_time, INTERVAL duration MINUTE) <= DATE_ADD(:time, INTERVAL :duration MINUTE))
                )";
        
        $params = [
            'veterinary_id' => $veterinaryId,
            'appointment_date' => $date,
            'time' => $time,
            'duration' => $duration
        ];
        
        if ($excludeAppointmentId) {
            $sql .= " AND appointment_id != :exclude_id";
            $params['exclude_id'] = $excludeAppointmentId;
        }
        
        try {
            $result = fetchOne($sql, $params);
            return $result['conflict_count'] == 0;
        } catch (Exception $e) {
            error_log("Availability check error: " . $e->getMessage());
            return true; // Default to available if there's an error
        }
    }
        
    // Update this method in your Appointment model
    public function getAvailableTimeSlots($veterinaryId, $date) {
        // Define working hours (9 AM to 5 PM)
        $startTime = '09:00:00';
        $endTime = '17:00:00';
        $slotDuration = 30; // minutes
        
        // If no specific veterinary is selected, we'll show all available slots
        // For now, we'll just generate slots without checking conflicts
        if (!$veterinaryId) {
            return $this->generateTimeSlots($date, $startTime, $endTime, $slotDuration);
        }
        
        // Get existing appointments for the day for this specific veterinary
        $existingAppointments = $this->getAppointmentsByDateRange($date, $date, $veterinaryId);
        
        return $this->generateTimeSlots($date, $startTime, $endTime, $slotDuration, $existingAppointments);
    }

    private function generateTimeSlots($date, $startTime, $endTime, $slotDuration, $existingAppointments = []) {
        $slots = [];
        $currentTime = strtotime($date . ' ' . $startTime);
        $endTimestamp = strtotime($date . ' ' . $endTime);
        
        // Check if it's Saturday (adjust end time to 1 PM)
        $dayOfWeek = date('N', strtotime($date));
        if ($dayOfWeek == 6) { // Saturday
            $endTimestamp = strtotime($date . ' 13:00:00');
        }
        
        // Check if it's Sunday (no slots)
        if ($dayOfWeek == 7) { // Sunday
            return $slots;
        }
        
        while ($currentTime < $endTimestamp) {
            $slotTime = date('H:i:s', $currentTime);
            $isAvailable = true;
            
            // Check if slot conflicts with existing appointments
            foreach ($existingAppointments as $appointment) {
                $appointmentStart = strtotime($date . ' ' . $appointment['appointment_time']);
                $appointmentEnd = strtotime($appointment['appointment_date'] . ' ' . $appointment['formatted_end_time']);
                
                if ($currentTime >= $appointmentStart && $currentTime < $appointmentEnd) {
                    $isAvailable = false;
                    break;
                }
            }
            
            if ($isAvailable) {
                $slots[] = [
                    'time' => $slotTime,
                    'formatted_time' => date('g:i A', $currentTime)
                ];
            }
            
            $currentTime += $slotDuration * 60;
        }
        
        return $slots;
    }
    
    public function createAppointment($appointmentData) {
        // Set default values
        $appointmentData['duration'] = $appointmentData['duration'] ?? 30;
        $appointmentData['status'] = $appointmentData['status'] ?? 'scheduled';
        $appointmentData['created_by'] = $appointmentData['created_by'] ?? $_SESSION['user_id'];
        
        return $this->create($appointmentData);
    }
    
    public function updateAppointmentStatus($appointmentId, $status) {
        return $this->update($appointmentId, ['status' => $status]);
    }
    
    public function getAppointmentStats() {
        $stats = [
            'total' => $this->count(),
            'scheduled' => $this->count(['status' => 'scheduled']),
            'confirmed' => $this->count(['status' => 'confirmed']),
            'completed' => $this->count(['status' => 'completed']),
            'cancelled' => $this->count(['status' => 'cancelled']),
            'today' => $this->countTodayAppointments()
        ];
        
        return $stats;
    }
    
    private function countTodayAppointments() {
        $sql = "SELECT COUNT(*) as count FROM appointments WHERE appointment_date = CURDATE() AND status IN ('scheduled', 'confirmed')";
        $result = fetchOne($sql);
        return $result['count'] ?? 0;
    }
    
    // Validation
    public function validate($data, $id = null) {
        $errors = [];
        
        // Required fields
        $required = ['animal_id', 'client_id', 'appointment_date', 'appointment_time', 'reason'];
        $errors = array_merge($errors, validateRequired($required, $data));
        
        // Validate date
        if (!empty($data['appointment_date'])) {
            $appointmentDate = strtotime($data['appointment_date']);
            if (!$appointmentDate || $appointmentDate < strtotime('today')) {
                $errors['appointment_date'] = 'Invalid appointment date';
            }
        }
        
        // Validate time
        if (!empty($data['appointment_time'])) {
            $appointmentTime = strtotime($data['appointment_time']);
            if (!$appointmentTime) {
                $errors['appointment_time'] = 'Invalid appointment time';
            }
        }
        
        // Check veterinary availability if provided
        if (!empty($data['veterinary_id']) && !empty($data['appointment_date']) && !empty($data['appointment_time'])) {
            $duration = $data['duration'] ?? 30;
            if (!$this->checkAvailability($data['veterinary_id'], $data['appointment_date'], $data['appointment_time'], $duration, $id)) {
                $errors['appointment_time'] = 'Selected time slot is not available for this veterinary';
            }
        }
        
        return $errors;
    }
}
?>