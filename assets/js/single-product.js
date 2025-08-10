// CUSTOM QUANTITY INPUT
function adjustValue(value) {
  const input = document.getElementsByName("quantity")[0];
  let newValue = Number(input.value) + value;
  if (newValue < 1) {
    newValue = 1;
  }

  input.value = newValue;
  
  // Trigger input event to check discount eligibility
  input.dispatchEvent(new Event('input'));
}

// DISCOUNT CARDS FUNCTIONALITY
document.addEventListener('DOMContentLoaded', function() {
  // Initialize Swiper for mobile view
  const initMobileSwiper = () => {
    const discountCardsContainer = document.querySelector('.product__discount-cards');
    
    if (!discountCardsContainer) return;
    
    // Only initialize swiper on mobile
    if (window.innerWidth < 768) {
      // Check if swiper isn't already initialized
      if (!discountCardsContainer.swiper) {
        // Initialize Swiper
        const discountSwiper = new Swiper(discountCardsContainer, {
          slidesPerView: 1,
          spaceBetween: 20,
          threshold: 5, // Minimum distance for a swipe
          autoHeight: false,
          resistance: true,
          loop:true,
          resistanceRatio: 0.85,
        });
      }
    } else if (discountCardsContainer.swiper) {
      // If we're not on mobile but swiper is initialized, destroy it
      discountCardsContainer.swiper.destroy(true, true);
    }
  };

  // Initialize on page load
  initMobileSwiper();
  
  // Update on window resize
  window.addEventListener('resize', () => {
    initMobileSwiper();
  });

  // Get product ID with multiple fallback methods
  let currentProductId = null;
  
  // Method 1: Check data attribute on discount cards container
  const discountCardsContainer = document.querySelector('.product__discount-cards');
  if (discountCardsContainer && discountCardsContainer.dataset.productId) {
    currentProductId = parseInt(discountCardsContainer.dataset.productId);
  }
  
  // Method 2: Check cart form data attribute
  if (!currentProductId) {
    const cartForm = document.querySelector('form.cart');
    if (cartForm && cartForm.dataset.productId) {
      currentProductId = parseInt(cartForm.dataset.productId);
    }
  }
  
  // Method 3: Check add to cart button
  if (!currentProductId) {
    const addToCartButton = document.querySelector('button[name="add-to-cart"]');
    if (addToCartButton && addToCartButton.value) {
      currentProductId = parseInt(addToCartButton.value);
    }
  }
  
  // Method 4: Check hidden input
  if (!currentProductId) {
    const productIdInput = document.querySelector('input[name="add-to-cart"]');
    if (productIdInput && productIdInput.value) {
      currentProductId = parseInt(productIdInput.value);
    }
  }
  
  // Method 5: Extract from URL
  if (!currentProductId) {
    const urlMatch = window.location.pathname.match(/\/produkt\/[^\/]+\/?$/);
    if (urlMatch) {
      // This is a product page, try to get ID from form or other sources
      const forms = document.querySelectorAll('form');
      forms.forEach(form => {
        if (form.classList.contains('cart') || form.querySelector('[name="add-to-cart"]')) {
          const button = form.querySelector('button[name="add-to-cart"]');
          const input = form.querySelector('input[name="add-to-cart"]');
          if (button && button.value) {
            currentProductId = parseInt(button.value);
          } else if (input && input.value) {
            currentProductId = parseInt(input.value);
          }
        }
      });
    }
  }
  
  if (!currentProductId) {
    console.warn('Could not detect product ID');
    return;
  }

  console.log('Detected product ID:', currentProductId);

  const discountCards = document.querySelectorAll('.discount-card');
  const quantityInput = document.querySelector('input[name="quantity"]');
  
  if (!discountCards.length || !quantityInput) {
    return;
  }

  let activeDiscount = null;

  // Handle discount card clicks
  discountCards.forEach(card => {
    const button = card.querySelector('.discount-card__button');
    if (!button) return;

    button.addEventListener('click', function(e) {
      e.preventDefault();
      
      const discount = parseInt(card.dataset.discount);
      const quantity = parseInt(card.dataset.quantity);
      const minQty = parseInt(card.dataset.minQty);
      const maxQty = parseInt(card.dataset.maxQty);
      
      // Set quantity in input
      quantityInput.value = quantity;
      
      // Calculate savings
      const productPrice = getProductPrice();
      const savings = (productPrice * quantity * discount / 100).toFixed(2);
      
      // Show temporary message
      showDiscountMessage(quantity, savings);
      
      // Store discount info for cart
      activeDiscount = {
        discount: discount,
        quantity: quantity,
        minQty: minQty,
        maxQty: maxQty,
        productId: currentProductId.toString() // Ensure it's a string
      };
      
      console.log('Discount activated:', activeDiscount); // Debug log
      
      // Store in sessionStorage
      let productDiscounts = JSON.parse(sessionStorage.getItem('productDiscounts') || '{}');
      productDiscounts[currentProductId] = activeDiscount;
      sessionStorage.setItem('productDiscounts', JSON.stringify(productDiscounts));
    });
  });

  // Show temporary discount message
  function showDiscountMessage(quantity, savings) {
    // Remove existing message
    const existingMessage = document.querySelector('.discount-message');
    if (existingMessage) {
      existingMessage.remove();
    }
    
    // Create new message
    const message = document.createElement('div');
    message.className = 'discount-message';
    message.innerHTML = `Przy zakupie ${quantity}szt. zaoszczędzisz ${savings} zł`;
    
    // Insert after the form element with class .cart
    const cartForm = document.querySelector('form.cart');
    if (cartForm && cartForm.parentNode) {
      cartForm.parentNode.insertBefore(message, cartForm.nextSibling);
    }
    // Remove message after 5 seconds
    setTimeout(() => {
      if (message.parentNode) {
        message.remove();
      }
    }, 5000);
  }

  // Get product price
  function getProductPrice() {
    const priceElement = document.querySelector('.price .woocommerce-Price-amount bdi');
    if (priceElement) {
      const priceText = priceElement.textContent.replace(/[^\d,]/g, '').replace(',', '.');
      return parseFloat(priceText) || 0;
    }
    return 0;
  }

  // Modify add to cart form submission
  const cartForm = document.querySelector('form.cart');
  if (cartForm) {
    cartForm.addEventListener('submit', function(e) {
      console.log('Form submit triggered, activeDiscount:', activeDiscount);
      
      if (activeDiscount) {
        // Remove any existing discount input
        const existingInput = cartForm.querySelector('input[name="discount_info"]');
        if (existingInput) {
          existingInput.remove();
        }
        
        // Add fresh discount info to form
        const discountInput = document.createElement('input');
        discountInput.type = 'hidden';
        discountInput.name = 'discount_info';
        discountInput.value = JSON.stringify(activeDiscount);
        cartForm.appendChild(discountInput);
        
        console.log('Discount data added to form:', discountInput.value);
      } else {
        console.log('No active discount to send');
      }
    });
    
    // Also try with the button click event as backup
    const addToCartButton = cartForm.querySelector('button[type="submit"], input[type="submit"]');
    if (addToCartButton) {
      addToCartButton.addEventListener('click', function(e) {
        console.log('Add to cart button clicked, activeDiscount:', activeDiscount);
        
        if (activeDiscount) {
          // Remove any existing discount input
          const existingInput = cartForm.querySelector('input[name="discount_info"]');
          if (existingInput) {
            existingInput.remove();
          }
          
          // Add fresh discount info to form
          const discountInput = document.createElement('input');
          discountInput.type = 'hidden';
          discountInput.name = 'discount_info';
          discountInput.value = JSON.stringify(activeDiscount);
          cartForm.appendChild(discountInput);
          
          console.log('Discount data added via button click:', discountInput.value);
        }
      });
    }
  }
});


