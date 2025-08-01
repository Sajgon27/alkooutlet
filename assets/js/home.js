document.addEventListener('DOMContentLoaded', function() {
    // Initialize Categories Swiper
    const categoriesSwiper = new Swiper('.categoriesSwiper', {
        slidesPerView: 2,
        spaceBetween: 16,
        grabCursor: true,
        loop: true,
        autoplay: {
            delay: 3500,
            disableOnInteraction: false,
        },
        breakpoints: {
            // Tablet and above
            768: {
                slidesPerView: 3,
                spaceBetween: 24,
            },
            // Desktop
            1024: {
                slidesPerView: 6,
                spaceBetween: 16,
                autoplay: false,
            }
        }
    });
    
    // Initialize Grid Boxes Swiper (only on mobile)
    const initGridBoxesSwiper = function() {
        let gridBoxesSwiper;
        let isInitialized = false;
        
        // Function to initialize or destroy Swiper based on screen width
        const handleGridBoxesSwiper = function() {
            if (window.innerWidth < 1024) {
                if (!isInitialized) {
                    // Initialize Swiper only on mobile
                    gridBoxesSwiper = new Swiper('.gridBoxesSwiper', {
                        slidesPerView: 1,
                        spaceBetween: 16,
                        grabCursor: true,
                        loop: true,
                        autoplay: {
                            delay: 4000,
                            disableOnInteraction: false,
                        }
                    });
                    isInitialized = true;
                }
            } else {
                // Destroy Swiper on tablet and desktop
                if (isInitialized && gridBoxesSwiper) {
                    gridBoxesSwiper.destroy(true, true);
                    isInitialized = false;
                }
            }
        };
        
        // Initial check
        handleGridBoxesSwiper();
        
        // Listen for resize events
        window.addEventListener('resize', handleGridBoxesSwiper);
    };
    
    // Initialize the grid boxes swiper with responsive behavior
    initGridBoxesSwiper();
    
    // Initialize Gallery 3 Columns Swiper (only on mobile)
    const initGallery3ColumnsSwiper = function() {
        let gallery3ColumnsSwiper;
        let isInitialized = false;
        
        // Function to initialize or destroy Swiper based on screen width
        const handleGallery3ColumnsSwiper = function() {
            if (window.innerWidth < 768) {
                if (!isInitialized) {
                    // Initialize Swiper only on mobile
                    gallery3ColumnsSwiper = new Swiper('.gallery3ColumnsSwiper', {
                        slidesPerView: 1,
                        spaceBetween: 16,
                        grabCursor: true,
                        loop: true,
                        autoplay: {
                            delay: 3500,
                            disableOnInteraction: false,
                        }
                    });
                    isInitialized = true;
                }
            } else {
                // Destroy Swiper on tablet and desktop
                if (isInitialized && gallery3ColumnsSwiper) {
                    gallery3ColumnsSwiper.destroy(true, true);
                    isInitialized = false;
                }
            }
        };
        
        // Initial check
        handleGallery3ColumnsSwiper();
        
        // Listen for resize events
        window.addEventListener('resize', handleGallery3ColumnsSwiper);
    };
    
    // Initialize the gallery swiper with responsive behavior
    initGallery3ColumnsSwiper();
    
    // Initialize Box Categories Swiper (only on mobile)
    const initBoxCategoriesSwiper = function() {
        let boxCategoriesSwiper;
        let isInitialized = false;
        
        // Function to initialize or destroy Swiper based on screen width
        const handleBoxCategoriesSwiper = function() {
            if (window.innerWidth < 768) {
                if (!isInitialized) {
                    // Initialize Swiper only on mobile
                    boxCategoriesSwiper = new Swiper('.boxCategoriesSwiper', {
                        slidesPerView: 1,
                        spaceBetween: 16,
                        grabCursor: true,
                        loop: true,
                        autoplay: {
                            delay: 3000,
                            disableOnInteraction: false,
                        }
                    });
                    isInitialized = true;
                }
            } else {
                // Destroy Swiper on tablet and desktop
                if (isInitialized && boxCategoriesSwiper) {
                    boxCategoriesSwiper.destroy(true, true);
                    isInitialized = false;
                }
            }
        };
        
        // Initial check
        handleBoxCategoriesSwiper();
        
        // Listen for resize events
        window.addEventListener('resize', handleBoxCategoriesSwiper);
    };
    
    // Initialize the box categories swiper with responsive behavior
    initBoxCategoriesSwiper();
});



