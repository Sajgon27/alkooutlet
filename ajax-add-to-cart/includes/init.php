<?php
/**
 * Inicjalizacja funkcji AJAX Add to Cart
 * 
 * @package WC_AJAX_Add_To_Cart
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Inicjalizacja głównych funkcji
 */
function wc_ajax_cart_init() {
    // Sprawdź czy WooCommerce jest aktywny
    if (!class_exists('WooCommerce')) {
        return;
    }
    
    // Załaduj skrypty i style
    add_action('wp_enqueue_scripts', 'wc_ajax_cart_enqueue_scripts');
    
    // Dodaj obsługę fragmentów AJAX
    add_filter('woocommerce_add_to_cart_fragments', 'wc_ajax_cart_fragments');
    
    // Dodaj szablon powiadomienia
    add_action('wp_footer', 'wc_ajax_cart_notification_template');
    
    // Dodaj obsługę AJAX dla modala wariantów
    add_action('wp_ajax_wc_ajax_get_product_data', 'wc_ajax_get_product_data');
    add_action('wp_ajax_nopriv_wc_ajax_get_product_data', 'wc_ajax_get_product_data');
    
    // Dodaj obsługę AJAX dla dodawania do koszyka
    add_action('wp_ajax_wc_ajax_add_to_cart', 'wc_ajax_add_to_cart');
    add_action('wp_ajax_nopriv_wc_ajax_add_to_cart', 'wc_ajax_add_to_cart');
    
    // Zmodyfikuj przyciski dodawania do koszyka dla produktów wariantowych
    add_filter('woocommerce_loop_add_to_cart_link', 'wc_ajax_modify_add_to_cart_button', 10, 2);
}
add_action('init', 'wc_ajax_cart_init');

/**
 * Ładowanie skryptów i stylów
 */
function wc_ajax_cart_enqueue_scripts() {
    // Rejestracja i ładowanie CSS
    wp_register_style(
        'wc-ajax-cart', 
        WC_AJAX_CART_URL . '/css/ajax-add-to-cart.css',
        array(),
        WC_AJAX_CART_VERSION
    );
    
    // Rejestracja CSS dla modala
    wp_register_style(
        'wc-ajax-cart-modal', 
        WC_AJAX_CART_URL . '/css/ajax-add-to-cart-modal.css',
        array(),
        WC_AJAX_CART_VERSION
    );
    
    // Rejestracja JS
    wp_register_script(
        'wc-ajax-cart', 
        WC_AJAX_CART_URL . '/js/ajax-add-to-cart.js',
        array('jquery', 'wc-add-to-cart'),
        WC_AJAX_CART_VERSION,
        true
    );
    
    // Rejestracja JS dla modala
    wp_register_script(
        'wc-ajax-cart-modal', 
        WC_AJAX_CART_URL . '/js/ajax-add-to-cart-modal.js',
        array('jquery', 'wc-ajax-cart'),
        WC_AJAX_CART_VERSION,
        true
    );
    
    // Parametry dla JavaScript
    wp_localize_script(
        'wc-ajax-cart',
        'wcAjaxCart',
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'wc_ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%'),
            'nonce' => wp_create_nonce('wc-ajax-cart-nonce'),
            'i18n_added' => __('dodano do koszyka', 'woocommerce'),
            'i18n_select_variation' => __('Wybierz wariant produktu', 'woocommerce'),
            'currency' => get_woocommerce_currency(),
            'currency_symbol' => get_woocommerce_currency_symbol(),
            'price_format' => get_woocommerce_price_format(),
            'cart_url' => wc_get_cart_url(),
            'is_cart' => is_cart(),
            'is_single' => is_product(),
            'is_checkout' => is_checkout(),
            'debug' => defined('WP_DEBUG') && WP_DEBUG
        )
    );

    
    // Załaduj zasoby na stronach WooCommerce
    // if (is_product() || is_cart() || is_checkout() || is_shop() || 
    //     is_product_category() || is_product_tag() || 
    //     is_product_taxonomy() || // Dodane dla niestandardowych taksonomii
    //     apply_filters('wc_ajax_cart_enqueue_scripts', false) // Hook dla niestandardowych stron
    // ) {
    //     wp_enqueue_style('wc-ajax-cart');
    //     wp_enqueue_style('wc-ajax-cart-modal');
    //     wp_enqueue_script('wc-ajax-cart');
    //     wp_enqueue_script('wc-ajax-cart-modal');
    // }
    wp_enqueue_style('wc-ajax-cart');
    wp_enqueue_style('wc-ajax-cart-modal');
    wp_enqueue_script('wc-ajax-cart');
    wp_enqueue_script('wc-ajax-cart-modal');
}

