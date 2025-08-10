<?php
/**
 * Kompatybilność z różnymi wtyczkami
 * 
 * @package WC_AJAX_Add_To_Cart
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Inicjalizacja obsługi kompatybilności
add_action('init', 'wc_ajax_cart_compatibility_init', 20);

/**
 * Inicjalizacja obsługi kompatybilności z wtyczkami
 */
function wc_ajax_cart_compatibility_init() {
    // WooCommerce Advanced Product Fields (Studio Wombat)
    if (wc_ajax_cart_is_plugin_active('woocommerce-advanced-product-fields/woocommerce-advanced-product-fields-pro.php') || 
        wc_ajax_cart_is_plugin_active('woocommerce-advanced-product-fields/woocommerce-advanced-product-fields.php')) {
        add_filter('woocommerce_add_cart_item_data', 'wc_ajax_cart_wapf_support', 10, 3);
    }
    
    // WPDesk Flexible Product Fields
    if (wc_ajax_cart_is_plugin_active('flexible-product-fields/flexible-product-fields.php') || 
        wc_ajax_cart_is_plugin_active('flexible-product-fields-pro/flexible-product-fields-pro.php')) {
        add_filter('woocommerce_add_cart_item_data', 'wc_ajax_cart_wpdesk_fpf_support', 10, 3);
    }
    
    // WooCommerce Product Bundles
    if (wc_ajax_cart_is_plugin_active('woocommerce-product-bundles/woocommerce-product-bundles.php')) {
        add_filter('woocommerce_add_to_cart_validation', 'wc_ajax_cart_bundles_support', 10, 5);
    }
    
    // YITH WooCommerce Product Add-ons
    if (wc_ajax_cart_is_plugin_active('yith-woocommerce-product-add-ons/init.php')) {
        add_filter('woocommerce_add_cart_item_data', 'wc_ajax_cart_yith_addons_support', 10, 3);
    }
}

/**
 * Wsparcie dla WooCommerce Advanced Product Fields
 */
function wc_ajax_cart_wapf_support($cart_item_data, $product_id, $variation_id) {
    if (isset($_POST['wapf'])) {
        // Dane pól WAPF są już obsługiwane przez wtyczkę
        // Możemy dodać dodatkową logikę jeśli potrzeba
    }
    
    return $cart_item_data;
}

/**
 * Wsparcie dla WPDesk Flexible Product Fields
 */
function wc_ajax_cart_wpdesk_fpf_support($cart_item_data, $product_id, $variation_id) {
    if (isset($_POST['fpf'])) {
        // Dane pól FPF są już obsługiwane przez wtyczkę
        // Możemy dodać dodatkową logikę jeśli potrzeba
    }
    
    return $cart_item_data;
}

/**
 * Wsparcie dla WooCommerce Product Bundles
 */
function wc_ajax_cart_bundles_support($passed, $product_id, $quantity, $variation_id = '', $variations = array()) {
    // Sprawdź czy to produkt z zestawem
    $product = wc_get_product($product_id);
    
    if ($product && $product->is_type('bundle')) {
        // Zestawy są obsługiwane przez standardowy mechanizm WooCommerce
        // Tutaj można dodać dodatkową logikę jeśli potrzeba
    }
    
    return $passed;
}

/**
 * Wsparcie dla YITH WooCommerce Product Add-ons
 */
function wc_ajax_cart_yith_addons_support($cart_item_data, $product_id, $variation_id) {
    // YITH Add-ons używa różnych prefixów w zależności od wersji
    $addons_prefixes = array('yith_wapo_', 'yith_wapo_group_');
    
    foreach ($_POST as $key => $value) {
        foreach ($addons_prefixes as $prefix) {
            if (strpos($key, $prefix) === 0) {
                // Dane dodatków YITH są już obsługiwane przez wtyczkę
                // Możemy dodać dodatkową logikę jeśli potrzeba
            }
        }
    }
    
    return $cart_item_data;
}