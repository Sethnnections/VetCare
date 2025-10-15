<?php
$routes = [
    // Home and Auth Routes
    '#^/$#' => [
        'controller' => 'Dashboard',
        'action' => 'index'
    ],
    '#^/dashboard$#' => [
        'controller' => 'Dashboard',
        'action' => 'index'
    ],
    '#^/login$#' => [
        'controller' => 'Auth',
        'action' => 'login'
    ],
    '#^/logout$#' => [
        'controller' => 'Auth',
        'action' => 'logout'
    ],
    '#^/register$#' => [
        'controller' => 'Auth',
        'action' => 'register'
    ],
    
    // Client Routes
    '#^/client/profile$#' => [
        'controller' => 'ClientProfile',
        'action' => 'profile'
    ],
    '#^/client/profile/update$#' => [
        'controller' => 'ClientProfile',
        'action' => 'updateProfile'
    ],
    '#^/client/animals$#' => [
        'controller' => 'ClientProfile',
        'action' => 'animals'
    ],
    '#^/client/animals/add$#' => [
        'controller' => 'ClientProfile',
        'action' => 'addAnimal'
    ],
    '#^/client/animals/([0-9]+)/edit$#' => [
        'controller' => 'ClientProfile',
        'action' => 'editAnimal'
    ],
    '#^/client/animals/([0-9]+)/delete$#' => [
        'controller' => 'ClientProfile',
        'action' => 'deleteAnimal'
    ],
    '#^/client/animals/([0-9]+)$#' => [
        'controller' => 'ClientProfile',
        'action' => 'viewAnimal'
    ],
    
    // Veterinary Routes
    '#^/veterinary/dashboard$#' => [
        'controller' => 'Veterinary',
        'action' => 'dashboard'
    ],
    '#^/veterinary/treatments$#' => [
        'controller' => 'Veterinary',
        'action' => 'treatments'
    ],
    '#^/veterinary/vaccinations$#' => [
        'controller' => 'Veterinary',
        'action' => 'vaccinations'
    ],
    
    // Admin Routes
    '#^/admin/dashboard$#' => [
        'controller' => 'Admin',
        'action' => 'dashboard'
    ],
    '#^/admin/users$#' => [
        'controller' => 'Admin',
        'action' => 'users'
    ],
    '#^/admin/register-veterinary$#' => [
        'controller' => 'Admin',
        'action' => 'registerVeterinary'
    ],
    
    // Animal Routes (General)
    '#^/animals$#' => [
        'controller' => 'Animal',
        'action' => 'index'
    ],
    '#^/animals/create$#' => [
        'controller' => 'Animal',
        'action' => 'create'
    ],
    '#^/animals/([0-9]+)$#' => [
        'controller' => 'Animal',
        'action' => 'show'
    ],
    '#^/animals/([0-9]+)/edit$#' => [
        'controller' => 'Animal',
        'action' => 'edit'
    ],
    '#^/animals/([0-9]+)/delete$#' => [
        'controller' => 'Animal',
        'action' => 'delete'
    ],
    
    // Treatment Routes
    '#^/treatments$#' => [
        'controller' => 'Treatment',
        'action' => 'index'
    ],
    '#^/treatments/create$#' => [
        'controller' => 'Treatment',
        'action' => 'create'
    ],
    '#^/treatments/([0-9]+)$#' => [
        'controller' => 'Treatment',
        'action' => 'show'
    ],
    '#^/treatments/([0-9]+)/edit$#' => [
        'controller' => 'Treatment',
        'action' => 'edit'
    ],
    '#^/treatments/([0-9]+)/complete$#' => [
        'controller' => 'Treatment',
        'action' => 'complete'
    ],
    
    // Vaccine Routes
    '#^/vaccines$#' => [
        'controller' => 'Vaccine',
        'action' => 'index'
    ],
    '#^/vaccines/create$#' => [
        'controller' => 'Vaccine',
        'action' => 'create'
    ],
    '#^/vaccines/([0-9]+)$#' => [
        'controller' => 'Vaccine',
        'action' => 'show'
    ],
    '#^/vaccines/([0-9]+)/edit$#' => [
        'controller' => 'Vaccine',
        'action' => 'edit'
    ],
    '#^/vaccines/([0-9]+)/complete$#' => [
        'controller' => 'Vaccine',
        'action' => 'complete'
    ],
    
    // Client Management Routes (for admin/veterinary)
    '#^/clients$#' => [
        'controller' => 'Client',
        'action' => 'index'
    ],
    '#^/clients/create$#' => [
        'controller' => 'Client',
        'action' => 'create'
    ],
    '#^/clients/([0-9]+)$#' => [
        'controller' => 'Client',
        'action' => 'show'
    ],
    '#^/clients/([0-9]+)/edit$#' => [
        'controller' => 'Client',
        'action' => 'edit'
    ],
    '#^/clients/([0-9]+)/delete$#' => [
        'controller' => 'Client',
        'action' => 'delete'
    ],
    
    // API Routes for AJAX
    '#^/api/animals/search$#' => [
        'controller' => 'Animal',
        'action' => 'search'
    ],
    '#^/api/clients/search$#' => [
        'controller' => 'Client',
        'action' => 'search'
    ],
    '#^/api/treatments/stats$#' => [
        'controller' => 'Treatment',
        'action' => 'stats'
    ]
];
?>