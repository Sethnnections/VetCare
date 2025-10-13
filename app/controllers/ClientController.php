<?php
class ClientController extends Controller {
    private $clientModel;
    private $animalModel;
    
    public function __construct() {
        $this->clientModel = new Client();
        $this->animalModel = new Animal();
    }
    
    // List all clients
    public function index() {
        requireLogin();
        
        $page = $this->get('page', 1);
        $search = $this->get('search');
        
        if ($search) {
            $clients = $this->clientModel->searchClients($search);
        } else {
            $clients = $this->paginate($this->clientModel, $page);
        }
        
        $this->setTitle('Clients');
        $this->setData('clients', $clients);
        $this->setData('search', $search);
        $this->setData('stats', $this->clientModel->getStats());
        $this->view('clients/index');
    }
    
    // Show create client form
    public function create() {
        requireLogin();
        
        $this->setTitle('Add New Client');
        $this->view('clients/create');
    }
    
    // Store new client
    public function store() {
        requireLogin();
        
        if (!$this->isPost()) {
            $this->redirect('/clients/create');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $clientData = $this->input();
            $errors = $this->clientModel->validate($clientData);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('old', $clientData);
                $this->create();
                return;
            }
            
            $clientId = $this->clientModel->createClient($clientData);
            
            if ($clientId) {
                $this->setFlash('success', 'Client added successfully');
                $this->redirect('/clients/' . $clientId);
            } else {
                $this->setFlash('error', 'Failed to add client');
                $this->setData('old', $clientData);
                $this->create();
            }
            
        } catch (Exception $e) {
            logError("Client creation error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while adding client');
            $this->create();
        }
    }
    
    // Show client details
    public function show($id) {
        requireLogin();
        
        $client = $this->clientModel->find($id);
        
        if (!$client) {
            $this->setFlash('error', 'Client not found');
            $this->redirect('/clients');
            return;
        }
        
        $animals = $this->animalModel->getAnimalsByClient($id);
        $recentTreatments = $this->clientModel->getClientTreatments($id, 5);
        $billings = $this->clientModel->getClientBillings($id);
        
        $this->setTitle('Client: ' . $client['name']);
        $this->setData('client', $client);
        $this->setData('animals', $animals);
        $this->setData('recentTreatments', $recentTreatments);
        $this->setData('billings', $billings);
        $this->view('clients/show');
    }
    
    // Show edit client form
    public function edit($id) {
        requireLogin();
        
        $client = $this->clientModel->find($id);
        
        if (!$client) {
            $this->setFlash('error', 'Client not found');
            $this->redirect('/clients');
            return;
        }
        
        $this->setTitle('Edit Client: ' . $client['name']);
        $this->setData('client', $client);
        $this->view('clients/edit');
    }
    
    // Update client
    public function update($id) {
        requireLogin();
        
        if (!$this->isPost()) {
            $this->redirect('/clients/' . $id . '/edit');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $clientData = $this->input();
            $errors = $this->clientModel->validate($clientData, $id);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Please fix the errors below');
                $this->setData('errors', $errors);
                $this->setData('client', array_merge(['client_id' => $id], $clientData));
                $this->edit($id);
                return;
            }
            
            $updated = $this->clientModel->updateClient($id, $clientData);
            
            if ($updated) {
                $this->setFlash('success', 'Client updated successfully');
                $this->redirect('/clients/' . $id);
            } else {
                $this->setFlash('error', 'Failed to update client');
                $this->setData('client', array_merge(['client_id' => $id], $clientData));
                $this->edit($id);
            }
            
        } catch (Exception $e) {
            logError("Client update error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while updating client');
            $this->edit($id);
        }
    }
    
    // Delete client (soft delete)
    public function delete($id) {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        if (!$this->isPost()) {
            $this->redirect('/clients');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $client = $this->clientModel->find($id);
            
            if (!$client) {
                $this->setFlash('error', 'Client not found');
                $this->redirect('/clients');
                return;
            }
            
            // Check if client has active animals
            $animals = $this->animalModel->getAnimalsByClient($id);
            $activeAnimals = array_filter($animals, function($animal) {
                return $animal['status'] == STATUS_ACTIVE;
            });
            
            if (!empty($activeAnimals)) {
                $this->setFlash('error', 'Cannot delete client with active animals. Please transfer or deactivate animals first.');
                $this->redirect('/clients/' . $id);
                return;
            }
            
            $deleted = $this->clientModel->deactivateClient($id);
            
            if ($deleted) {
                $this->setFlash('success', 'Client deactivated successfully');
            } else {
                $this->setFlash('error', 'Failed to deactivate client');
            }
            
            $this->redirect('/clients');
            
        } catch (Exception $e) {
            logError("Client deletion error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while deleting client');
            $this->redirect('/clients');
        }
    }
    
    // Reactivate client
    public function activate($id) {
        requireLogin();
        $this->authorize(ROLE_ADMIN);
        
        if (!$this->isPost()) {
            $this->redirect('/clients');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $activated = $this->clientModel->activateClient($id);
            
            if ($activated) {
                $this->setFlash('success', 'Client activated successfully');
            } else {
                $this->setFlash('error', 'Failed to activate client');
            }
            
            $this->redirect('/clients');
            
        } catch (Exception $e) {
            logError("Client activation error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while activating client');
            $this->redirect('/clients');
        }
    }
    
    // AJAX client search for autocomplete
    public function search() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $term = $this->get('term');
        $clients = [];
        
        if (!empty($term)) {
            $clients = $this->clientModel->searchClients($term);
        }
        
        $this->json($clients);
    }
    
    // Get client statistics (AJAX)
    public function stats() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $stats = $this->clientModel->getStats();
        $this->json($stats);
    }
}
?>