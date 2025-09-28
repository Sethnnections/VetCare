// public/assets/js/app.js

$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Initialize popovers
    $('[data-bs-toggle="popover"]').popover();
    
    // Handle form submissions with loading state
    $('form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        if (submitBtn.length) {
            submitBtn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...'
            );
        }
    });
    
    // Auto-dismiss alerts after 5 seconds
    $('.alert').not('.alert-permanent').delay(5000).fadeOut(300);
    
    // Handle sidebar toggle on mobile
    $('#sidebarToggle').on('click', function() {
        $('#sidebar').toggleClass('show');
    });
    
    // Check session status periodically
    setInterval(checkSession, 300000); // Check every 5 minutes
    
    // Load notifications
    loadNotifications();
    
    // Handle search functionality
    $('.search-form').on('submit', function(e) {
        const searchTerm = $(this).find('input[name="search"]').val().trim();
        if (!searchTerm) {
            e.preventDefault();
            window.location.href = $(this).attr('action').split('?')[0];
        }
    });
    
    // Date picker initialization
    $('.datepicker').each(function() {
        $(this).attr('type', 'date');
    });
    
    // Confirm before destructive actions
    $('.btn-delete, .btn-danger').on('click', function(e) {
        if (!confirm('Are you sure you want to perform this action? This cannot be undone.')) {
            e.preventDefault();
            return false;
        }
    });
});

// Check session status
function checkSession() {
    $.ajax({
        url: APP_CONFIG.baseUrl + 'auth/check-session',
        type: 'GET',
        success: function(response) {
            if (response.status === 'expired') {
                alert('Your session has expired. You will be redirected to the login page.');
                window.location.href = APP_CONFIG.baseUrl + 'auth/login';
            }
        },
        error: function() {
            console.log('Session check failed');
        }
    });
}

// Load notifications
function loadNotifications() {
    $.ajax({
        url: APP_CONFIG.baseUrl + 'notifications',
        type: 'GET',
        success: function(response) {
            if (response.count > 0) {
                $('#notificationCount').text(response.count).show();
                updateNotificationDropdown(response.notifications);
            }
        },
        error: function() {
            console.log('Failed to load notifications');
        }
    });
}

// Update notification dropdown
function updateNotificationDropdown(notifications) {
    const dropdown = $('.notification-dropdown');
    dropdown.find('.dropdown-item-text').remove();
    
    notifications.forEach(notification => {
        const item = $(
            `<div class="notification-item ${notification.unread ? 'unread' : ''}">
                <div class="d-flex justify-content-between">
                    <strong>${notification.title}</strong>
                    <small class="text-muted">${notification.time}</small>
                </div>
                <div class="notification-message">${notification.message}</div>
            </div>`
        );
        
        dropdown.append(item);
    });
    
    if (notifications.length === 0) {
        dropdown.append('<div class="dropdown-item-text text-muted text-center">No new notifications</div>');
    }
}

// Format currency
function formatCurrency(amount, currency = 'MWK') {
    return currency + ' ' + parseFloat(amount).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Show loading overlay
function showLoading() {
    $('#loadingOverlay').show();
}

// Hide loading overlay
function hideLoading() {
    $('#loadingOverlay').hide();
}

// Handle AJAX errors
function handleAjaxError(xhr, status, error) {
    hideLoading();
    
    let message = 'An error occurred while processing your request.';
    
    if (xhr.responseJSON && xhr.responseJSON.message) {
        message = xhr.responseJSON.message;
    } else if (xhr.status === 0) {
        message = 'Network error. Please check your internet connection.';
    } else if (xhr.status === 500) {
        message = 'Server error. Please try again later.';
    }
    
    showAlert('error', message);
}

// Show alert message
function showAlert(type, message) {
    const alertClass = type === 'error' ? 'danger' : type;
    const alertHtml = `
        <div class="alert alert-${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Prepend to the first container-fluid element or create one
    const container = $('.container-fluid:first');
    if (container.length) {
        container.prepend(alertHtml);
    } else {
        $('main').prepend(`<div class="container-fluid mt-3">${alertHtml}</div>`);
    }
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        $(`.alert`).alert('close');
    }, 5000);
}

// Validate email format
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Validate phone format (Malawi)
function isValidPhone(phone) {
    const phoneRegex = /^(\+265|0)?[1-9][0-9]{7,8}$/;
    return phoneRegex.test(phone.replace(/\s/g, ''));
}

// Debounce function for search inputs
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}