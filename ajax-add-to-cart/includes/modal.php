<?php
/**
 * Funkcje dla modalu wariantów produktów
 * 
 * @package WC_AJAX_Add_To_Cart
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dodanie szablonu modalu do stopki strony
 */
function wc_ajax_cart_modal_template() {
    // Tylko jeśli skrypt modalu jest załadowany
    if (!wp_script_is('wc-ajax-cart-modal', 'enqueued')) {
        return;
    }
    
    // Modal jest generowany dynamicznie przez JavaScript
}
add_action('wp_footer', 'wc_ajax_cart_modal_template');

/**
 * Tworzenie customowego przycisku dodawania do koszyka dla loop produktów
 * 
 * @param WC_Product $product Obiekt produktu
 * @param array $args Dodatkowe argumenty
 * @return string HTML przycisku
 */
/*
function wc_ajax_cart_loop_add_to_cart_button($product, $args = array()) {
    if (!$product) {
        return '';
    }
    
    $defaults = array(
        'quantity' => 1,
        'class' => 'product-card__button',
        'attributes' => array(
            'data-product_id' => $product->get_id(),
            'data-product_sku' => $product->get_sku(),
            'aria-label' => $product->add_to_cart_description(),
            'rel' => 'nofollow'
        ),
    );
    
    $args = wp_parse_args($args, $defaults);
    
    // Sprawdź typ produktu i dostępność
    $type = $product->get_type();
    $availability = $product->get_availability();
    $is_purchasable = $product->is_purchasable();
    $is_in_stock = $product->is_in_stock();
    
    // Jeśli produkt nie jest w magazynie lub nie jest dostępny do kupienia, zwróć komunikat
    if (!$is_in_stock || !$is_purchasable) {
        $button_text = $availability['availability'] ? esc_html($availability['availability']) : esc_html__('Niedostępny', 'woocommerce');
        $button_class = 'product-card__button product-card__button--disabled';
        
        return sprintf(
            '<a class="%s" disabled>
                <img src="%s" class="product-card__cart-icon" alt="Koszyk" width="16" height="16">
                <span class="product-card__button-text--desktop">%s</span>
                <span class="product-card__button-text--mobile">%s</span>
            </a>',
            esc_attr($button_class),
            esc_url(get_template_directory_uri() . '/assets/img/cart.svg'),
            esc_html($button_text),
            esc_html($button_text)
        );
    }
    
    // Dostosuj klasę przycisku w zależności od typu produktu
    if ($type === 'variable') {
        $args['class'] .= ' product-type-variable';
        $button_text_desktop = __('Wybierz opcje', 'woocommerce');
        $button_text_mobile = __('Opcje', 'woocommerce');
        $args['attributes']['aria-label'] = __('Wybierz opcje produktu', 'woocommerce');
    } else {
        $args['class'] .= ' ajax_add_to_cart add_to_cart_button';
        $button_text_desktop = __('Dodaj do koszyka', 'woocommerce');
        $button_text_mobile = __('Do koszyka', 'woocommerce');
    }
    
    // Generuj HTML przycisku
    $button_html = sprintf(
        '<a href="%s" data-quantity="%s" class="%s" %s>
            <img src="%s" class="product-card__cart-icon" alt="Koszyk" width="16" height="16">
            <span class="product-card__button-text--desktop">%s</span>
            <span class="product-card__button-text--mobile">%s</span>
        </a>',
        esc_url($product->add_to_cart_url()),
        esc_attr($args['quantity']),
        esc_attr($args['class']),
        isset($args['attributes']) ? wc_implode_html_attributes($args['attributes']) : '',
        esc_url(get_template_directory_uri() . '/assets/img/cart.svg'),
        esc_html($button_text_desktop),
        esc_html($button_text_mobile)
    );
    
    return apply_filters('wc_ajax_cart_loop_add_to_cart_button_html', $button_html, $product, $args);
}
*/
/**
 * Zastąpienie standardowego przycisku dodawania do koszyka w loopach produktów
 */
/*
function wc_ajax_cart_replace_loop_add_to_cart() {
    // Usuń standardową akcję
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
    
    // Dodaj naszą customową akcję
    add_action('woocommerce_after_shop_loop_item', 'wc_ajax_cart_template_loop_add_to_cart', 10);
}
add_action('init', 'wc_ajax_cart_replace_loop_add_to_cart');
*/
/**
 * Customowa funkcja wyświetlania przycisku dodawania do koszyka w loopach
 */
/*
function wc_ajax_cart_template_loop_add_to_cart() {
    global $product;
    
    echo wc_ajax_cart_loop_add_to_cart_button($product);
}
*/
/**
 * Obsługa AJAX dla pobierania obrazka wariantu
 */
function wc_ajax_get_variation_image() {
    // Sprawdź nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wc-ajax-cart-nonce')) {
        wp_send_json_error(array('message' => 'Invalid security token'));
        wp_die();
    }
    
    // Sprawdź ID wariantu
    if (!isset($_POST['variation_id']) || empty($_POST['variation_id'])) {
        wp_send_json_error(array('message' => 'Variation ID is required'));
        wp_die();
    }
    
    $variation_id = absint($_POST['variation_id']);
    $variation = wc_get_product($variation_id);
    
    if (!$variation || !$variation->is_type('variation')) {
        wp_send_json_error(array('message' => 'Invalid variation'));
        wp_die();
    }
    
    // Pobierz URL obrazka wariantu
    $image_id = $variation->get_image_id();
    $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
    
    // Pobierz cenę wariantu
    $price_html = $variation->get_price_html();
    
    wp_send_json_success(array(
        'image_url' => $image_url,
        'price_html' => $price_html
    ));
    wp_die();
}
add_action('wp_ajax_wc_ajax_get_variation_image', 'wc_ajax_get_variation_image');
add_action('wp_ajax_nopriv_wc_ajax_get_variation_image', 'wc_ajax_get_variation_image');