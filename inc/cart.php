<?php

/**
 * Cart functionality
 * 
 * Handles AJAX functionality for cart operations
 * 
 * @package SBS
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register AJAX actions
 */
function sbs_register_cart_ajax()
{
    add_action('wp_ajax_sbs_update_cart_item', 'sbs_update_cart_item');
    add_action('wp_ajax_nopriv_sbs_update_cart_item', 'sbs_update_cart_item');

    add_action('wp_ajax_sbs_remove_cart_item', 'sbs_remove_cart_item');
    add_action('wp_ajax_nopriv_sbs_remove_cart_item', 'sbs_remove_cart_item');

    add_action('wp_ajax_sbs_apply_coupon', 'sbs_apply_coupon');
    add_action('wp_ajax_nopriv_sbs_apply_coupon', 'sbs_apply_coupon');

    add_action('wp_ajax_sbs_remove_coupon', 'sbs_remove_coupon');
    add_action('wp_ajax_nopriv_sbs_remove_coupon', 'sbs_remove_coupon');
}
add_action('init', 'sbs_register_cart_ajax');

/**
 * Enqueue cart scripts
 */
function sbs_enqueue_cart_scripts()
{
    if (is_cart()) {
        wp_enqueue_script('sbs-cart', get_template_directory_uri() . '/assets/js/cart.js', array('jquery'), '1.0.0', true);

        // Localize the script with cart parameters
        wp_localize_script('sbs-cart', 'wc_cart_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'update_shipping_method_nonce' => wp_create_nonce('woocommerce-cart'),
            'cart_url' => wc_get_cart_url(),
            'is_cart' => is_cart(),
            'cart_reload_on_error' => get_option('woocommerce_cart_redirect_after_error') === 'yes'
        ));
    }
}
add_action('wp_enqueue_scripts', 'sbs_enqueue_cart_scripts');

/**
 * Update cart item quantity
 */
function sbs_update_cart_item()
{
    check_ajax_referer('woocommerce-cart', 'security');

    $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
    $qty = absint($_POST['qty']);

    if (0 === $qty) {
        $qty = 1;
    }

    // Update cart item
    $cart_item_data = WC()->cart->get_cart_item($cart_item_key);

    if ($cart_item_data) {
        WC()->cart->set_quantity($cart_item_key, $qty, true);

        $response = array(
            'success' => true,
            'data' => array(
                'fragments' => apply_filters('woocommerce_add_to_cart_fragments', array(
                    '.woocommerce-cart-form' => sbs_get_cart_form(),
                    '.cart-summary' => sbs_get_cart_summary(),
                )),
                'cart_hash' => WC()->cart->get_cart_hash(),
                'cart_is_empty' => WC()->cart->is_empty(),
            )
        );

        wp_send_json($response);
    } else {
        wp_send_json(array(
            'success' => false,
            'data' => __('Item not found in cart', 'sbs')
        ));
    }

    wp_die();
}

/**
 * Remove cart item
 */
function sbs_remove_cart_item()
{
    check_ajax_referer('woocommerce-cart', 'security');

    $cart_item_key = sanitize_text_field($_POST['cart_item_key']);

    // Remove cart item
    if (WC()->cart->remove_cart_item($cart_item_key)) {
        $response = array(
            'success' => true,
            'data' => array(
                'fragments' => apply_filters('woocommerce_add_to_cart_fragments', array(
                    '.woocommerce-cart-form' => sbs_get_cart_form(),
                    '.cart-summary' => sbs_get_cart_summary(),
                )),
                'cart_hash' => WC()->cart->get_cart_hash(),
                'cart_is_empty' => WC()->cart->is_empty(),
            )
        );

        wp_send_json($response);
    } else {
        wp_send_json(array(
            'success' => false,
            'data' => __('Item could not be removed', 'sbs')
        ));
    }

    wp_die();
}

/**
 * Apply coupon
 */