/**
 * Dodanie szablonu powiadomienia o dodaniu do koszyka
 */
function wc_ajax_cart_notification_template() {
    // Tylko jeśli skrypt jest załadowany
    if (!wp_script_is('wc-ajax-cart', 'enqueued')) {
        return;
    }
    ?>
    <div class="wc-ajax-notification" id="wc-ajax-notification" style="display:none;">
        <div class="wc-ajax-notification-content">
            <span class="message"></span>
        </div>
    </div>
    <?php
}

/**
 * Dodanie fragmentów do aktualizacji AJAX
 */
function wc_ajax_cart_fragments($fragments) {
    // Aktualizacja liczby przedmiotów w koszyku
    ob_start();
    ?>
    <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    <?php
    $fragments['.cart-count'] = ob_get_clean();
    
    // Aktualizacja łącznej wartości koszyka
    ob_start();
    ?>
    <span class="cart-total"><?php echo WC()->cart->get_cart_total(); ?></span>
    <?php
    $fragments['.cart-total'] = ob_get_clean();
    
    return $fragments;
}

/**
 * Modyfikacja przycisku dodawania do koszyka dla produktów wariantowych
 */
function wc_ajax_modify_add_to_cart_button($html, $product) {
    if (!$product->is_type('variable')) {
        return $html;
    }
    
    // Dodaj klasę typu produktu
    $html = str_replace('product-card__button', 'product-card__button product-type-variable', $html);
    
    // Dodaj atrybut data-product_id, jeśli nie istnieje
    if (strpos($html, 'data-product_id') === false) {
        $html = str_replace('<a href', '<a data-product_id="' . esc_attr($product->get_id()) . '" href', $html);
    }
    
    // Usuń atrybut href dla produktów wariantowych, aby uniknąć przechwytywania przez GTM
    $html = preg_replace('/href="[^"]*"/', '', $html);
    
    return $html;
}

/**
 * Obsługa AJAX dla pobierania danych produktu
 */
