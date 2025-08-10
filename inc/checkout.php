<?php

/**
 * SBS Theme Checkout Functions
 *
 * Functions for customizing the WooCommerce checkout process
 *
 * @package SBS
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Remove unwanted elements from checkout and customize the checkout experience
 */
function sbs_customize_checkout()
{
    // Remove the order review (cart items) from default position
    remove_action('woocommerce_checkout_order_review', 'woocommerce_order_review', 10);

    // Remove the coupon form from default location
    remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);

    // Remove coupon notice from default location and add it in a higher priority
    remove_action('woocommerce_before_checkout_form', 'woocommerce_output_all_notices', 10);
    add_action('woocommerce_before_checkout_form', 'woocommerce_output_all_notices', 5);

    // Disable coupons functionality completely
    add_filter('woocommerce_coupons_enabled', '__return_false');
}

/**
 * Hide shipping methods when only one is available
 */
function sbs_hide_shipping_when_single_method($show_shipping)
{
    if (WC()->cart->get_shipping_methods() && count(WC()->cart->get_shipping_methods()) <= 1) {
        return false;
    }
    return $show_shipping;
}

// Initialize checkout customizations
add_action('woocommerce_before_checkout_form', 'sbs_customize_checkout', 5);


// Remove the order notes field and shipping address fields and country field
add_filter('woocommerce_enable_order_notes_field', '__return_false');
add_filter('woocommerce_cart_needs_shipping_address', '__return_false');
add_filter('woocommerce_checkout_fields', function ($fields) {
    unset($fields['billing']['billing_country']);
    return $fields;
});


// Edit fields placeholders
add_filter('woocommerce_checkout_fields', function ($fields) {
    $fields['billing']['billing_first_name']['placeholder'] = 'Wpisz imię';
    $fields['billing']['billing_first_name']['label'] = 'Imię';

    $fields['billing']['billing_last_name']['placeholder'] = 'Wpisz nazwisko';
    $fields['billing']['billing_last_name']['label'] = 'Nazwisko';

    $fields['billing']['billing_phone']['placeholder'] = 'Wpisz numer telefonu';
    $fields['billing']['billing_phone']['label'] = 'Numer telefonu';
    $fields['billing']['billing_phone']['required'] = true;

    $fields['billing']['billing_email']['placeholder'] = 'Wpisz adres e-mail';
    $fields['billing']['billing_email']['label'] = 'Adres e-mail';

    $fields['billing']['billing_address_1']['placeholder'] = 'Wpisz ulicę, numer budynku / lokalu';
    $fields['billing']['billing_address_1']['label'] = 'Ulica';

    $fields['billing']['billing_address_2']['placeholder'] = 'Ciąg dalszy adresu (opcjonalnie)';
    $fields['billing']['billing_address_2']['label'] = 'Numer mieszkania';


    $fields['billing']['billing_postcode']['placeholder'] = 'Wpisz kod pocztowy';
    $fields['billing']['billing_postcode']['label'] = 'Kod pocztowy';

    $fields['billing']['billing_city']['placeholder'] = 'Wpisz miasto';
    $fields['billing']['billing_city']['label'] = 'Miasto';
   

    return $fields;
}, 10, 1);

// Remove shipping header
add_filter('woocommerce_shipping_package_name', '__return_empty_string');


add_action('woocommerce_review_order_before_submit', 'custom_checkout_consent_checkboxes', 10);

function custom_checkout_consent_checkboxes() {
    echo '<div class="custom-checkout-terms-checkboxes"><h4 class="checkout-form__title">Zgody i oświadczenia</h4>';

    woocommerce_form_field('terms_and_privacy', [
        'type'      => 'checkbox',
        'class'     => ['form-row privacy'],
        'label'     => 'Zapoznałem się i akceptuję <a href="/regulamin" target="_blank">regulamin sklepu internetowego</a> oraz <a href="/polityka-prywatnosci" target="_blank">politykę prywatności</a>.',
        'required'  => true,
    ]);

    woocommerce_form_field('data_processing_consent', [
        'type'      => 'checkbox',
        'class'     => ['form-row privacy'],
        'label'     => 'Wyrażam zgodę na przetwarzanie moich danych osobowych na potrzeby realizacji zamówienia.',
        'required'  => true,
    ]);

    echo '</div>';
}


add_action('woocommerce_after_checkout_form','checkout_newsletter');
function checkout_newsletter() {
   get_template_part('template-parts/components/newsletter');
}