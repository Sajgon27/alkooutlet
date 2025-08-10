/**
 * Main JavaScript file for the AlkoOutlet theme.
 */

document.addEventListener('DOMContentLoaded', () => {
    initMobileMenu();
    initSubmenuToggles();
    initLogosSlider();
    initProductsSliders();
  //  initBlogSlider();
});

/**
 * Initialize the mobile menu functionality.
 */
function initMobileMenu() {
    const menuToggle = document.querySelector('.header__menu-toggle');
    const mobileMenu = document.querySelector('.header__mobile-menu');
    const mobileMenuClose = document.querySelector('.header__mobile-menu-close');
    const body = document.body;

    if (menuToggle && mobileMenu && mobileMenuClose) {
        // Open mobile menu
        menuToggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            mobileMenu.classList.add('active');
            menuToggle.setAttribute('aria-expanded', 'true');
            menuToggle.classList.add('active');
            setTimeout(() => {
                body.style.overflow = 'hidden'; // Prevent scrolling when menu is open
            }, 200);
        });

        // Close mobile menu
        mobileMenuClose.addEventListener('click', () => {
            closeMobileMenu();
        });

        // Close mobile menu when clicking on the overlay (outside the menu)
        document.addEventListener('click', (event) => {
            if (
                mobileMenu.classList.contains('active') &&
                !mobileMenu.contains(event.target) &&
                !menuToggle.contains(event.target)
            ) {
                closeMobileMenu();
            }
        });

        // Close menu on escape key
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && mobileMenu.classList.contains('active')) {
                closeMobileMenu();
            }
        });

        // Function to close mobile menu
        function closeMobileMenu() {
            mobileMenu.classList.remove('active');
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.classList.remove('active');
            body.style.overflow = '';
        }
    }
}

/**
 * Initialize submenu toggles for the mobile menu.
 */
function initSubmenuToggles() {
    const menuItemsWithChildren = document.querySelectorAll('.header__mobile-menu-list .menu-item-has-children');
    
    menuItemsWithChildren.forEach(item => {
        const link = item.querySelector('a');
        const subMenu = item.querySelector('.sub-menu');
        
        if (link && subMenu) {
            // Create toggle button
            const toggleBtn = document.createElement('button');
            toggleBtn.classList.add('submenu-toggle');
            toggleBtn.innerHTML = '<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 9L11 4L10 3L6 7L2 3L1 4L6 9Z" fill="currentColor"/></svg><span class="screen-reader-text">Toggle submenu</span>';
            toggleBtn.setAttribute('aria-expanded', 'false');
            toggleBtn.setAttribute('aria-label', 'Toggle submenu');
            
            // Insert toggle button after link
            link.parentNode.insertBefore(toggleBtn, link.nextSibling);
            
            // Add click event to toggle
            toggleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const expanded = toggleBtn.getAttribute('aria-expanded') === 'true';
                toggleBtn.setAttribute('aria-expanded', !expanded);
                
                // Toggle the submenu visibility with a slight animation
                if (expanded) {
                    subMenu.style.height = subMenu.scrollHeight + 'px';
                    setTimeout(() => {
                        subMenu.style.height = '0px';
                        setTimeout(() => {
                            subMenu.style.display = 'none';
                        }, 300);
                    }, 10);
                } else {
                    subMenu.style.display = 'block';
                    subMenu.style.height = '0px';
                    setTimeout(() => {
                        subMenu.style.height = subMenu.scrollHeight + 'px';
                        setTimeout(() => {
                            subMenu.style.height = 'auto';
                        }, 300);
                    }, 10);
                }
            });
        }
    });
}

/**
 * Initialize logos slider
 */
function initLogosSlider() {
    // Check if the logos slider exists on the page
    const logosSlider = document.querySelector('.logosSwiper');
    
    if (logosSlider) {
        new Swiper('.logosSwiper', {
            slidesPerView: 2,
            spaceBetween: 20,
            loop: true,
            grabCursor: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            speed: 800,
            breakpoints: {
                // Tablet
                768: {
                    slidesPerView: 4,
                    spaceBetween: 30,
                },
                // Desktop
                1024: {
                    slidesPerView: 6,
                    spaceBetween: 30,
                }
            }
        });
    }
}

/**
 * Initialize products sliders
 */
function initProductsSliders() {
    // Find all products sliders on the page
    const productsSwipers = document.querySelectorAll('.productsSwiper');
    
    if (productsSwipers.length > 0) {
        productsSwipers.forEach((swiperElement, index) => {
            const parentSection = swiperElement.closest('.products-slider');
            if (!parentSection) return;
            
            // Get the navigation buttons from the same section
            const prevButton = parentSection.querySelector('.products-slider__nav-button--prev');
            const nextButton = parentSection.querySelector('.products-slider__nav-button--next');
            
            if (!prevButton || !nextButton) {
                console.error('Navigation buttons not found for products slider');
                return;
            }
            
            // Create unique identifier for this swiper
            const swiperInstanceId = `productsSwiper-${index}`;
            swiperElement.classList.add(swiperInstanceId);
            
            // Initialize the swiper with fixed 3 slides on desktop
            const swiper = new Swiper(`.${swiperInstanceId}`, {
                slidesPerView: 1,
                spaceBetween: 16,
                grabCursor: true,
                watchOverflow: true,
                observer: true,
                observeParents: true,
                init: true,
                loop:true,
                navigation: {
                    prevEl: prevButton,
                    nextEl: nextButton
                },
                breakpoints: {
                    // Small mobile
                    580: {
                        slidesPerView: 2,
                        spaceBetween: 24
                    },
                    // Tablet
                    828: {
                        slidesPerView: 2,
                        spaceBetween: 24
                    },
                    // Desktop
                    1154: {
                        slidesPerView: 3,
                        spaceBetween: 32
                    },
                
                }
            });
            
            // Add event listeners for navigation buttons to ensure they work
            if (prevButton && nextButton) {
                prevButton.addEventListener('click', () => {
                    swiper.slidePrev();
                });
                
                nextButton.addEventListener('click', () => {
                    swiper.slideNext();
                });
            }
            
            // Force update after initial render
            setTimeout(() => {
                swiper.update();
            }, 100);
        });
    }
}

