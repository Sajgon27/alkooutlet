<?php
require_once get_template_directory() . '/inc/single-product.php';
require_once get_template_directory() . '/inc/checkout.php';
require_once get_template_directory() . '/inc/cart.php';
require_once get_template_directory() . '/inc/woo-checkout-customer-type-nip.php';
require_once get_template_directory() . '/inc/map-countries.php';
require_once get_template_directory() . '/ajax-add-to-cart/ajax-add-to-cart.php';
require_once get_template_directory() . '/inc/compare.php';
require_once get_template_directory() . '/inc/shop-archive.php';

function alkooutlet_enqueue_assets()
{
    // SWIPER CSS
    wp_enqueue_style(
        'swiper-css',
        get_template_directory_uri() . '/assets/packages/swiper/swiper-bundle.min.css',
        [],
        '11.2.10'
    );

    // SWIPER JS
    wp_enqueue_script(
        'swiper-js',
        get_template_directory_uri() . '/assets/packages/swiper/swiper-bundle.min.js',
        [],
        '11.2.10',
        true
    );

    // MAIN CSS
    wp_enqueue_style(
        'alkooutlet-style',
        get_template_directory_uri() . '/assets/css/style.css',
        []
    );

    // MAIN JS
    wp_enqueue_script(
        'alkooutlet-script',
        get_template_directory_uri() . '/assets/js/main.js',
        [],
        [],
        true
    );
    wp_enqueue_script(
        'favorites-script',
        get_template_directory_uri() . '/assets/js/favorites.js',
        ['jquery'],
        null,
        true
    );
    if (is_product()) {
        wp_enqueue_script(
            'single-product',
            get_template_directory_uri() . '/assets/js/single-product.js',
            ['jquery', 'swiper-js'],
            null,
            true
        );
    }
    if (is_front_page()) {
        wp_enqueue_script(
            'home-script',
            get_template_directory_uri() . '/assets/js/home.js',
            ['jquery'],
            null,
            true
        );
    }

    if (is_page(324)) {
        wp_enqueue_script(
            'faq-script',
            get_template_directory_uri() . '/assets/js/faq.js',
            ['jquery'],
            null,
            true
        );
    }
    
    // Enqueue shop archive script
    if (is_shop() || is_product_category() || is_product_tag()) {
        wp_enqueue_script(
            'shop-archive-script',
            get_template_directory_uri() . '/assets/js/shop-archive.js',
            ['jquery'],
            null,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'alkooutlet_enqueue_assets');


// Głowny skrypt jako moduł
add_filter('script_loader_tag', function ($tag, $handle, $src) {
    if ('alkooutlet-script' === $handle) {
        return '<script type="module" src="' . esc_url($src) . '"></script>';
    }
    return $tag;
}, 10, 3);




// Rejestracja podstawowych funkcji i menu
add_action('after_setup_theme', function () {
    add_theme_support('woocommerce');
    add_theme_support('menus');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    add_theme_support('title-tag');
    register_nav_menus([
        'primary_desktop' => __('Primary Menu Desktop', 'alkooutlet'),
        'primary_mobile' => __('Primary Menu Mobile', 'alkooutlet'),
        'footer1' => __('Footer column 1', 'alkooutlet'),
        'footer2' => __('Footer column 2', 'alkooutlet'),
        'footer3' => __('Footer column 3', 'alkooutlet')
    ]);
});





// Show 10-word excerpt or shortened description below title
add_action('woocommerce_after_shop_loop_item_title', 'show_excerpt_below_title', 5);
function show_excerpt_below_title()
{
    global $product;
    $excerpt = $product->get_short_description();

    if (empty($excerpt)) {
        $excerpt = $product->get_description();
    }

    // Strip HTML and limit to 10 words
    $excerpt = wp_strip_all_tags($excerpt);
    $words = explode(' ', $excerpt);
    $trimmed = implode(' ', array_slice($words, 0, 6));

    echo '<div class="product-short-excerpt">' . esc_html($trimmed) . '...</div>';
}


// BREADCRUMBS
function basic_wp_breadcrumbs()
{
    if (is_front_page()) return;

    $chevron_icon = ' <svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M4 1L9 5L4 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg> ';

    echo '<nav class="breadcrumbs">';
    echo '<a href="' . home_url() . '">Strona główna</a>';

    // Blog posts page (when static front page is used)
    if (is_home()) {
        $page_for_posts = get_option('page_for_posts');
        if ($page_for_posts) {
            echo $chevron_icon . get_the_title($page_for_posts);
        } else {
            echo $chevron_icon . 'Blog';
        }

        // Single Post
    } elseif (is_single()) {
        $category = get_the_category();
        if (!empty($category)) {
            $cat = $category[0];
            echo $chevron_icon . '<a href="' . get_category_link($cat->term_id) . '">' . esc_html($cat->name) . '</a>';
        }
        echo $chevron_icon . get_the_title();

        // Category archive
    } elseif (is_category()) {
        echo $chevron_icon . 'Kategoria: ' . single_cat_title('', false);

        // Tag archive
    } elseif (is_tag()) {
        echo $chevron_icon . 'Tag: ' . single_tag_title('', false);

        // Author archive
    } elseif (is_author()) {
        echo $chevron_icon . 'Autor: ' . get_the_author();

        // Date archive
    } elseif (is_date()) {
        if (is_day()) {
            echo $chevron_icon . get_the_date();
        } elseif (is_month()) {
            echo $chevron_icon . get_the_date('F Y');
        } elseif (is_year()) {
            echo $chevron_icon . get_the_date('Y');
        }

        // Search results
    } elseif (is_search()) {
        echo $chevron_icon . 'Wyniki wyszukiwania dla: "' . get_search_query() . '"';

        // Page (with parents)
    } elseif (is_page()) {
        $parent_id = wp_get_post_parent_id(get_the_ID());
        if ($parent_id) {
            $breadcrumbs = [];
            while ($parent_id) {
                $page = get_post($parent_id);
                $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
                $parent_id = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            foreach ($breadcrumbs as $crumb) {
                echo $chevron_icon . $crumb;
            }
        }
        echo $chevron_icon . get_the_title();

        // 404 page
    } elseif (is_404()) {
        echo $chevron_icon . '404 - Nie znaleziono strony';
    }

    echo '</nav>';
}

// Zmiana separatora 
add_filter('woocommerce_breadcrumb_defaults', 'custom_woocommerce_breadcrumbs');
function custom_woocommerce_breadcrumbs($defaults)
{
    $defaults['delimiter'] = ' <svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M4 1L9 5L4 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>'; // Change this to your preferred separator
    return $defaults;
}



/**
 * Display product attributes (pojemnosc, moc-alkoholu, kraj) on product cards
 */
function alkooutlet_product_card_attributes() {
    global $product;
    if (!$product) return;

    $attributes_to_display = [
        'moc-alkoholu' => ['icon' => 'procent.svg', 'label' => 'Moc alkoholu'],
        'pojemnosc'    => ['icon' => 'bottle.svg', 'label' => 'Pojemność'],
        'kraj'         => ['icon' => '', 'label' => 'Kraj pochodzenia']
    ];

    $has_attributes = false;
    foreach ($attributes_to_display as $attr_name => $attr_data) {
        $attr_value = $product->get_attribute($attr_name);
        if (!empty($attr_value)) {
            $has_attributes = true;
            break;
        }
    }

    // Only proceed if we have at least one attribute to show
    if (!$has_attributes) return;

    echo '<div class="product-card__attributes">';

    foreach ($attributes_to_display as $attr_name => $attr_data) {
        $attr_value = $product->get_attribute($attr_name);

        if (!empty($attr_value)) {
            echo '<div class="product-card__attribute">';

            if (!empty($attr_data['icon'])) {
                echo '<div class="product-card__attribute-icon">';
                echo '<img src="' . get_template_directory_uri() . '/assets/icons/product/' . esc_attr($attr_data['icon']) . '" alt="' . esc_attr($attr_data['label']) . '">';
                echo '</div>';
            } elseif ($attr_name === 'kraj') {
                // Special handling for country - display flag emoji
                $flag_emoji = country_name_to_emoji_flag($attr_value);
                if (!empty($flag_emoji)) {
                    echo '<div class="product-card__attribute-icon product-card__attribute-icon--flag">';
                    echo $flag_emoji;
                    echo '</div>';
                }
            }

            // Show attribute value with % sign for alcohol strength
            if ($attr_name === 'moc-alkoholu') {
                echo '<span class="product-card__attribute-value">' . esc_html($attr_value) . '%</span>';
            } else {
                echo '<span class="product-card__attribute-value">' . esc_html($attr_value) . '</span>';
            }
            
            echo '</div>';
        }
    }

    echo '</div>';
}
add_action('woocommerce_after_shop_loop_item_title', 'alkooutlet_product_card_attributes', 7);

/**
 * Shortcode to display product attributes anywhere
 * 
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function alkooutlet_product_attributes_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'product_id' => get_the_ID(),
            'attribute' => '', // specific attribute to display, blank for all
        ),
        $atts,
        'product_attributes'
    );
    
    $product_id = $atts['product_id'];
    $product = wc_get_product($product_id);
    
    if (!$product) {
        return '';
    }
    
    ob_start();
    
    // If a specific attribute is requested, only show that one
    if (!empty($atts['attribute'])) {
        $attr_value = $product->get_attribute($atts['attribute']);
        if (!empty($attr_value)) {
            echo '<div class="product-attributes">';
            echo '<div class="product-attribute">';
            echo '<span class="product-attribute__name">' . esc_html(wc_attribute_label($atts['attribute'], $product)) . ': </span>';
            echo '<span class="product-attribute__value">' . esc_html($attr_value) . '</span>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        // Show all attributes in our preferred format
        $product_attributes = $product->get_attributes();
        if (!empty($product_attributes)) {
            echo '<div class="product-attributes">';
            foreach ($product_attributes as $attribute) {
                if ($attribute->get_visible()) {
                    echo '<div class="product-attribute">';
                    echo '<span class="product-attribute__name">' . esc_html(wc_attribute_label($attribute->get_name(), $product)) . ': </span>';
                    echo '<span class="product-attribute__value">' . wp_kses_post(wc_get_product_attribute($product, $attribute->get_name())) . '</span>';
                    echo '</div>';
                }
            }
            echo '</div>';
        }
    }
    
    return ob_get_clean();
}
add_shortcode('product_attributes', 'alkooutlet_product_attributes_shortcode');

// Handle quantity discount functionality
add_filter('woocommerce_add_cart_item_data', 'add_quantity_discount_to_cart_item', 10, 3);
function add_quantity_discount_to_cart_item($cart_item_data, $product_id, $variation_id) {
    if (isset($_POST['discount_info'])) {
        $discount_info = json_decode(stripslashes($_POST['discount_info']), true);
        
        if ($discount_info && isset($discount_info['discount'])) {
            $cart_item_data['quantity_discount'] = $discount_info;
            error_log('Discount added: ' . $discount_info['discount'] . '% for product ' . $product_id);
        }
    }
    return $cart_item_data;
}

// Merge same products with different discount settings
add_filter('woocommerce_add_to_cart_validation', 'merge_same_products_in_cart', 10, 5);
function merge_same_products_in_cart($passed, $product_id, $quantity, $variation_id = 0, $variations = array()) {
    // Only process if validation already passed
    if (!$passed) {
        return $passed;
    }
    
    // Check if this product already exists in cart
    $cart = WC()->cart;
    
    // If cart is empty, allow the item to be added normally
    if ($cart->is_empty()) {
        return $passed;
    }
    
    $existing_cart_item_key = null;
    $existing_quantity = 0;
    
    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        if ($cart_item['product_id'] == $product_id && $cart_item['variation_id'] == $variation_id) {
            $existing_cart_item_key = $cart_item_key;
            $existing_quantity = $cart_item['quantity'];
            break;
        }
    }
    
    if ($existing_cart_item_key) {
        // Calculate new total quantity
        $new_total_quantity = $existing_quantity + $quantity;
        
        // Determine appropriate discount for the new total quantity
        $appropriate_discount = get_discount_for_quantity($new_total_quantity);
        
        // Update the existing cart item
        $cart->set_quantity($existing_cart_item_key, $new_total_quantity);
        
        // Apply appropriate discount based on total quantity
        if ($appropriate_discount > 0) {
            $discount_info = array(
                'discount' => $appropriate_discount,
                'quantity' => $new_total_quantity,
                'minQty' => get_min_qty_for_discount($appropriate_discount),
                'maxQty' => get_max_qty_for_discount($appropriate_discount),
                'productId' => $product_id
            );
            $cart->cart_contents[$existing_cart_item_key]['quantity_discount'] = $discount_info;
        } else {
            // Remove discount if total quantity doesn't qualify
            unset($cart->cart_contents[$existing_cart_item_key]['quantity_discount']);
        }
        
        // Prevent adding as new item
        return false;
    }
    
    return $passed;
}

// Helper function to determine best discount for given quantity
function get_discount_for_quantity($quantity) {
    if ($quantity >= 15) {
        return 15;
    } elseif ($quantity >= 10) {
        return 10;
    } elseif ($quantity >= 5) {
        return 5;
    }
    return 0;
}

// Helper function to get minimum quantity for discount tier
function get_min_qty_for_discount($discount) {
    switch ($discount) {
        case 15:
            return 15;
        case 10:
            return 10;
        case 5:
            return 5;
        default:
            return 1;
    }
}

// Helper function to get maximum quantity for discount tier
function get_max_qty_for_discount($discount) {
    switch ($discount) {
        case 15:
            return 999;
        case 10:
            return 14;
        case 5:
            return 9;
        default:
            return 999;
    }
}

// Apply quantity discount to cart item prices
add_action('woocommerce_before_calculate_totals', 'apply_quantity_discount_to_cart', 20, 1);
function apply_quantity_discount_to_cart($cart) {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        if (isset($cart_item['quantity_discount']) && !empty($cart_item['quantity_discount'])) {
            $discount_info = $cart_item['quantity_discount'];
            $discount_percentage = $discount_info['discount'];
            
            // Get original price
            $original_price = $cart_item['data']->get_regular_price();
            if (empty($original_price)) {
                $original_price = $cart_item['data']->get_price();
            }
            
            // Calculate discounted price
            $discount_amount = ($original_price * $discount_percentage) / 100;
            $discounted_price = $original_price - $discount_amount;
            
            // Set the new price
            $cart_item['data']->set_price($discounted_price);
        }
    }
}

// Helper function to determine best discount for given quantity
function determine_best_discount_for_quantity($quantity, $new_discount = null, $existing_discount = null) {
    // Define discount tiers
    $discount_tiers = [
        ['min' => 15, 'max' => 999, 'discount' => 15],
        ['min' => 10, 'max' => 14, 'discount' => 10],
        ['min' => 5, 'max' => 9, 'discount' => 5],
    ];
    
    // Find the best discount tier for this quantity
    foreach ($discount_tiers as $tier) {
        if ($quantity >= $tier['min'] && $quantity <= $tier['max']) {
            return [
                'productId' => $new_discount['productId'] ?? $existing_discount['productId'] ?? null,
                'discount' => $tier['discount'],
                'minQty' => $tier['min'],
                'maxQty' => $tier['max']
            ];
        }
    }
    
    // No discount applies
    return null;
}

// Display discount info in cart
add_filter('woocommerce_cart_item_name', 'display_quantity_discount_in_cart', 10, 3);
function display_quantity_discount_in_cart($product_name, $cart_item, $cart_item_key) {
    if (isset($cart_item['quantity_discount']) && !empty($cart_item['quantity_discount'])) {
        $discount_info = $cart_item['quantity_discount'];
        $discount_percentage = $discount_info['discount'];
        $product_name .= '<br><small style="color: #28a745; font-weight: 500;">Rabat ilościowy ' . $discount_percentage . '%</small>';
    }
    return $product_name;
}

// Automatically adjust discount when quantity is updated in cart
add_action('woocommerce_after_cart_item_quantity_update', 'handle_quantity_discount_on_update', 10, 4);
function handle_quantity_discount_on_update($cart_item_key, $quantity, $old_quantity, $cart) {
    $cart_item = $cart->cart_contents[$cart_item_key];
    
    // Calculate appropriate discount for new quantity
    $appropriate_discount = get_discount_for_quantity($quantity);
    
    if ($appropriate_discount > 0) {
        // Apply the new discount with all required fields
        $discount_info = array(
            'discount' => $appropriate_discount,
            'quantity' => $quantity,
            'minQty' => get_min_qty_for_discount($appropriate_discount),
            'maxQty' => get_max_qty_for_discount($appropriate_discount),
            'productId' => $cart_item['product_id']
        );
        $cart->cart_contents[$cart_item_key]['quantity_discount'] = $discount_info;
    } else {
        // Remove discount if quantity doesn't qualify
        unset($cart->cart_contents[$cart_item_key]['quantity_discount']);
    }
}

// Keep the discount info persistent but don't apply discount if quantity is wrong
add_action('woocommerce_cart_item_removed', 'cleanup_discount_session_on_remove', 10, 2);
function cleanup_discount_session_on_remove($cart_item_key, $cart) {
    // Optional: Clean up any session data if needed when item is completely removed
}

// Also handle cart loading to merge duplicate products
add_action('woocommerce_cart_loaded_from_session', 'merge_duplicate_products_on_cart_load');
function merge_duplicate_products_on_cart_load($cart) {
    $items_to_merge = [];
    $items_to_remove = [];
    
    // Group items by product_id and variation_id
    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        $product_key = $cart_item['product_id'] . '_' . $cart_item['variation_id'];
        
        if (!isset($items_to_merge[$product_key])) {
            $items_to_merge[$product_key] = [
                'key' => $cart_item_key,
                'quantity' => $cart_item['quantity'],
                'product_id' => $cart_item['product_id']
            ];
        } else {
            // Found duplicate - add quantity to first item
            $items_to_merge[$product_key]['quantity'] += $cart_item['quantity'];
            $items_to_remove[] = $cart_item_key;
        }
    }
    
    // Process merges with automatic discount calculation
    foreach ($items_to_merge as $product_key => $merge_data) {
        if ($merge_data['quantity'] != $cart->cart_contents[$merge_data['key']]['quantity']) {
            // Update quantity
            $cart->set_quantity($merge_data['key'], $merge_data['quantity']);
            
            // Apply appropriate discount for merged quantity
            $appropriate_discount = get_discount_for_quantity($merge_data['quantity']);
            if ($appropriate_discount > 0) {
                $discount_info = array(
                    'discount' => $appropriate_discount,
                    'quantity' => $merge_data['quantity'],
                    'minQty' => get_min_qty_for_discount($appropriate_discount),
                    'maxQty' => get_max_qty_for_discount($appropriate_discount),
                    'productId' => $merge_data['product_id']
                );
                $cart->cart_contents[$merge_data['key']]['quantity_discount'] = $discount_info;
            } else {
                unset($cart->cart_contents[$merge_data['key']]['quantity_discount']);
            }
        }
    }
    
    // Remove duplicate items
    foreach ($items_to_remove as $key_to_remove) {
        $cart->remove_cart_item($key_to_remove);
    }
}

// Add custom data to order items to preserve discount info
add_action('woocommerce_checkout_create_order_line_item', 'add_discount_info_to_order_items', 10, 4);
function add_discount_info_to_order_items($item, $cart_item_key, $values, $order) {
    if (isset($values['quantity_discount'])) {
        $item->add_meta_data('_quantity_discount', $values['quantity_discount']);
    }
}

// Display discount info in order details
add_filter('woocommerce_order_item_name', 'display_discount_in_order', 10, 2);
function display_discount_in_order($item_name, $item) {
    $discount_info = $item->get_meta('_quantity_discount');
    if ($discount_info) {
        $quantity = $item->get_quantity();
        if ($quantity >= $discount_info['minQty'] && $quantity <= $discount_info['maxQty']) {
            $item_name .= '<br><small>Rabat ' . $discount_info['discount'] . '% zastosowany</small>';
        }
    }
    return $item_name;
}





add_action('woocommerce_after_shop_loop_item_title', 'add_favorite_icon_to_product_card', 15);

function add_favorite_icon_to_product_card() {
    global $product;
    ?>
 <div class="fav-icon" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
        <button type="button" class="fav-button" data-product-id="<?php echo esc_attr($product->get_id()); ?>" aria-label="<?php esc_attr_e('Dodaj do ulubionych', 'natuscape'); ?>">
            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/icons/fav.svg" alt="<?php esc_attr_e('Dodaj do ulubionych', 'natuscape'); ?>">
        </button>
    </div>
    <?php
}





