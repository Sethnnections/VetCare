<?php
class AppointmentController extends Controller {
    private $appointmentModel;
    private $animalModel;
    private $clientModel;
    private $userModel;
    
    public function __construct() {
        $this->appointmentModel = new Appointment();
        $this->animalModel = new Animal();
        $this->clientModel = new Client();
        $this->userModel = new User();
    }
    
    // List appointments based on user role
    public function index() {
        requireLogin();
        
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['user_id'];
        
        switch ($userRole) {
            case ROLE_ADMIN:
                $appointments = $this->appointmentModel->getUpcomingAppointments(30);
                break;
                
            case ROLE_VETERINARY:
                $appointments = $this->appointmentModel->getAppointmentsByVeterinary($userId);
                break;
                
            case ROLE_CLIENT:
                $client = $this->clientModel->getClientByUserId($userId);
                if ($client) {
                    $appointments = $this->appointmentModel->getAppointmentsByClient($client['client_id']);
                } else {
                    $appointments = [];
                }
                break;
                
            default:
                $appointments = [];
        }
        
        $this->setTitle('Appointments');
        $this->setData('appointments', $appointments);
        $this->setData('stats', $this->appointmentModel->getAppointmentStats());
        $this->view('appointments/index', 'dashboard');
    }
    
    // Show calendar view
    public function calendar() {
        requireLogin();
        
        $startDate = $this->get('start', date('Y-m-01'));
        $endDate = $this->get('end', date('Y-m-t'));
        $veterinaryId = $this->get('veterinary_id');
        
        $appointments = $this->appointmentModel->getAppointmentsByDateRange($startDate, $endDate, $veterinaryId);
        
        // Format for FullCalendar
        $calendarEvents = [];
        foreach ($appointments as $appointment) {
            $calendarEvents[] = [
                'id' => $appointment['appointment_id'],
                'title' => $appointment['animal_name'] . ' - ' . $appointment['appointment_type'],
                'start' => $appointment['appointment_date'] . 'T' . $appointment['appointment_time'],
                'end' => $appointment['appointment_date'] . 'T' . $appointment['formatted_end_time'],
                'className' => 'appointment-' . $appointment['status'],
                'extendedProps' => [
                    'type' => $appointment['appointment_type'],
                    'status' => $appointment['status'],
                    'client' => $appointment['client_full_name'],
                    'veterinary' => $appointment['vet_full_name'] ?? 'Unassigned'
                ]
            ];
        }
        
        $veterinarians = $this->userModel->getUsersByRole(ROLE_VETERINARY);
        
        $this->setTitle('Appointment Calendar');
        $this->setData('calendarEvents', json_encode($calendarEvents));
        $this->setData('veterinarians', $veterinarians);
        $this->setData('selectedVeterinary', $veterinaryId);
        $this->view('appointments/calendar', 'dashboard');
    }
    
    // Show create appointment form
    public function create() {
        requireLogin();
        
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['user_id'];
        
        // Get available animals based on user role
        if ($userRole === ROLE_CLIENT) {
            $client = $this->clientModel->getClientByUserId($userId);
            $animals = $client ? $this->animalModel->getAnimalsByClient($client['client_id']) : [];
        } else {
            $animals = $this->animalModel->getAllAnimalsWithDetails();
        }
        
        $veterinarians = $this->userModel->getUsersByRole(ROLE_VETERINARY);
        $clients = $this->clientModel->getActiveClients();
        
        $this->setTitle('Schedule Appointment');
        $this->setData('animals', $animals);
        $this->setData('veterinarians', $veterinarians);
        $this->setData('clients', $clients);
        $this->view('appointments/create', 'dashboard');
    }
    
