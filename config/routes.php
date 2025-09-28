<?php

$routes = [
    // Auth routes
    '#^auth/login$#' => ['controller' => 'Auth', 'action' => 'login'],
    '#^auth/authenticate$#' => ['controller' => 'Auth', 'action' => 'authenticate'],
    '#^auth/logout$#' => ['controller' => 'Auth', 'action' => 'logout'],
    '#^auth/register$#' => ['controller' => 'Auth', 'action' => 'register'],
    '#^auth/store$#' => ['controller' => 'Auth', 'action' => 'store'],
    '#^auth/change-password$#' => ['controller' => 'Auth', 'action' => 'change-password'],
    '#^auth/update-password$#' => ['controller' => 'Auth', 'action' => 'update-password'],
    '#^auth/forgot-password$#' => ['controller' => 'Auth', 'action' => 'forgot-password'],
    '#^auth/send-reset-link$#' => ['controller' => 'Auth', 'action' => 'send-reset-link'],
    '#^auth/profile$#' => ['controller' => 'Auth', 'action' => 'profile'],
    '#^auth/update-profile$#' => ['controller' => 'Auth', 'action' => 'update-profile'],
    
    // Admin routes
    '#^admin/dashboard$#' => ['controller' => 'Admin', 'action' => 'dashboard'],
    '#^admin/users$#' => ['controller' => 'Admin', 'action' => 'users'],
    '#^admin/clients$#' => ['controller' => 'Admin', 'action' => 'clients'],
    '#^admin/medicines$#' => ['controller' => 'Admin', 'action' => 'medicines'],
    '#^admin/reports$#' => ['controller' => 'Admin', 'action' => 'reports'],
    
    // Veterinary routes
    '#^veterinary/dashboard$#' => ['controller' => 'Veterinary', 'action' => 'dashboard'],
    '#^veterinary/treatments$#' => ['controller' => 'Veterinary', 'action' => 'treatments'],
    '#^veterinary/vaccinations$#' => ['controller' => 'Veterinary', 'action' => 'vaccinations'],
    '#^veterinary/reminders$#' => ['controller' => 'Veterinary', 'action' => 'reminders'],
    
    // Client routes
    '#^client/dashboard$#' => ['controller' => 'Client', 'action' => 'dashboard'],
    '#^client/animals$#' => ['controller' => 'Client', 'action' => 'animals'],
    '#^client/appointments$#' => ['controller' => 'Client', 'action' => 'appointments'],
    
    // Default route
    '#^$#' => ['controller' => 'Auth', 'action' => 'login']
];
?>