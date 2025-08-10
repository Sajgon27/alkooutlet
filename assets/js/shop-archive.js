// Shop Archive Filters Toggle for Mobile
document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    const mobileFilterBtn = document.querySelector('.button--mobile-filters');
    const sidebar = document.querySelector('.shop-archive__sidebar');
    
    if (!mobileFilterBtn || !sidebar) {
        return; // Exit if elements don't exist
    }
    
    // Set initial display style
    if (window.innerWidth < 1024) {
        sidebar.style.display = 'none';
    }
    
    // Function to toggle filters visibility
    function toggleFilters() {
          // Update button text/icon based on state
        console.log('eoo');
        if (!sidebar.classList.contains('active')) {
            console.log('not containe');
            mobileFilterBtn.querySelector('img').style.transform = 'rotate(0)';
            mobileFilterBtn.classList.add('button--mobile-filters-active');
        } else {
            console.log('containe');
            mobileFilterBtn.querySelector('img').style.transform = 'rotate(180deg)';
            mobileFilterBtn.classList.remove('button--mobile-filters-active');
        }
        // Opening the filters
        if (!sidebar.classList.contains('active')) {
            sidebar.style.display = 'flex';
            sidebar.classList.add('animating');
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    sidebar.classList.add('active');
                });
            });
        } 
        // Closing the filters
        else {
            sidebar.classList.add('closing');
            sidebar.classList.remove('active');
            
            // Use transitionend to properly clean up classes after animation completes
            const onTransitionEnd = () => {
                sidebar.classList.remove('animating');
                sidebar.classList.remove('closing');
                sidebar.style.display = 'none';
                sidebar.removeEventListener('transitionend', onTransitionEnd);
            };
            
            sidebar.addEventListener('transitionend', onTransitionEnd);
        }
        
      
    }
    
    // Event listener for filter toggle button
    mobileFilterBtn.addEventListener('click', toggleFilters);
    
    // Hide filters on window resize to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('active');
            sidebar.classList.remove('animating');
            sidebar.classList.remove('closing');
            sidebar.style.display = ''; // Reset to default display from CSS
            mobileFilterBtn.querySelector('img').style.transform = 'rotate(0)';
            mobileFilterBtn.classList.remove('button--mobile-filters-active');
        } else {
            // On mobile, if not active, make sure it's hidden
            if (!sidebar.classList.contains('active')) {
                sidebar.style.display = 'none';
            }
        }
    });
});