function sbs_apply_coupon()
{
    check_ajax_referer('woocommerce-cart', 'security');

    $coupon_code = sanitize_text_field($_POST['coupon_code']);
    if (WC()->cart->apply_coupon($coupon_code)) {
        $response = array(
            'success' => true,
            'data' => array(
                'fragments' => apply_filters('woocommerce_add_to_cart_fragments', array(
                    '.woocommerce-cart-form' => sbs_get_cart_form(),
                    '.cart-summary' => sbs_get_cart_summary(),
                )),
                'cart_hash' => WC()->cart->get_cart_hash(),
                'cart_is_empty' => WC()->cart->is_empty(),
                'message' => sprintf('Kod rabatowy "%s" został pomyślnie zastosowany', $coupon_code),
            )
        );
        wc_clear_notices();
        wp_send_json($response);
    } else {
        // Get the last error message
        $notices = wc_get_notices('error');
        $error = isset($notices[0]) ? $notices[0]['notice'] : 'Kod rabatowy nie mógł zostać zastosowany';

        // Try to improve the message
        if (strpos($error, 'does not exist') !== false || strpos($error, 'nie istnieje') !== false) {
            $error = sprintf('Kod rabatowy "%s" nie istnieje', $coupon_code);
        }
        wc_clear_notices();
        wp_send_json(array(
            'success' => false,
            'data' => $error
        ));
    }
    wc_clear_notices();
    wp_die();
}

/**
 * Remove coupon
 */
function sbs_remove_coupon()
{
    check_ajax_referer('woocommerce-cart', 'security');

    $coupon_code = sanitize_text_field($_POST['coupon_code']);

    if (WC()->cart->remove_coupon($coupon_code)) {
        $response = array(
            'success' => true,
            'data' => array(
                'fragments' => apply_filters('woocommerce_add_to_cart_fragments', array(
                    '.woocommerce-cart-form' => sbs_get_cart_form(),
                    '.cart-summary' => sbs_get_cart_summary(),
                )),
                'cart_hash' => WC()->cart->get_cart_hash(),
                'cart_is_empty' => WC()->cart->is_empty(),
            )
        );
        wc_clear_notices();
        wp_send_json($response);
    } else {
        wc_clear_notices();
        wp_send_json(array(
            'success' => false,
            'data' => __('Kupon nie mógł zostać usunięty', 'sbs')
        ));
    }

    wp_die();
}

/**
 * Get cart form HTML
 */
function sbs_get_cart_form()
{
    ob_start();
    wc_get_template('cart/cart-form.php');
    return ob_get_clean();
}

/**
 * Get cart summary HTML
 */
function sbs_get_cart_summary()
{
    ob_start();
    wc_get_template('cart/cart-summary.php');
    return ob_get_clean();
}

/**
 * Add custom cart templates to woocommerce template path
 */
function sbs_add_cart_template_path($template, $template_name)
{
    if ($template_name === 'cart/cart-form.php' || $template_name === 'cart/cart-summary.php') {
        $template = get_template_directory() . '/woocommerce/' . $template_name;
    }
    return $template;
}
add_filter('woocommerce_locate_template', 'sbs_add_cart_template_path', 10, 2);

/**
 * Add custom quantity template
 */
function sbs_quantity_input_template($args, $product)
{
    if (is_cart()) {
        $args['max_value'] = $product->get_max_purchase_quantity();
        $args['min_value'] = 1;
        $args['input_value'] = $args['input_value'];
        $args['classes'] = array('qty');
        $args['input_name'] = $args['input_name'];
    }

    return $args;
}


remove_action('woocommerce_before_cart', 'woocommerce_output_all_notices', 10);




add_action('woocommerce_after_cart', 'cart_show_recommendations');

function cart_show_recommendations()
{
 
  


    $icon_url    = get_template_directory_uri() . '/assets/icons/product/white-bottle.svg';
    $small_text  = 'Warto spróbować';
    $title       = 'Wybrane z myślą o Tobie';
    $description = 'Zobacz produkty, które równie często trafiają na stoły koneserów. Rekomendacje inspirowane Twoim wyborem – unikalne, aromatyczne i warte poznania.';

    // To change later
    $product_ids = [179,180,181,57,56,53];

    // Only show the slider if we have products to display
    if (!empty($product_ids)) {
        set_query_var('icon', esc_url($icon_url));
        set_query_var('small_text', $small_text);
        set_query_var('title', $title);
        set_query_var('description', $description);
        set_query_var('product_ids', $product_ids);
        set_query_var('theme', 'dark');

        get_template_part('template-parts/components/products.slider');
    }
}