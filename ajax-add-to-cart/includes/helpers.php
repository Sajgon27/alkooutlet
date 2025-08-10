<?php
/**
 * Funkcje pomocnicze dla AJAX Add to Cart
 * 
 * @package WC_AJAX_Add_To_Cart
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sprawdza czy produkt wariantowy ma atrybuty z opcją "dowolny"
 * 
 * @param WC_Product_Variable $product Produkt wariantowy
 * @return array Lista atrybutów obsługujących dowolne opcje
 */
function wc_ajax_cart_get_any_attributes($product) {
    if (!$product || !$product->is_type('variable')) {
        return array();
    }
    
    $attributes = $product->get_variation_attributes();
    $any_attributes = array();
    
    if (!empty($attributes)) {
        foreach ($attributes as $attribute_name => $options) {
            // Sprawdź czy atrybut ma ustawione "used_for_variations"
            $attribute_obj = $product->get_attribute_by_name(sanitize_title($attribute_name));
            
            if ($attribute_obj && strpos($attribute_obj->get_options(), '_') !== false) {
                $any_attributes[] = 'attribute_' . sanitize_title($attribute_name);
            }
        }
    }
    
    return $any_attributes;
}

/**
 * Pobiera nazwę produktu na podstawie ID
 * 
 * @param int $product_id ID produktu
 * @return string Nazwa produktu lub domyślna wartość
 */
function wc_ajax_cart_get_product_name($product_id) {
    $product = wc_get_product($product_id);
    
    if ($product) {
        return $product->get_name();
    }
    
    return __('Produkt', 'woocommerce');
}

/**
 * Sprawdza czy jest zainstalowana i aktywna wtyczka
 * 
 * @param string $plugin Ścieżka do pliku wtyczki (np. woocommerce/woocommerce.php)
 * @return bool True jeżeli wtyczka jest aktywna
 */
function wc_ajax_cart_is_plugin_active($plugin) {
    if (!function_exists('is_plugin_active')) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    
    return is_plugin_active($plugin);
}

/**
 * Pobiera ID produktu głównego z ID wariantu
 * 
 * @param int $variation_id ID wariantu
 * @return int ID produktu głównego
 */
function wc_ajax_cart_get_parent_id($variation_id) {
    $variation = wc_get_product($variation_id);
    
    if ($variation && $variation->is_type('variation')) {
        return $variation->get_parent_id();
    }
    
    return 0;
}

/**
 * Zapisuje informacje do logu (tylko w trybie WP_DEBUG)
 * 
 * @param mixed $data Dane do zapisania
 * @param string $type Typ logu (info, error)
 */
function wc_ajax_cart_log($data, $type = 'info') {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('AJAX Add to Cart [' . $type . ']: ' . (is_array($data) || is_object($data) ? print_r($data, true) : $data));
    }
}