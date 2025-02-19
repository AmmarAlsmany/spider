$(document).ready(function() {
    let mapInstance = null;
    let resizeTimer = null;

    function initializeMap(isDarkMode) {
        if ($('#world-map').length) {
            var mapContainer = $('#world-map');
            
            // Set explicit dimensions
            mapContainer.css({
                'width': '100%',
                'height': '350px',
                'min-width': '300px',
                'display': 'block'
            });

            // Force a reflow
            mapContainer[0].offsetHeight;

            try {
                // Clean up existing instance
                if (mapInstance) {
                    mapInstance.remove();
                    mapInstance = null;
                }

                // Initialize new map instance
                mapInstance = mapContainer.vectorMap({
                    map: 'world_mill_en',
                    backgroundColor: 'transparent',
                    zoomOnScroll: false,
                    regionStyle: {
                        initial: {
                            fill: isDarkMode ? '#2d3d4d' : '#7c8a96'
                        }
                    }
                }).vectorMap('get', 'mapObject');
            } catch (error) {
                console.error('Error initializing vector map:', error);
            }
        }
    }

    // Handle window resize with debouncing
    $(window).on('resize', function() {
        if (resizeTimer) {
            clearTimeout(resizeTimer);
        }
        
        resizeTimer = setTimeout(function() {
            if (mapInstance) {
                const isDarkMode = document.documentElement.classList.contains('dark-theme');
                initializeMap(isDarkMode);
            }
        }, 250); // Wait for resize to finish
    });

    // Initialize map with current theme
    const isDarkMode = document.documentElement.classList.contains('dark-theme');
    initializeMap(isDarkMode);

    // Listen for theme changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                const isDarkMode = document.documentElement.classList.contains('dark-theme');
                initializeMap(isDarkMode);
            }
        });
    });

    // Start observing theme changes
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
});
