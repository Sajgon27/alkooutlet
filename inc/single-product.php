<?php

/**
 * Single Product Template Modifications
 * 
 * @package alkooutlet
 */

require_once get_template_directory() . '/inc/map-countries.php';
// Remove default WooCommerce elements
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);



// Add custom breadcrumbs
add_action('woocommerce_single_product_summary', 'alko_single_product_breadcrumbs', 1);
function alko_single_product_breadcrumbs()
{
    global $product;
?>
    <div class="product__summary-top">
        <?php woocommerce_breadcrumb(); ?>
        <div class="product__actions">
            <div class="fav-icon" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
                <button type="button" id="fav-button-desktop" class="fav-button" data-product-id="<?php echo esc_attr($product->get_id()); ?>" aria-label="<?php esc_attr_e('Dodaj do ulubionych', 'alko'); ?>">
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/icons/fav.svg" alt="<?php esc_attr_e('Dodaj do ulubionych', 'alko'); ?>">
                </button>
            </div>
      
        </div>
    </div>

<?php
}


add_filter( 'woocommerce_get_breadcrumb', function( $crumbs, $breadcrumb ) {
    if ( is_product() ) {
        $product = wc_get_product( get_the_ID() );

        // Always keep Home
        $new_crumbs   = [];
        $new_crumbs[] = $crumbs[0]; // Home

        // Add "Shop" page
        $shop_page_id = wc_get_page_id( 'shop' );
        if ( $shop_page_id > 0 ) {
            $new_crumbs[] = [ get_the_title( $shop_page_id ), get_permalink( $shop_page_id ) ];
        }

        // Add Product title (no link)
        $new_crumbs[] = [ $product->get_name() ];

        return $new_crumbs;
    }

    return $crumbs;
}, 10, 2 );



// Add custom header with title, wishlist and share icons
add_action('woocommerce_single_product_summary', 'alko_single_product_header', 5);
function alko_single_product_header()
{
    global $product;
    if (!$product) return;
?>
    <div class="product__header">
        <h1 class="product__title"><?php echo esc_html($product->get_name()); ?></h1>

    </div>
<?php
}

// Add product attributes display
add_action('woocommerce_single_product_summary', 'alko_single_product_attributes', 7);
function alko_single_product_attributes()
{
    global $product;
    if (!$product) return;

    $attributes = [
        'category'         => null,
        'moc-alkoholu'     => ['icon' => 'procent.svg', 'label' => 'Moc alkoholu'],
        'pojemnosc'        => ['icon' => 'bottle.svg', 'label' => 'Pojemność'],
        'kraj'             => ['icon' => '', 'label' => 'Kraj pochodzenia'],
        'smak-wytrawnosc'  => ['icon' => 'glass.svg', 'label' => 'Smak/Wytrawność']
    ];

    echo '<div class="product__attributes">';

    // 1. Display main category (top-level)
    $terms = get_the_terms($product->get_id(), 'product_cat');
    if ($terms && !is_wp_error($terms)) {
        // Find top-level category
        $top_level_cat = null;
        foreach ($terms as $term) {
            $ancestors = get_ancestors($term->term_id, 'product_cat');
            if (empty($ancestors)) {
                $top_level_cat = $term;
                break;
            }
        }

        // If no top-level found, use the first category
        if (!$top_level_cat && !empty($terms)) {
            $top_level_cat = $terms[0];
        }

        if ($top_level_cat) {
            echo '<div class="product__attribute product__attribute--category">';
            echo '<span class="product__attribute-value">' . esc_html($top_level_cat->name) . '</span>';
            echo '</div>';
        }
    }

    // 2. Display other attributes
    foreach ($attributes as $attr_name => $attr_data) {
        if ($attr_name === 'category') continue; // Already handled above

        $attr_value = $product->get_attribute($attr_name);

        if (!empty($attr_value)) {
            echo '<div class="product__attribute">';

            if (!empty($attr_data['icon'])) {
                echo '<div class="product__attribute-icon">';
                echo '<img src="' . get_template_directory_uri() . '/assets/icons/product/' . esc_attr($attr_data['icon']) . '" alt="' . esc_attr($attr_data['label']) . '">';
                echo '</div>';
            } elseif ($attr_name === 'kraj') {
                // Special handling for country - display flag emoji
                $flag_emoji = country_name_to_emoji_flag($attr_value);
                if (!empty($flag_emoji)) {
                    echo '<div class="product__attribute-icon product__attribute-icon--flag">';
                    echo $flag_emoji;
                    echo '</div>';
                }
            }

            // Show attribute value with % sign for alcohol strength
            if ($attr_name === 'moc-alkoholu') {
                echo '<span class="product__attribute-value">' . esc_html($attr_value) . '%</span>';
            } else {
                echo '<span class="product__attribute-value">' . esc_html($attr_value) . '</span>';
            }
            echo '</div>';
        }
    }

    echo '</div>';

    // Add excerpt or first 12 words from product content
    $short_description = $product->get_short_description();
    if (!empty($short_description)) {
        echo '<div class="product__excerpt">' . wp_kses_post($short_description) . '</div>';
    } else {
        // If no excerpt available, show first 12 words from product content
        $content = $product->get_description();
        if (!empty($content)) {
            $content = wp_strip_all_tags($content);
            $words = explode(' ', $content);
            $excerpt = implode(' ', array_slice($words, 0, 12));
            if (count($words) > 12) {
                $excerpt .= '...';
            }
            echo '<div class="product__excerpt">' . esc_html($excerpt) . '</div>';
        }
    }
}