    // Store new appointment
    public function store() {
        requireLogin();
        
        if (!$this->isPost()) {
            $this->redirect('/appointments/create');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $appointmentData = $this->input();
            $appointmentData['created_by'] = $_SESSION['user_id'];
            
            // For clients, automatically set client_id
            if ($_SESSION['role'] === ROLE_CLIENT) {
                $client = $this->clientModel->getClientByUserId($_SESSION['user_id']);
                if ($client) {
                    $appointmentData['client_id'] = $client['client_id'];
                }
            }
            
            $errors = $this->appointmentModel->validate($appointmentData);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('old', $appointmentData);
                $this->create();
                return;
            }
            
            $appointmentId = $this->appointmentModel->createAppointment($appointmentData);
            
            if ($appointmentId) {
                logActivity("Appointment scheduled: ID {$appointmentId}", $_SESSION['user_id']);
                $this->setFlash('success', 'Appointment scheduled successfully');
                $this->redirect('/appointments');
            } else {
                $this->setFlash('error', 'Failed to schedule appointment');
                $this->setData('old', $appointmentData);
                $this->create();
            }
            
        } catch (Exception $e) {
            logError("Appointment creation error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while scheduling appointment');
            $this->create();
        }
    }
    
    // Show appointment details
    public function show($id) {
        requireLogin();
        
        $appointment = $this->appointmentModel->getAppointmentWithDetails($id);
        
        if (!$appointment) {
            $this->setFlash('error', 'Appointment not found');
            $this->redirect('/appointments');
            return;
        }
        
        // Check permissions
        if (!$this->canViewAppointment($appointment)) {
            $this->setFlash('error', 'Access denied');
            $this->redirect('/appointments');
            return;
        }
        
        $this->setTitle('Appointment Details');
        $this->setData('appointment', $appointment);
        $this->view('appointments/show', 'dashboard');
    }
    
    // Update appointment status (AJAX)
    public function updateStatus($id) {
        requireLogin();
        
        if (!$this->isPost() || !$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $status = $this->input('status');
            $appointment = $this->appointmentModel->find($id);
            
            if (!$appointment) {
                $this->json(['error' => 'Appointment not found'], 404);
                return;
            }
            
            if (!$this->canModifyAppointment($appointment)) {
                $this->json(['error' => 'Access denied'], 403);
                return;
            }
            
            $updated = $this->appointmentModel->updateAppointmentStatus($id, $status);
            
            if ($updated) {
                logActivity("Appointment status updated: ID {$id} to {$status}", $_SESSION['user_id']);
                $this->json(['success' => true, 'message' => 'Appointment status updated']);
            } else {
                $this->json(['error' => 'Failed to update appointment status']);
            }
            
        } catch (Exception $e) {
            logError("Appointment status update error: " . $e->getMessage());
            $this->json(['error' => 'An error occurred while updating appointment status']);
        }
    }
    
