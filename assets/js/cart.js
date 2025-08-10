(function ($) {
  "use strict";

  // Cart functionality
  const Cart = {
    init: function () {
      this.cartContainer = $(".cart-container");
      this.cartForm = $(".woocommerce-cart-form");
      this.cartItemsContainer = $(".cart-items");
      this.cartSummary = $(".cart-summary");
      this.timeout = null;

      this.bindEvents();
    },

    bindEvents: function () {
      // Update quantity when + or - buttons are clicked
      $(document).on("click", ".qty-btn", this.handleQtyButtonClick.bind(this));

      // Update quantity when input field changes
      $(document).on("change", ".qty", this.handleQtyChange.bind(this));

      // Remove item from cart
      $(document).on(
        "click",
        ".cart_item .remove",
        this.handleRemoveItem.bind(this)
      );

      // Apply coupon
      $(document).on(
        "submit",
        ".cart-summary__coupon-form",
        this.handleCouponApply.bind(this)
      );

      // Remove coupon
      $(document).on(
        "click",
        ".sbs-remove-coupon",
        this.handleCouponRemove.bind(this)
      );
    },

    handleQtyButtonClick: function (e) {
      e.preventDefault();

      const $button = $(e.currentTarget);
      const $input = $button.closest(".quantity").find(".qty");
      const currentValue = parseInt($input.val(), 10);

      if ($button.hasClass("qty-minus")) {
        if (currentValue > 1) {
          $input.val(currentValue - 1).trigger("change");
        }
      } else {
        $input.val(currentValue + 1).trigger("change");
      }
    },
    handleQtyChange: function (e) {
      const $input = $(e.currentTarget);
      const cartItemKey = $input.closest(".cart_item").data("cart-item-key");

      // Prevent duplicate update triggers by adding a processing flag
      if ($input.data("processing")) {
        return;
      }

      $input.data("processing", true);

      // Clear the previous timeout
      if (this.timeout !== null) {
        clearTimeout(this.timeout);
      }

      // Set a new timeout
      this.timeout = setTimeout(() => {
        this.updateCartItem(cartItemKey, $input.val());
        // Remove the processing flag after update is sent
        $input.data("processing", false);
      }, 500);
    },

    handleRemoveItem: function (e) {
      e.preventDefault();

      const $link = $(e.currentTarget);
      const cartItemKey = $link.closest(".cart_item").data("cart-item-key");

      this.removeCartItem(cartItemKey);
    },

    handleCouponApply: function (e) {
      e.preventDefault();

      const $form = $(e.currentTarget);
      const couponCode = $form.find('input[name="coupon_code"]').val();

      if (!couponCode) {
        return;
      }

      this.applyCoupon(couponCode);
    },

    handleCouponRemove: function (e) {
      e.preventDefault();

      const $link = $(e.currentTarget);
      const couponCode = $link.data("coupon");

      this.removeCoupon(couponCode);
    },
    updateCartItem: function (cartItemKey, qty) {
      this.toggleLoading(true);

      $.ajax({
        type: "POST",
        url: wc_cart_params.ajax_url,
        data: {
          action: "sbs_update_cart_item",
          cart_item_key: cartItemKey,
          qty: qty,
          security: wc_cart_params.update_shipping_method_nonce,
        },
        success: (response) => {
          if (response.success) {
            this.updateCartFragments(response.data);
          } else {
            this.displayCouponMessage(response.data, "error");
          }
        },
        complete: () => {
          this.toggleLoading(false);
        },
      });
    },
    removeCartItem: function (cartItemKey) {
      this.toggleLoading(true);

      $.ajax({
        type: "POST",
        url: wc_cart_params.ajax_url,
        data: {
          action: "sbs_remove_cart_item",
          cart_item_key: cartItemKey,
          security: wc_cart_params.update_shipping_method_nonce,
        },
        success: (response) => {
          if (response.success) {
            this.updateCartFragments(response.data);
          } else {
            this.displayCouponMessage(response.data, "error");
          }
        },
        complete: () => {
          this.toggleLoading(false);
        },
      });
    },
    applyCoupon: function (couponCode) {
      this.toggleLoading(true);

      // Clear any previous message
      this.displayCouponMessage("", "");

      $.ajax({
        type: "POST",
        url: wc_cart_params.ajax_url,
        data: {
          action: "sbs_apply_coupon",
          coupon_code: couponCode,
          security: wc_cart_params.update_shipping_method_nonce,
        },
        success: (response) => {
          if (response.success) {
            this.updateCartFragments(response.data);
            // Clear the coupon input
            $(".cart-summary__coupon-input").val("");
            // Show success message
            this.displayCouponMessage(
              response.data.message ||
                "Kod rabatowy został pomyślnie zastosowany",
              "success"
            );
          } else {
            // Show error message
            const errorMessage = this.translateCouponError(response.data);
            this.displayCouponMessage(errorMessage, "error");
          }
        },
        complete: () => {
          this.toggleLoading(false);
        },
      });
    },
    removeCoupon: function (couponCode) {
      this.toggleLoading(true);

      // Clear any previous message
      this.displayCouponMessage("", "");

      $.ajax({
        type: "POST",
        url: wc_cart_params.ajax_url,
        data: {
          action: "sbs_remove_coupon",
          coupon_code: couponCode,
          security: wc_cart_params.update_shipping_method_nonce,
        },
        success: (response) => {
          if (response.success) {
            this.updateCartFragments(response.data);
            // Show success message
            this.displayCouponMessage(
              "Kod rabatowy został usunięty",
              "success"
            );
          } else {
            // Show error message
            const errorMessage = this.translateCouponError(response.data);
            this.displayCouponMessage(errorMessage, "error");
          }
        },
        complete: () => {
          this.toggleLoading(false);
        },
      });
    },

    updateCartFragments: function (data) {
      if (data.fragments) {
        $.each(data.fragments, function (key, value) {
          $(key).replaceWith(value);
        });
      }

      if (data.cart_hash) {
        $(".woocommerce-cart-form").data("cart-hash", data.cart_hash);

        if (sessionStorage) {
          sessionStorage.setItem("wc_cart_hash", data.cart_hash);
        }
      }

      // Check if cart is empty and refresh if needed
      if (
        data.cart_is_empty &&
        !$(".woocommerce-cart").hasClass("cart-empty")
      ) {
        window.location.reload();
      }

      $(document.body).trigger("wc_fragments_loaded");
    },
    toggleLoading: function (isLoading) {
      if (isLoading) {
        this.cartContainer.addClass("updating-cart");
      } else {
        this.cartContainer.removeClass("updating-cart");
      }
    },

    displayCouponMessage: function (message, type) {
      const $messageContainer = $(".cart-summary__coupon-message");

      // Clear the container
      $messageContainer.empty();

      // If no message, just return
      if (!message) {
        return;
      }

      // Decode HTML entities in the message
      const decodedMessage = $("<div/>").html(message).text();

      // Create message element
      const $message = $("<div>", {
        class: "cart-summary__message cart-summary__message--" + type,
        text: decodedMessage,
      });

      // Add to container
      $messageContainer.append($message);

      // Auto-hide success messages after 5 seconds
      if (type === "success") {
        setTimeout(() => {
          $message.fadeOut(300, function () {
            $(this).remove();
          });
        }, 5000);
      }
    },

    translateCouponError: function (errorMessage) {
      // Common WooCommerce error messages translated to Polish
      const errorMap = {
        "Coupon code already applied!":
          "Ten kod rabatowy został już zastosowany!",
        "Coupon does not exist!": "Podany kod rabatowy nie istnieje!",
        "Coupon has expired!": "Kod rabatowy wygasł!",
        "Coupon usage limit has been reached.":
          "Limit użyć kodu rabatowego został osiągnięty.",
        "Minimum spend of": "Minimalna kwota zamówienia dla tego kodu to",
        "Maximum spend of": "Maksymalna kwota zamówienia dla tego kodu to",
        "This coupon is not valid for sale items.":
          "Ten kod rabatowy nie jest ważny dla przecenionych produktów.",
        "Sorry, this coupon is not applicable to your cart contents.":
          "Przepraszamy, ten kod rabatowy nie jest dostępny dla produktów w Twoim koszyku.",
        "Please enter a coupon code.": "Proszę wprowadzić kod rabatowy.",
        "This coupon cannot be used in conjunction with other coupons.":
          "Ten kod rabatowy nie może być użyty razem z innymi kodami.",
        "nie może zostać wykorzystany, ponieważ nie istnieje": "nie istnieje",
      };

      // Try to find a direct translation
      if (errorMap[errorMessage]) {
        return errorMap[errorMessage];
      }

      // Check for partial matches
      for (const key in errorMap) {
        if (errorMessage.includes(key)) {
          return errorMessage.replace(key, errorMap[key]);
        }
      }

      // Default fallback
      return errorMessage;
    },
  };

  // Initialize when document is ready
  $(document).ready(function () {
    if ($(".woocommerce-cart").length) {
      Cart.init();
    }
  });
})(jQuery);