// Remove default excerpt since we're adding our own
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);

// Re-position product price
//remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
//add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 25);

// Re-position add to cart button
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);

// Add feature blocks below add to cart button
add_action('woocommerce_share', 'alko_product_discount_cards', 35);
function alko_product_discount_cards()
{
    global $product;
    $price = $product->get_price();
    $product_id = $product->get_id();
    
    // Calculate savings for each tier
    $savings_5 = number_format($price * 0.05, 2);
    $savings_10 = number_format($price * 0.10, 2);
    $savings_15 = number_format($price * 0.15, 2);
    ?>
    <div class="product__discount-cards" data-product-id="<?php echo esc_attr($product_id); ?>">
        <div class="swiper-wrapper">
            <div class="discount-card swiper-slide" data-quantity="5" data-discount="5" data-min-qty="5" data-max-qty="9" data-product-id="<?php echo esc_attr($product_id); ?>">
                <div class="discount-card__quantity">5 szt.</div>
                <div class="discount-card__discount">RABAT 5%</div>
                <div class="discount-card__text">
                    Kup więcej<br>
                    i zapłać mniej!
                </div>
                <div class="discount-card__savings">
                    Oszczędzasz <span class="savings-amount"><?php echo $savings_5; ?> zł</span>
                </div>
                <button type="button" class="discount-card__button button button--primary">Wybierz</button>
            </div>

            <div class="discount-card swiper-slide" data-quantity="10" data-discount="10" data-min-qty="10" data-max-qty="14" data-product-id="<?php echo esc_attr($product_id); ?>">
                <div class="discount-card__quantity">10 szt.</div>
                <div class="discount-card__discount">RABAT 10%</div>
                <div class="discount-card__text">
                    Kup więcej<br>
                    i zapłać mniej!
                </div>
                <div class="discount-card__savings">
                    Oszczędzasz <span class="savings-amount"><?php echo $savings_10; ?> zł</span>
                </div>
                <button type="button" class="discount-card__button button button--primary">Wybierz</button>
            </div>

            <div class="discount-card swiper-slide" data-quantity="15" data-discount="15" data-min-qty="15" data-max-qty="999" data-product-id="<?php echo esc_attr($product_id); ?>">
                <div class="discount-card__quantity">15 szt.</div>
                <div class="discount-card__discount">RABAT 15%</div>
                <div class="discount-card__text">
                    Kup więcej<br>
                    i zapłać mniej!
                </div>
                <div class="discount-card__savings">
                    Oszczędzasz <span class="savings-amount"><?php echo $savings_15; ?> zł</span>
                </div>
                <button type="button" class="discount-card__button button button--primary">Wybierz</button>
            </div>
        </div>
    </div>
    <?php
}

add_action('woocommerce_share', 'alko_product_feature_blocks', 40);
function alko_product_feature_blocks()
{
    global $product;
?>
    <div class="product__features">
        <div class="product__feature">
            <div class="product__feature-icon">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/product/cup.svg" alt="Cup icon">
            </div>
            <div class="product__feature-content">
                <h6 class="product__feature-title">BOGATY WYBÓR Z CAŁEGO ŚWIATA</h6>
                <p class="product__feature-text">Znajdź swój ulubiony smak wśród klasyków i nowości z różnych zakątków globu</p>
            </div>
        </div>

        <div class="product__feature">
            <div class="product__feature-icon">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/product/truck.svg" alt="Delivery icon">
            </div>
            <div class="product__feature-content">
                <h6 class="product__feature-title">SZYBKA WYSYŁKA BEZ ZBĘDNEJ ZWŁOKI</h6>
                <p class="product__feature-text">Zamówienia pakujemy od razu i dostarczamy już w ciągu 24 godzin</p>
            </div>
        </div>

        <div class="product__feature">
            <div class="product__feature-icon">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/product/wallet.svg" alt="Wallet icon">
            </div>
            <div class="product__feature-content">
                <h6 class="product__feature-title">TOPOWE MARKI W UCZCIWYCH CENACH</h6>
                <p class="product__feature-text">Oferujemy jakość i charakter bez konieczności przepłacania za etykietę</p>
            </div>
        </div>
    </div>
<?php
}

