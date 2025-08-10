<?php
/**
 * AJAX Add to Cart - Główny plik
 * 
 * @package WC_AJAX_Add_To_Cart
 * @version 1.1.0
 */

if (!defined('ABSPATH')) {
    exit; // Zabezpieczenie przed bezpośrednim dostępem
}

// Definicje globalne
define('WC_AJAX_CART_PATH', get_template_directory() . '/ajax-add-to-cart');
define('WC_AJAX_CART_URL', get_template_directory_uri() . '/ajax-add-to-cart');
define('WC_AJAX_CART_VERSION', '1.1.0');

// Dołącz wymagane pliki
require_once WC_AJAX_CART_PATH . '/includes/init.php';
require_once WC_AJAX_CART_PATH . '/includes/helpers.php';
require_once WC_AJAX_CART_PATH . '/includes/compatibility.php';
require_once WC_AJAX_CART_PATH . '/includes/tracking.php';
require_once WC_AJAX_CART_PATH . '/includes/modal.php';

// Inicjalizacja
add_action('init', 'wc_ajax_cart_init');