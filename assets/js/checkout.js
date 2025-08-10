/**
 * Checkout JavaScript
 *
 * Handles customer type selection and NIP field visibility
 */
(function ($) {
    'use strict';

    const Checkout = {
        init: function () {
            this.customerTypeRadios = $('input[name="customer_type"]');
            this.nipField = $('.checkout-form__fields-row--company, #billing_nip_field');
            
            this.bindEvents();
        },

        bindEvents: function () {
            // Customer type selection
            this.customerTypeRadios.on('change', this.handleCustomerTypeChange.bind(this));
            
            // Initialize with current selection
            this.handleCustomerTypeChange();
            
            // Shipping method selection
         //   $('body').on('change', 'input.shipping_method', this.handleShippingMethodChange.bind(this));
            
            // Payment method selection
        //    $('input.woocommerce-savedpayment, input.payment_method').on('change', this.handlePaymentMethodChange.bind(this));
            
            // Initialize payment methods
          //  this.handlePaymentMethodChange();
        },

        handleCustomerTypeChange: function () {
            const customerType = $('input[name="customer_type"]:checked').val();
            
            // Update radio styles
            this.customerTypeRadios.each(function () {
                const $label = $(this).next('.checkout-form__radio-label');
                
                if ($(this).is(':checked')) {
                    $label.addClass('checkout-form__radio-label--checked');
                } else {
                    $label.removeClass('checkout-form__radio-label--checked');
                }
            });
            
            // Show/hide NIP field
            if (customerType === 'company') {
                this.nipField.addClass('active').show();
                $('#billing_nip').attr('required', 'required');
            } else {
                this.nipField.removeClass('active').hide();
                $('#billing_nip').removeAttr('required');
            }
        },

        
  
    };

    $(document).ready(function () {
        if ($('.woocommerce-checkout').length) {
            Checkout.init();
        }
    });

})(jQuery);