add_filter('woocommerce_get_price_html', 'add_cena_brutto_label_to_price', 20, 2);

function add_cena_brutto_label_to_price($price_html, $product)
{
    // Only apply on single product pages
    if (is_product()) {
        $price_html .= ' <span class="cena-brutto-label">Cena brutto</span>';
      //  $price_html .= '<span>' . do_shortcode('[omnibus_price_message]') . '</span>';
    }

    return $price_html;
}

add_action( 'woocommerce_single_product_summary', 'custom_wrap_price_and_omnibus', 9 );
function custom_wrap_price_and_omnibus() {

    echo '<div class="product__price-wrapper">';
}

add_action( 'woocommerce_single_product_summary', 'custom_wrap_price_and_omnibus_close', 11 );
function custom_wrap_price_and_omnibus_close() {
    echo '</div>';
}


add_filter('woocommerce_get_availability_text', 'remove_stock_text_single_product', 10, 2);
function remove_stock_text_single_product($availability, $product)
{
    if (is_product()) {
        return ''; // Remove the text
    }
    return $availability;
}


add_filter('woocommerce_product_tabs', 'custom_rename_woocommerce_tabs', 98);
function custom_rename_woocommerce_tabs($tabs)
{
    if (isset($tabs['description'])) {
        $tabs['description']['title'] = 'O produkcie'; // Rename "Opis"
    }

    if (isset($tabs['additional_information'])) {
        $tabs['additional_information']['title'] = 'Detale produktu'; // Rename "Dodatkowe informacje"
    }

    return $tabs;
}

// Remove heading from "Opis" (Description) tab
add_filter('woocommerce_product_description_heading', '__return_null');
add_filter('woocommerce_product_additional_information_heading', '__return_null');


