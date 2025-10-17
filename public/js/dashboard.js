    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar toggle functionality
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
            });
        }
        
        if (mobileSidebarToggle) {
            mobileSidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('mobile-open');
            });
        }
        
        // Sub-menu toggle functionality
        const sidebarNavItems = document.querySelectorAll('.sidebar-nav-item');
        sidebarNavItems.forEach(item => {
            const link = item.querySelector('.nav-link');
            link.addEventListener('click', function(e) {
                if (!sidebar.classList.contains('collapsed')) {
                    e.preventDefault();
                    item.classList.toggle('active');
                }
            });
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !mobileSidebarToggle.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
            }
        });
        
        // Active page highlighting
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-link[href]');
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
                // Also activate parent menu items
                let parent = link.closest('.sub-group-menu');
                if (parent) {
                    parent.previousElementSibling.classList.add('active');
                    parent.parentElement.classList.add('active');
                }
            }
        });

        // Preloader Handling
        const preloader = document.getElementById('preloader');
        
        // Hide preloader when window fully loads
        window.addEventListener('load', function() {
            setTimeout(function() {
                if (preloader) {
                    preloader.style.opacity = '0';
                    preloader.style.visibility = 'hidden';
                    document.body.classList.add('loaded');
                }
            }, 500);
        });

        // Safety: force hide after 5 seconds
        setTimeout(function() {
            if (preloader) {
                preloader.style.display = 'none';
                preloader.style.opacity = '0';
                preloader.style.visibility = 'hidden';
                document.body.classList.add('loaded');
            }
        }, 5000);

        // Initialize counter animation for stats
        $('.stat-value').counterUp({
            delay: 10,
            time: 1000
        });
    });
