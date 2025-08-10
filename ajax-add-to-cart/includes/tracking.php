<?php
/**
 * Śledzenie konwersji dla AJAX Add to Cart
 * 
 * @package WC_AJAX_Add_To_Cart
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Śledzenie dodania produktu do koszyka
 * 
 * @param string $cart_item_key Klucz produktu w koszyku
 * @param int $product_id ID produktu
 * @param int $quantity Ilość
 * @param int $variation_id ID wariantu (opcjonalnie)
 * @param array $variation Dane wariantu (opcjonalnie)
 * @param array $cart_item_data Dodatkowe dane produktu (opcjonalnie)
 */
function wc_ajax_cart_tracking($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
    // Dodaj dane dla Google Analytics Enhanced Ecommerce
    wp_enqueue_script('wc-ajax-cart-tracking');
    
    // Pobierz dane produktu
    $product = wc_get_product($variation_id ? $variation_id : $product_id);
    
    if (!$product) {
        return;
    }
    
    $product_name = $product->get_name();
    $product_price = $product->get_price();
    $product_cat = '';
    
    // Pobierz kategorię produktu
    $terms = get_the_terms($product_id, 'product_cat');
    if ($terms && !is_wp_error($terms)) {
        $product_cat = $terms[0]->name;
    }
    
    // Przygotuj dane do śledzenia
    $tracking_data = array(
        'event' => 'add_to_cart',
        'ecommerce' => array(
            'currencyCode' => get_woocommerce_currency(),
            'add' => array(
                'products' => array(
                    array(
                        'name' => $product_name,
                        'id' => $product_id,
                        'price' => $product_price,
                        'variant' => $variation_id ? $product_id : '',
                        'category' => $product_cat,
                        'quantity' => $quantity
                    )
                )
            )
        )
    );
    
    // Dodaj zdarzenie dataLayer dla Google Tag Manager
    wc_enqueue_js("
        if (typeof window.dataLayer !== 'undefined') {
            window.dataLayer.push(" . wp_json_encode($tracking_data) . ");
        }
        
        // Facebook Pixel
        if (typeof fbq !== 'undefined') {
            fbq('track', 'AddToCart', {
                content_ids: ['" . esc_js($product_id) . "'],
                content_type: 'product',
                value: " . esc_js($product_price) . ",
                currency: '" . esc_js(get_woocommerce_currency()) . "'
            });
        }
        
        // Wyzwól zdarzenie dodania produktu do koszyka
        jQuery(document.body).trigger('wc_ajax_cart_event_tracking', [" . wp_json_encode($tracking_data) . "]);
    ");
    
    // Akcja dla integracji z niestandardowymi systemami śledzenia
    do_action('wc_ajax_cart_track_add_to_cart', $product_id, $quantity, $variation_id, $variation, $cart_item_data);
}

/**
 * Inicjalizacja śledzenia konwersji
 */
function wc_ajax_cart_tracking_init() {
    // Tylko na stronach z obsługą koszyka
    if (!is_cart() && !is_checkout() && !is_product() && !is_shop()) {
        return;
    }
    
    // Dodaj wsparcie dla narzędzi śledzenia
    add_action('wp_footer', 'wc_ajax_cart_tracking_scripts', 99);
}
add_action('template_redirect', 'wc_ajax_cart_tracking_init');

/**
 * Dodaj skrypty śledzenia
 */
function wc_ajax_cart_tracking_scripts() {
    ?>
    <script type="text/javascript">
    (function() {
        // Śledzenie zdarzeń dodania do koszyka
        jQuery(document.body).on('wc_ajax_cart_tracked', function(event, trackingData) {
            // Ta funkcja zostanie wywołana po śledzeniu w JavaScript
            // Możemy tu dodać dodatkowe niestandardowe śledzenie
            
            // Przykład wywołania niestandardowego hooka
            if (typeof window.wc_ajax_cart_custom_tracking === 'function') {
                window.wc_ajax_cart_custom_tracking(trackingData);
            }
        });
    })();
    </script>
    <?php
}