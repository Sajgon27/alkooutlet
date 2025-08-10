<?php 
// Start session if it's not already started
add_action('init', 'start_session_for_compare', 1); // priority 1 to run early
function start_session_for_compare() {
    if (!session_id() && !headers_sent()) {
        session_start();
    }
}


// Enqueue scripts dla porównywania
add_action('wp_enqueue_scripts', 'enqueue_compare_scripts');
function enqueue_compare_scripts() {
    wp_enqueue_script('compare-js', get_template_directory_uri() . '/assets/js/compare.js', array('jquery'), '1.0.0', true);
    
    // Przekaż dane do JavaScript
    wp_localize_script('compare-js', 'compareData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('compare_products_nonce'),
        'maxProducts' => 3,
        'currentProducts' => get_compare_products()
    ));
}

// Pobierz produkty z sesji
function get_compare_products() {
    return isset($_SESSION['compare_products']) ? $_SESSION['compare_products'] : array();
}

// Zapisz produkty do sesji
function save_compare_products($products) {
    $_SESSION['compare_products'] = array_slice($products, 0, 3); // Max 3 produkty
}

// AJAX: Aktualizuj produkty w sesji
add_action('wp_ajax_update_compare_products', 'ajax_update_compare_products');
add_action('wp_ajax_nopriv_update_compare_products', 'ajax_update_compare_products');
function ajax_update_compare_products() {
    check_ajax_referer('compare_products_nonce', 'nonce');
    
    $products = isset($_POST['products']) ? array_map('sanitize_text_field', $_POST['products']) : array();
    
    // Walidacja - sprawdź czy produkty istnieją
    $validated_products = array();
    foreach ($products as $product_id) {
        if (get_post_status($product_id) === 'publish' && get_post_type($product_id) === 'product') {
            $validated_products[] = $product_id;
        }
    }
    
    save_compare_products($validated_products);
    
    wp_send_json_success(array('products' => $validated_products));
}


