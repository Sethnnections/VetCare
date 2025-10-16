<?php
$routes = [
    // Home and Auth Routes
    '/' => [
        'controller' => 'Auth',
        'action' => 'login'
    ],
    '/login' => [
        'controller' => 'Auth',
        'action' => 'login'
    ],
    '/auth/login' => [
        'controller' => 'Auth',
        'action' => 'login'
    ],
    '/authenticate' => [
        'controller' => 'Auth',
        'action' => 'authenticate'
    ],
    '/auth/authenticate' => [
        'controller' => 'Auth',
        'action' => 'authenticate'
    ],
    '/logout' => [
        'controller' => 'Auth',
        'action' => 'logout'
    ],
    '/auth/logout' => [
        'controller' => 'Auth',
        'action' => 'logout'
    ],
    '/register' => [
        'controller' => 'Auth',
        'action' => 'register'
    ],
    '/auth/register' => [
        'controller' => 'Auth',
        'action' => 'register'
    ],
    '/auth/change-password' => [
        'controller' => 'Auth',
        'action' => 'changePassword'
    ],
    '/auth/update-password' => [
        'controller' => 'Auth',
        'action' => 'updatePassword'
    ],
    '/auth/forgot-password' => [
        'controller' => 'Auth',
        'action' => 'forgotPassword'
    ],
    '/auth/send-reset-link' => [
        'controller' => 'Auth',
        'action' => 'sendResetLink'
    ],
    '/auth/profile' => [
        'controller' => 'Auth',
        'action' => 'profile'
    ],
    '/auth/update-profile' => [
        'controller' => 'Auth',
        'action' => 'updateProfile'
    ],
    
    // Test Route
    '/test' => [
        'controller' => 'Test',
        'action' => 'index'
    ],
    // Dashboard routes

    // Dashboard Routes
    '/dashboard' => [
        'controller' => 'Dashboard',
        'action' => 'index'
    ],
    '/admin/dashboard' => [
        'controller' => 'Dashboard',
        'action' => 'admin'
    ],
    '/veterinary/dashboard' => [
        'controller' => 'Dashboard',
        'action' => 'veterinary'
    ],
    '/client/dashboard' => [
        'controller' => 'Dashboard',
        'action' => 'client'
    ],
    
    // Client Routes
    '/client/profile' => [
        'controller' => 'ClientProfile',
        'action' => 'profile'
    ],
    '/client/profile/update' => [
        'controller' => 'ClientProfile',
        'action' => 'updateProfile'
    ],
    '/client/animals' => [
        'controller' => 'ClientProfile',
        'action' => 'animals'
    ],
    '/client/animals/add' => [
        'controller' => 'ClientProfile',
        'action' => 'addAnimal'
    ],
    '/client/animals/{id}/edit' => [
        'controller' => 'ClientProfile',
        'action' => 'editAnimal'
    ],
    '/client/animals/{id}/delete' => [
        'controller' => 'ClientProfile',
        'action' => 'deleteAnimal'
    ],
    '/client/animals/{id}' => [
        'controller' => 'ClientProfile',
        'action' => 'viewAnimal'
    ],
    '/client/profile/create' => [
        'controller' => 'ClientProfile',
        'action' => 'create'
    ],
    '/client/profile/store' => [
        'controller' => 'ClientProfile',
        'action' => 'store'
    ],
    
    // User Management Routes (Admin)
    '/users' => [
        'controller' => 'User',
        'action' => 'index'
    ],
    '/users/create' => [
        'controller' => 'User',
        'action' => 'create'
    ],
    '/users/store' => [
        'controller' => 'User',
        'action' => 'store'
    ],
    '/users/{id}' => [
        'controller' => 'User',
        'action' => 'show'
    ],
    '/users/{id}/edit' => [
        'controller' => 'User',
        'action' => 'edit'
    ],
    '/users/{id}/update' => [
        'controller' => 'User',
        'action' => 'update'
    ],
    '/users/{id}/deactivate' => [
        'controller' => 'User',
        'action' => 'deactivate'
    ],
    '/users/{id}/activate' => [
        'controller' => 'User',
        'action' => 'activate'
    ],
    
    // Client Management Routes
    '/clients' => [
        'controller' => 'Client',
        'action' => 'index'
    ],
    '/clients/create' => [
        'controller' => 'Client',
        'action' => 'create'
    ],
    '/clients/store' => [
        'controller' => 'Client',
        'action' => 'store'
    ],
    '/clients/{id}' => [
        'controller' => 'Client',
        'action' => 'show'
    ],
    '/clients/{id}/edit' => [
        'controller' => 'Client',
        'action' => 'edit'
    ],
    '/clients/{id}/update' => [
        'controller' => 'Client',
        'action' => 'update'
    ],
    '/clients/{id}/delete' => [
        'controller' => 'Client',
        'action' => 'delete'
    ],
    '/clients/{id}/activate' => [
        'controller' => 'Client',
        'action' => 'activate'
    ],
    
    // Animal Routes
    '/animals' => [
        'controller' => 'Animal',
        'action' => 'index'
    ],
    '/animals/create' => [
        'controller' => 'Animal',
        'action' => 'create'
    ],
    '/animals/store' => [
        'controller' => 'Animal',
        'action' => 'store'
    ],
    '/animals/{id}' => [
        'controller' => 'Animal',
        'action' => 'show'
    ],
    '/animals/{id}/edit' => [
        'controller' => 'Animal',
        'action' => 'edit'
    ],
    '/animals/{id}/update' => [
        'controller' => 'Animal',
        'action' => 'update'
    ],
    
    // AJAX Routes
    '/api/users/search' => [
        'controller' => 'User',
        'action' => 'search'
    ],
    '/api/users/stats' => [
        'controller' => 'User',
        'action' => 'stats'
    ],
    '/api/users/by-role/{role}' => [
        'controller' => 'User',
        'action' => 'byRole'
    ],
    '/api/clients/search' => [
        'controller' => 'Client',
        'action' => 'search'
    ],
    '/api/clients/stats' => [
        'controller' => 'Client',
        'action' => 'stats'
    ],
    '/api/auth/check-email' => [
        'controller' => 'Auth',
        'action' => 'checkEmail'
    ],
    '/api/auth/check-session' => [
        'controller' => 'Auth',
        'action' => 'checkSession'
    ],
    
];
?>