function wc_ajax_get_product_data() {
    // Sprawdź nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wc-ajax-cart-nonce')) {
        wp_send_json_error(array('message' => 'Invalid security token'));
        wp_die();
    }
    
    // Sprawdź ID produktu
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        wp_send_json_error(array('message' => 'Product ID is required'));
        wp_die();
    }
    
    $product_id = absint($_POST['product_id']);
    $product = wc_get_product($product_id);
    
    if (!$product) {
        wp_send_json_error(array('message' => 'Product not found'));
        wp_die();
    }
    
    // Przygotuj dane produktu
    $product_data = array(
        'title' => $product->get_name(),
        'price_html' => $product->get_price_html(),
        'image' => wp_get_attachment_url($product->get_image_id()),
        'product_url' => get_permalink($product_id),
        'attributes' => array(),
        'variations' => array(),
        'gallery' => array()
    );
    
    // Dodaj obrazy galerii
    $attachment_ids = $product->get_gallery_image_ids();
    if (!empty($attachment_ids)) {
        foreach ($attachment_ids as $attachment_id) {
            $product_data['gallery'][] = array(
                'thumbnail' => wp_get_attachment_image_url($attachment_id, 'thumbnail'),
                'full' => wp_get_attachment_image_url($attachment_id, 'full')
            );
        }
    }
    
    // Dodaj główny obraz do galerii
    array_unshift($product_data['gallery'], array(
        'thumbnail' => wp_get_attachment_image_url($product->get_image_id(), 'thumbnail'),
        'full' => wp_get_attachment_image_url($product->get_image_id(), 'full')
    ));
    
    // Jeśli to produkt wariantowy, dodaj informacje o wariantach
    if ($product->is_type('variable')) {
        $attributes = $product->get_variation_attributes();
        
        // Przygotuj atrybuty
        foreach ($attributes as $attribute_name => $options) {
            $attribute_key = sanitize_title($attribute_name);
            $attribute_label = wc_attribute_label($attribute_name);
            
            $attribute_options = array();
            foreach ($options as $option) {
                $term = get_term_by('slug', $option, $attribute_name);
                $option_label = $term ? $term->name : $option;
                
                $attribute_options[] = array(
                    'label' => $option_label,
                    'value' => $option
                );
            }
            
            $product_data['attributes']['attribute_' . $attribute_key] = array(
                'label' => $attribute_label,
                'options' => $attribute_options
            );
        }
        
        // Przygotuj warianty
        $variations = $product->get_available_variations();
        
        foreach ($variations as $variation) {
            $variation_id = $variation['variation_id'];
            $variation_obj = wc_get_product($variation_id);
            
            if ($variation_obj) {
                // Dodaj informację o dostępności wariantu
                $is_in_stock = $variation_obj->is_in_stock();
                $stock_status = $variation_obj->get_stock_status(); // 'instock', 'outofstock', 'onbackorder'
                
                $product_data['variations'][$variation_id] = array(
                    'price_html' => $variation_obj->get_price_html(),
                    'image' => $variation['image']['full_src'],
                    'sku' => $variation_obj->get_sku(),
                    'attributes' => $variation['attributes'],
                    'is_in_stock' => $is_in_stock,
                    'stock_status' => $stock_status,
                    'availability_html' => wc_get_stock_html($variation_obj)
                );
            }
        }
    }
    
    wp_send_json_success($product_data);
    wp_die();
}

/**
 * Obsługa AJAX dla dodawania do koszyka
 */
function wc_ajax_add_to_cart() {
    // Sprawdź nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wc-ajax-cart-nonce')) {
        wp_send_json_error(array('message' => 'Invalid security token'));
        wp_die();
    }
    
    // Sprawdź ID produktu
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        wp_send_json_error(array('message' => 'Product ID is required'));
        wp_die();
    }
    
    $product_id = absint($_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;
    $variation_id = isset($_POST['variation_id']) ? absint($_POST['variation_id']) : 0;
    $variation = isset($_POST['variation']) ? $_POST['variation'] : array();
    
    /*
// Validate before adding
$passed_validation = apply_filters(
    'woocommerce_add_to_cart_validation',
    true,
    $product_id,
    $quantity,
    $variation_id,
    $variation
);

if ( ! $passed_validation ) {
    wp_send_json_error( array(
        'message' => __( 'Cannot add that quantity to the cart — not enough stock.', 'woocommerce' )
    ) );
    wp_die();
}

*/
    // Próba dodania do koszyka
    $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation);
    
    if ($cart_item_key) {
        do_action('woocommerce_ajax_added_to_cart', $product_id);
        
        // Pobierz fragmenty koszyka
        $fragments = apply_filters('woocommerce_add_to_cart_fragments', array());
        
        // Śledź dodanie do koszyka
        do_action('wc_ajax_cart_tracking', $cart_item_key, $product_id, $quantity, $variation_id, $variation, array());
        
        wp_send_json_success(array(
            'fragments' => $fragments,
            'cart_hash' => WC()->cart->get_cart_hash()
        ));
    } else {
        $product = wc_get_product($product_id);
        
        // Sprawdź czy produkt wymaga wyboru opcji
        if ($product && $product->is_type('variable') && empty($variation_id)) {
            wp_send_json_error(array(
                'message' => __('Wybierz wariant produktu', 'woocommerce'),
                'code' => 'variation_required'
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Nie udało się dodać produktu do koszyka', 'woocommerce')
            ));
        }
    }
    
    wp_die();
}