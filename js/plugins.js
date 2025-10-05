// Avoid `console` errors in browsers that lack a console.
(function() {
  var method;
  var noop = function () {};
  var methods = [
    'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
    'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
    'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
    'timeline', 'timelineEnd', 'timeStamp', 'trace', 'warn'
  ];
  var length = methods.length;
  var console = (window.console = window.console || {});

  while (length--) {
    method = methods[length];

    // Only stub undefined methods.
    if (!console[method]) {
      console[method] = noop;
    }
  }
}());

// Place any jQuery/helper plugins in here.
// plugins.js - Load necessary plugins
(function($) {
    "use strict";

    // Simple scrollUp implementation if plugin not available
    if (!$.fn.scrollUp) {
        $.fn.scrollUp = function(options) {
            var settings = $.extend({
                scrollName: 'scrollUp',
                topDistance: 300,
                topSpeed: 300,
                animation: 'fade',
                animationInSpeed: 200,
                animationOutSpeed: 200,
                scrollText: '',
                scrollImg: false
            }, options);

            // Create scroll to top button
            var scrollButton = $('<div/>', {
                id: settings.scrollName,
                html: settings.scrollText,
                css: {
                    display: 'none',
                    position: 'fixed',
                    bottom: '20px',
                    right: '20px',
                    width: '40px',
                    height: '40px',
                    backgroundColor: '#667eea',
                    color: 'white',
                    borderRadius: '50%',
                    textAlign: 'center',
                    lineHeight: '40px',
                    cursor: 'pointer',
                    zIndex: 1000
                }
            }).appendTo('body');

            // Add icon
            scrollButton.html('<i class="fas fa-chevron-up"></i>');

            // Show/hide based on scroll position
            $(window).scroll(function() {
                if ($(this).scrollTop() > settings.topDistance) {
                    scrollButton.fadeIn(settings.animationInSpeed);
                } else {
                    scrollButton.fadeOut(settings.animationOutSpeed);
                }
            });

            // Scroll to top when clicked
            scrollButton.click(function(e) {
                e.preventDefault();
                $('html, body').animate({scrollTop: 0}, settings.topSpeed);
                return false;
            });
        };
    }

})(jQuery);