add_action('woocommerce_after_main_content', "single_product_custom_content", 5);
function single_product_custom_content()
{
    global $product;

    if (is_product()) {

        $icon_url    = get_template_directory_uri() . '/assets/icons/product/white-bottle.svg';
        $small_text  = 'Warto spróbować';
        $title       = 'Wybrane z myślą o Tobie';
        $description = 'Zobacz produkty, które równie często trafiają na stoły koneserów. Rekomendacje inspirowane Twoim wyborem – unikalne, aromatyczne i warte poznania.';

        // Get product IDs to display (need 5 total)
        $product_ids = [];
        $needed_products = 5;
        $current_product_id = $product->get_id();

        // 1. First try to get upsell products
        $upsell_ids = $product->get_upsell_ids();
        if (!empty($upsell_ids)) {
            // Filter out any unavailable products
            $upsell_ids = array_filter($upsell_ids, function ($id) {
                $product = wc_get_product($id);
                return $product && $product->is_in_stock() && $product->is_visible();
            });
            $product_ids = array_slice($upsell_ids, 0, $needed_products);
        }

        // 2. If we still need more products, get from the same category
        if (count($product_ids) < $needed_products) {
            $remaining = $needed_products - count($product_ids);

            $product_categories = get_the_terms($current_product_id, 'product_cat');
            if ($product_categories && !is_wp_error($product_categories)) {
                $category_ids = wp_list_pluck($product_categories, 'term_id');

                $args = array(
                    'post_type'      => 'product',
                    'posts_per_page' => $remaining,
                    'post__not_in'   => array_merge([$current_product_id], $product_ids),
                    'tax_query'      => array(
                        array(
                            'taxonomy' => 'product_cat',
                            'field'    => 'term_id',
                            'terms'    => $category_ids,
                            'operator' => 'IN'
                        )
                    ),
                    'meta_query'     => array(
                        array(
                            'key'     => '_stock_status',
                            'value'   => 'instock',
                            'compare' => '='
                        )
                    )
                );

                $category_products = get_posts($args);
                $category_product_ids = wp_list_pluck($category_products, 'ID');
                $product_ids = array_merge($product_ids, array_slice($category_product_ids, 0, $remaining));
            }
        }

        // 3. If we still need more products, get any popular products
        if (count($product_ids) < $needed_products) {
            $remaining = $needed_products - count($product_ids);

            $args = array(
                'post_type'      => 'product',
                'posts_per_page' => $remaining,
                'post__not_in'   => array_merge([$current_product_id], $product_ids),
                'meta_key'       => 'total_sales',
                'orderby'        => 'meta_value_num',
                'meta_query'     => array(
                    array(
                        'key'     => '_stock_status',
                        'value'   => 'instock',
                        'compare' => '='
                    )
                )
            );

            $popular_products = get_posts($args);
            $popular_product_ids = wp_list_pluck($popular_products, 'ID');
            $product_ids = array_merge($product_ids, array_slice($popular_product_ids, 0, $remaining));
        }

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
}


add_action('woocommerce_after_main_content', "single_product_ask_question", 6);

function single_product_ask_question()
{
    if (is_product()) {
        get_template_part('template-parts/components/pytanie');
    }
}


add_action('woocommerce_after_main_content', 'single_product_newsletter', 7);
function single_product_newsletter()
{
    if (is_product()) {
        get_template_part('template-parts/components/newsletter');
    }
}



add_action('woocommerce_share', 'single_product_compare_action', 10);
function single_product_compare_action()
{
    if (is_product()) {
            global $product;               
            $product_id = $product->get_id();
            $compare_products = function_exists('get_compare_products') ? get_compare_products() : [];
            $is_in_compare = in_array($product_id, $compare_products);

            $action = $is_in_compare ? 'remove-from-compare' : 'add-to-compare';
            $text = $is_in_compare ? __('Usuń z porównania', 'bayonet') : __('Dodaj do porównania', 'bayonet');
            $icon = $is_in_compare ? 'minus' : 'plus';
            ?>

            <button class="compare-button <?php echo esc_attr($action); ?>"
                data-product-id="<?php echo esc_attr($product_id); ?>" title="<?php echo esc_attr($text); ?>">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/icons/product/compare.svg" />
                <span><?php echo esc_attr($text); ?></span>
            </button>
            <?php
    }
}




// MOBILE CHANGES
add_action('woocommerce_before_single_product_summary', 'single_product_mobile_header',5);
function single_product_mobile_header() {
    global $product;
    ?>
      <div class="product__summary-mobile-top">
        <?php basic_wp_breadcrumbs(); ?>
          <h1 class="product__title"><?php echo esc_html($product->get_name()); ?></h1>
</div>
    <?php
}

add_action('woocommerce_after_add_to_cart_quantity', 'single_product_mobile_actions',10);
function single_product_mobile_actions() {
    global $product;
    ?>
   <div class="product__actions-mobile">
            <div class="fav-icon" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
                <button type="button" class="fav-button" data-product-id="<?php echo esc_attr($product->get_id()); ?>" aria-label="<?php esc_attr_e('Dodaj do ulubionych', 'alko'); ?>">
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/icons/fav.svg" alt="<?php esc_attr_e('Dodaj do ulubionych', 'alko'); ?>">
                </button>
            </div>
            
        </div>
    <?php

}



// Show "Ask About Product" button if stock is 0 on single product page
add_action( 'woocommerce_single_product_summary', 'show_ask_about_product_button', 25 );

function show_ask_about_product_button() {
    global $product;

    if ( ! is_product() || ! $product ) {
        return;
    }

    // Check if stock is 0
    if ( $product->get_stock_quantity() === 0 || ! $product->is_in_stock() ) {
        echo '<a class="button button--primary ask-about-product">Zapytaj o dostępność</a>';
    }
}

// Add product inquiry modal at the footer
add_action('wp_footer', 'add_product_inquiry_modal');
function add_product_inquiry_modal() {
    if (!is_product()) {
        return;
    }
    
    global $product;
    if (!$product) {
        return;
    }
    ?>
    <div class="product-inquiry-overlay"></div>
    <div class="product-inquiry-modal">
        <button class="product-inquiry-close" aria-label="Zamknij">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <div class="product-inquiry-modal-content">
            <h4>Zapytaj o dostępność produktu</h4>
            <p>Wypełnij formularz, aby zapytać o dostępność produktu: <strong><?php echo esc_html($product->get_name()); ?></strong></p>
            <?php echo do_shortcode('[contact-form-7 id="594f5cf" title="Zapytanie o produkt"]'); ?>
        </div>
    </div>
    <?php
}