function removeFavButtonIfMobile() {
    if (window.innerWidth < 1024) {
        const favButton = document.getElementById('fav-button-desktop');
        if (favButton) {
            favButton.remove();
        }
    }
}

// Run on page load
removeFavButtonIfMobile();

// Also run on window resize
window.addEventListener('resize', removeFavButtonIfMobile);


// PRODUCT INQUIRY MODAL
document.addEventListener('DOMContentLoaded', function() {
  const askButton = document.querySelector('.ask-about-product');
  const modal = document.querySelector('.product-inquiry-modal');
  const overlay = document.querySelector('.product-inquiry-overlay');
  const closeButton = document.querySelector('.product-inquiry-close');
  
  if (!askButton || !modal || !overlay || !closeButton) {
    return; // If any element doesn't exist, exit
  }
  
  // Function to open modal
  function openModal() {
    modal.classList.add('active');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
    
    // Add product name to a hidden field if it exists
    const productNameField = modal.querySelector('input[name="product-name"]');
    if (productNameField) {
      const productTitle = document.querySelector('.product__title');
      if (productTitle) {
        productNameField.value = productTitle.textContent;
      }
    }
  }
  
  // Function to close modal
  function closeModal() {
    modal.classList.remove('active');
    overlay.classList.remove('active');
    document.body.style.overflow = ''; // Restore scrolling
  }
  
  // Event listeners
  askButton.addEventListener('click', openModal);
  closeButton.addEventListener('click', closeModal);
  overlay.addEventListener('click', closeModal);
  
  // Close on ESC key
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && modal.classList.contains('active')) {
      closeModal();
    }
  });
  
  // Prevent closing when clicking inside the modal
  modal.addEventListener('click', function(event) {
    event.stopPropagation();
  });
});