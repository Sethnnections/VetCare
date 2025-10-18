<?php
class AdminAnimalController extends Controller {
    private $animalModel;
    private $userModel;
    private $clientModel;
    private $treatmentModel;
    
    public function __construct() {
        $this->authorize(ROLE_ADMIN);
        $this->animalModel = new Animal();
        $this->userModel = new User();
        $this->clientModel = new Client();
        $this->treatmentModel = new Treatment();
    }
    
    // Admin animal management dashboard
        public function index() {
        try {
            $page = $this->get('page', 1);
            $search = $this->get('search');
            $species = $this->get('species');
            $status = $this->get('status');
            $veterinary = $this->get('veterinary');
            
            $filters = [];
            if ($status) $filters['status'] = $status;
            if ($species) $filters['species'] = $species;
            if ($veterinary) $filters['assigned_veterinary'] = $veterinary;
            
            $animals = $this->animalModel->getAllAnimalsWithDetails($filters);
            
            // Apply search filter
            if ($search && !empty($animals)) {
                $animals = array_filter($animals, function($animal) use ($search) {
                    return stripos($animal['name'], $search) !== false ||
                           stripos($animal['species'], $search) !== false ||
                           stripos($animal['breed'] ?? '', $search) !== false ||
                           stripos($animal['client_first_name'] . ' ' . $animal['client_last_name'], $search) !== false ||
                           stripos($animal['microchip'] ?? '', $search) !== false;
                });
            }
            
            $veterinarians = $this->userModel->getUsersByRole('veterinary');
            $speciesList = $this->animalModel->getSpeciesStats();
            
            $this->setTitle('Animal Management');
            $this->setData('animals', $animals);
            $this->setData('search', $search);
            $this->setData('species', $species);
            $this->setData('status', $status);
            $this->setData('veterinary', $veterinary);
            $this->setData('veterinarians', $veterinarians);
            $this->setData('speciesList', $speciesList);
            $this->setData('stats', $this->animalModel->getStats());
            $this->setData('workloadStats', $this->animalModel->getVeterinaryWorkload());
            $this->view('admin/animals/index', 'dashboard');
            
        } catch (Exception $e) {
            logError("Admin animals index error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while loading animal management');
            $this->redirect('/admin/dashboard');
        }
    }
    // Animal details with management options
    public function show($id) {
        $animal = $this->animalModel->getAnimalWithClient($id);
        
        if (!$animal) {
            $this->setFlash('error', 'Animal not found');
            $this->redirect('/admin/animals');
            return;
        }
        
        $treatments = $this->treatmentModel->getTreatmentsByAnimal($id);
        $vaccines = $this->animalModel->getAnimalVaccinations($id);
        $medications = $this->animalModel->getActiveMedications($id);
        $assignmentHistory = $this->animalModel->getAssignmentHistory($id);
        $veterinarians = $this->userModel->getUsersByRole('veterinary');
        
        $this->setTitle('Manage Animal: ' . $animal['name']);
        $this->setData('animal', $animal);
        $this->setData('treatments', $treatments);
        $this->setData('vaccines', $vaccines);
        $this->setData('medications', $medications);
        $this->setData('assignmentHistory', $assignmentHistory);
        $this->setData('veterinarians', $veterinarians);
        $this->view('admin/animals/show', 'dashboard');
    }
    
    // Assign animal to veterinary
    public function assignVeterinary($id) {
        if (!$this->isPost()) {
            $this->redirect('/admin/animals/' . $id);
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $veterinaryId = $this->input('veterinary_id');
            $assignedBy = $_SESSION['user_id'];
            
            if (empty($veterinaryId)) {
                $this->setFlash('error', 'Please select a veterinary');
                $this->redirect('/admin/animals/' . $id);
                return;
            }
            
            $animal = $this->animalModel->find($id);
            if (!$animal) {
                $this->setFlash('error', 'Animal not found');
                $this->redirect('/admin/animals');
                return;
            }
            
            // Assign veterinary
            $assigned = $this->animalModel->assignToVeterinary($id, $veterinaryId);
            
            if ($assigned) {
                // Log assignment
                $this->animalModel->logAssignment($id, $veterinaryId, $assignedBy, 'assigned');
                
                $veterinary = $this->userModel->find($veterinaryId);
                $vetName = $veterinary['first_name'] . ' ' . $veterinary['last_name'];
                
                logActivity("Animal '{$animal['name']}' assigned to veterinary '{$vetName}' by admin");
                $this->setFlash('success', 'Animal assigned to veterinary successfully');
            } else {
                $this->setFlash('error', 'Failed to assign animal to veterinary');
            }
            
            $this->redirect('/admin/animals/' . $id);
            
        } catch (Exception $e) {
            logError("Animal assignment error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while assigning animal');
            $this->redirect('/admin/animals/' . $id);
        }
    }
    
    // Unassign animal from veterinary
    public function unassignVeterinary($id) {
        if (!$this->isPost()) {
            $this->redirect('/admin/animals/' . $id);
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $animal = $this->animalModel->find($id);
            if (!$animal) {
                $this->setFlash('error', 'Animal not found');
                $this->redirect('/admin/animals');
                return;
            }
            
            // Log the unassignment before removing
            if ($animal['assigned_veterinary']) {
                $this->animalModel->logAssignment($id, $animal['assigned_veterinary'], $_SESSION['user_id'], 'unassigned');
            }
            
            $unassigned = $this->animalModel->unassignFromVeterinary($id);
            
            if ($unassigned) {
                logActivity("Animal '{$animal['name']}' unassigned from veterinary by admin");
                $this->setFlash('success', 'Animal unassigned successfully');
            } else {
                $this->setFlash('error', 'Failed to unassign animal');
            }
            
            $this->redirect('/admin/animals/' . $id);
            
        } catch (Exception $e) {
            logError("Animal unassignment error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while unassigning animal');
            $this->redirect('/admin/animals/' . $id);
        }
    }
    
    // Activate animal
    public function activate($id) {
        if (!$this->isPost()) {
            $this->redirect('/admin/animals');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $animal = $this->animalModel->find($id);
            if (!$animal) {
                $this->setFlash('error', 'Animal not found');
                $this->redirect('/admin/animals');
                return;
            }
            
            $activated = $this->animalModel->updateAnimalStatus($id, 'active');
            
            if ($activated) {
                logActivity("Animal '{$animal['name']}' activated by admin");
                $this->setFlash('success', 'Animal activated successfully');
            } else {
                $this->setFlash('error', 'Failed to activate animal');
            }
            
            $this->redirect('/admin/animals/' . $id);
            
        } catch (Exception $e) {
            logError("Animal activation error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while activating animal');
            $this->redirect('/admin/animals/' . $id);
        }
    }
    
    // Deactivate animal
    public function deactivate($id) {
        if (!$this->isPost()) {
            $this->redirect('/admin/animals');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $animal = $this->animalModel->find($id);
            if (!$animal) {
                $this->setFlash('error', 'Animal not found');
                $this->redirect('/admin/animals');
                return;
            }
            
            // Check for active treatments
            $activeTreatments = $this->treatmentModel->getActiveTreatmentsByAnimal($id);
            if (!empty($activeTreatments)) {
                $this->setFlash('error', 'Cannot deactivate animal with active treatments. Complete treatments first.');
                $this->redirect('/admin/animals/' . $id);
                return;
            }
            
            $deactivated = $this->animalModel->updateAnimalStatus($id, 'inactive');
            
            if ($deactivated) {
                logActivity("Animal '{$animal['name']}' deactivated by admin");
                $this->setFlash('success', 'Animal deactivated successfully');
            } else {
                $this->setFlash('error', 'Failed to deactivate animal');
            }
            
            $this->redirect('/admin/animals/' . $id);
            
        } catch (Exception $e) {
            logError("Animal deactivation error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while deactivating animal');
            $this->redirect('/admin/animals/' . $id);
        }
    }
    
    // View medication history
    public function medicationHistory($id) {
        $animal = $this->animalModel->getAnimalWithClient($id);
        
        if (!$animal) {
            $this->setFlash('error', 'Animal not found');
            $this->redirect('/admin/animals');
            return;
        }
        
        $medicationHistory = $this->animalModel->getMedicationHistory($id);
        $activeMedications = $this->animalModel->getActiveMedications($id);
        
        $this->setTitle('Medication History: ' . $animal['name']);
        $this->setData('animal', $animal);
        $this->setData('medicationHistory', $medicationHistory);
        $this->setData('activeMedications', $activeMedications);
        $this->view('admin/animals/medication-history', 'dashboard');
    }
    
    // Veterinary workload report
   public function veterinaryWorkload() {
        try {
            $workloadStats = $this->animalModel->getVeterinaryWorkload();
            $assignmentStats = $this->animalModel->getAssignmentStats();
            
            $this->setTitle('Veterinary Workload Report');
            $this->setData('workloadStats', $workloadStats);
            $this->setData('assignmentStats', $assignmentStats);
            $this->view('admin/animals/veterinary-workload', 'dashboard');
            
        } catch (Exception $e) {
            logError("Veterinary workload report error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while loading workload report');
            $this->redirect('/admin/animals');
        }
    }
    
    // Quick status toggle (AJAX)
    public function toggleStatus($id) {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        try {
            $animal = $this->animalModel->find($id);
            if (!$animal) {
                $this->json(['error' => 'Animal not found'], 404);
                return;
            }
            
            $newStatus = $animal['status'] == 'active' ? 'inactive' : 'active';
            
            // Check if we can deactivate
            if ($newStatus == 'inactive') {
                $activeTreatments = $this->treatmentModel->getActiveTreatmentsByAnimal($id);
                if (!empty($activeTreatments)) {
                    $this->json(['error' => 'Cannot deactivate animal with active treatments'], 400);
                    return;
                }
            }
            
            $updated = $this->animalModel->updateAnimalStatus($id, $newStatus);
            
            if ($updated) {
                logActivity("Animal '{$animal['name']}' status changed to {$newStatus} by admin");
                $this->json([
                    'success' => true,
                    'new_status' => $newStatus,
                    'status_text' => ucfirst($newStatus),
                    'status_class' => $newStatus == 'active' ? 'bg-success' : 'bg-warning'
                ]);
            } else {
                $this->json(['error' => 'Failed to update status'], 500);
            }
            
        } catch (Exception $e) {
            logError("Animal status toggle error: " . $e->getMessage());
            $this->json(['error' => 'An error occurred'], 500);
        }
    }
    
    // Quick assignment (AJAX)
    public function quickAssign($id) {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $veterinaryId = $this->input('veterinary_id');
            $animal = $this->animalModel->find($id);
            
            if (!$animal) {
                $this->json(['error' => 'Animal not found'], 404);
                return;
            }
            
            if (empty($veterinaryId)) {
                $this->json(['error' => 'Veterinary ID is required'], 400);
                return;
            }
            
            $assigned = $this->animalModel->assignToVeterinary($id, $veterinaryId);
            
            if ($assigned) {
                $this->animalModel->logAssignment($id, $veterinaryId, $_SESSION['user_id'], 'assigned');
                
                $veterinary = $this->userModel->find($veterinaryId);
                $vetName = $veterinary['first_name'] . ' ' . $veterinary['last_name'];
                
                logActivity("Animal '{$animal['name']}' assigned to veterinary '{$vetName}' by admin");
                $this->json([
                    'success' => true,
                    'message' => 'Animal assigned successfully',
                    'veterinary_name' => $vetName
                ]);
            } else {
                $this->json(['error' => 'Failed to assign animal'], 500);
            }
            
        } catch (Exception $e) {
            logError("Quick assignment error: " . $e->getMessage());
            $this->json(['error' => 'An error occurred'], 500);
        }
    }
}