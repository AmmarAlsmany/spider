/**
 * Dark Mode Toggle Functionality
 * Handles setting and toggling of dark/light theme
 */

(function() {
    "use strict";

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        const darkModeToggle = document.getElementById('darkModeToggle');
        
        if (!darkModeToggle) {
            console.error('Dark mode toggle element not found!');
            return;
        }
        
        console.log('Dark mode toggle initialized');
        
        // Function to set dark mode state
        function setDarkMode(enabled) {
            const html = document.documentElement;
            const icon = darkModeToggle.querySelector('i');
            
            if (enabled) {
                html.classList.add('dark-theme');
                if (icon) {
                    icon.classList.remove('bx-moon');
                    icon.classList.add('bx-sun');
                }
            } else {
                html.classList.remove('dark-theme');
                if (icon) {
                    icon.classList.remove('bx-sun');
                    icon.classList.add('bx-moon');
                }
            }
            
            localStorage.setItem('darkMode', enabled ? 'enabled' : 'disabled');
            console.log('Dark mode set to:', enabled ? 'dark' : 'light');
            
            // Handle vector maps - fix for NaN errors
            setTimeout(function() {
                handleVectorMaps();
            }, 100);
        }
        
        // Function to fix vector map issues after theme change
        function handleVectorMaps() {
            if (typeof window.jQuery !== 'undefined') {
                try {
                    // If vector maps exist, reinitialize them
                    const maps = jQuery('.jvectormap-container');
                    if (maps.length > 0) {
                        console.log('Reinitializing vector maps after theme change');
                        
                        // Temporarily hide maps to prevent NaN errors
                        maps.css('visibility', 'hidden');
                        
                        // Force window resize event to recalculate map dimensions
                        setTimeout(function() {
                            jQuery(window).trigger('resize');
                            maps.css('visibility', 'visible');
                        }, 200);
                    }
                } catch (e) {
                    console.error('Error handling vector maps:', e);
                }
            }
        }
        
        // Initialize based on local storage or system preference
        const isDarkMode = localStorage.getItem('darkMode') === 'enabled';
        setDarkMode(isDarkMode);
        
        // Toggle dark mode on click
        darkModeToggle.addEventListener('click', function(e) {
            e.preventDefault();
            const currentMode = document.documentElement.classList.contains('dark-theme');
            setDarkMode(!currentMode);
            console.log('Dark mode toggled to:', !currentMode ? 'dark' : 'light');
        });
    });
})();