    // Get available time slots (AJAX)
        // Update this method in your AppointmentController
public function getTimeSlots() {
    if (!$this->isAjax()) {
        $this->json(['error' => 'Invalid request'], 400);
        return;
    }
    
    try {
        $veterinaryId = $this->get('veterinary_id');
        $date = $this->get('date');
        
        if (!$date) {
            $this->json(['error' => 'Date is required'], 400);
            return;
        }
        
        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->json(['error' => 'Invalid date format'], 400);
            return;
        }
        
        // Check if date is in the past
        if (strtotime($date) < strtotime('today')) {
            $this->json(['error' => 'Cannot schedule appointments in the past'], 400);
            return;
        }
        
        $slots = $this->appointmentModel->getAvailableTimeSlots($veterinaryId, $date);
        
        $this->json([
            'success' => true, 
            'slots' => $slots,
            'date' => $date,
            'veterinary_id' => $veterinaryId
        ]);
        
    } catch (Exception $e) {
        error_log("Time slots error: " . $e->getMessage());
        $this->json([
            'success' => false, 
            'error' => 'Failed to load time slots',
            'debug' => DEBUG_MODE ? $e->getMessage() : null
        ], 500);
    }
}
    
    // Check availability (AJAX)
    public function checkAvailability() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $veterinaryId = $this->get('veterinary_id');
        $date = $this->get('date');
        $time = $this->get('time');
        $duration = $this->get('duration', 30);
        
        if (!$veterinaryId || !$date || !$time) {
            $this->json(['error' => 'Required parameters missing'], 400);
            return;
        }
        
        $isAvailable = $this->appointmentModel->checkAvailability($veterinaryId, $date, $time, $duration);
        $this->json(['success' => true, 'available' => $isAvailable]);
    }
    
    // Permission checks
    private function canViewAppointment($appointment) {
        $userRole = $_SESSION['role'];
        $userId = $_SESSION['user_id'];
        
        switch ($userRole) {
            case ROLE_ADMIN:
                return true;
                
            case ROLE_VETERINARY:
                return $appointment['veterinary_id'] == $userId;
                
            case ROLE_CLIENT:
                $client = $this->clientModel->getClientByUserId($userId);
                return $client && $appointment['client_id'] == $client['client_id'];
                
            default:
                return false;
        }
    }
    
    private function canModifyAppointment($appointment) {
        $userRole = $_SESSION['role'];
        
        if ($userRole === ROLE_ADMIN) {
            return true;
        }
        
        if ($userRole === ROLE_VETERINARY && $appointment['veterinary_id'] == $_SESSION['user_id']) {
            return true;
        }
        
        return false;
    }

    // Book appointment form for clients
    public function book() {
        requireLogin();
        $this->authorize([ROLE_CLIENT]);
        
        $userId = $_SESSION['user_id'];
        $client = $this->clientModel->getClientByUserId($userId);
        
        if (!$client) {
            $this->setFlash('error', 'Please complete your profile first');
            $this->redirect('/client/profile/create');
            return;
        }
        
        $animals = $this->animalModel->getAnimalsByClient($client['client_id']);
        $veterinarians = $this->userModel->getUsersByRole(ROLE_VETERINARY);
        
        $this->setTitle('Book Appointment');
        $this->setData('animals', $animals);
        $this->setData('veterinarians', $veterinarians);
        $this->setData('client', $client);
        $this->view('appointments/book', 'dashboard');
    }

    // Show today's appointments
    public function today() {
        requireLogin();
        $this->authorize([ROLE_ADMIN, ROLE_VETERINARY]);
        
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['role'];
        
        if ($userRole === ROLE_VETERINARY) {
            $appointments = $this->appointmentModel->getTodayAppointments($userId);
        } else {
            $appointments = $this->appointmentModel->getTodayAppointments();
        }
        
        $this->setTitle("Today's Appointments");
        $this->setData('appointments', $appointments);
        $this->view('appointments/today', 'dashboard');
    }

    // Appointment reports for admin
    public function reports() {
        requireLogin();
        $this->authorize([ROLE_ADMIN]);
        
        $startDate = $this->get('start_date', date('Y-m-01'));
        $endDate = $this->get('end_date', date('Y-m-t'));
        $veterinaryId = $this->get('veterinary_id');
        
        $appointments = $this->appointmentModel->getAppointmentsByDateRange($startDate, $endDate, $veterinaryId);
        $veterinarians = $this->userModel->getUsersByRole(ROLE_VETERINARY);
        
        // Calculate report statistics
        $stats = $this->calculateAppointmentStats($appointments);
        
        $this->setTitle('Appointment Reports');
        $this->setData('appointments', $appointments);
        $this->setData('veterinarians', $veterinarians);
        $this->setData('stats', $stats);
        $this->setData('startDate', $startDate);
        $this->setData('endDate', $endDate);
        $this->setData('selectedVeterinary', $veterinaryId);
        $this->view('appointments/reports', 'dashboard');
    }

    private function calculateAppointmentStats($appointments) {
        $stats = [
            'total' => count($appointments),
            'by_type' => [],
            'by_status' => [],
            'by_veterinary' => [],
            'daily_average' => 0
        ];
        
        $uniqueDays = [];
        
        foreach ($appointments as $appointment) {
            // Count by type
            $type = $appointment['appointment_type'];
            $stats['by_type'][$type] = ($stats['by_type'][$type] ?? 0) + 1;
            
            // Count by status
            $status = $appointment['status'];
            $stats['by_status'][$status] = ($stats['by_status'][$status] ?? 0) + 1;
            
            // Count by veterinary
            $vetName = $appointment['vet_full_name'] ?? 'Unassigned';
            $stats['by_veterinary'][$vetName] = ($stats['by_veterinary'][$vetName] ?? 0) + 1;
            
            // Track unique days for average calculation
            $uniqueDays[$appointment['appointment_date']] = true;
        }
        
        // Calculate daily average
        if (count($uniqueDays) > 0) {
            $stats['daily_average'] = round($stats['total'] / count($uniqueDays), 2);
        }
        
        return $stats;
    }
}
?>