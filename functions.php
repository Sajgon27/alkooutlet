<?php
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
    if (is_product()) {
        wp_enqueue_script(
            'single-product',
            get_template_directory_uri() . '/assets/js/single-product.js',
            ['jquery'],
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
    if(is_page(324)) {
        wp_enqueue_script(
            'faq-script',
            get_template_directory_uri() . '/assets/js/faq.js',
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



// Show top-level category above product title
add_action('woocommerce_shop_loop_item_title', 'show_top_level_category_above_title', 5);
function show_top_level_category_above_title()
{
    global $product;
    $terms = get_the_terms($product->get_id(), 'product_cat');

    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            // Find the top-level ancestor
            $parent = $term;
            while ($parent->parent != 0) {
                $parent = get_term($parent->parent, 'product_cat');
            }
            echo '<div class="product-top-category">' . esc_html($parent->name) . '</div>';
            break; // Only show the first top-level category
        }
    }
}

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
    $trimmed = implode(' ', array_slice($words, 0, 8));

    echo '<div class="product-short-excerpt">' . esc_html($trimmed) . '...</div>';
}


//BREADCRUMBS
function basic_wp_breadcrumbs()
{
    if (is_front_page()) return;

    $chevron_icon = '   <svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 1L9 5L4 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>';
    echo '<nav class="breadcrumbs">';
    echo '<a href="' . home_url() . '">Strona główna</a>';

    // Archive page for CPT
    if (is_post_type_archive('kurs-i-wydarzenie')) {
        echo $chevron_icon . 'Terminarz';

        // Single post of CPT
    } elseif (is_singular('kurs-i-wydarzenie')) {
        echo $chevron_icon . '<a href="' . get_post_type_archive_link('kurs-i-wydarzenie') . '">Terminarz</a>';
        echo $chevron_icon . get_the_title();

        // Regular single post with category
    } elseif (is_category() || is_single()) {
        $category = get_the_category();
        if ($category && !is_search()) {
            echo $chevron_icon . '<a href="' . get_category_link($category[0]->term_id) . '">' . esc_html($category[0]->name) . '</a>';
        }
        if (is_single()) {
            echo $chevron_icon . get_the_title();
        }

        // Page with parent hierarchy
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
    } elseif (is_tag()) {
        echo $chevron_icon . 'Tag: ' . single_tag_title('', false);
    } elseif (is_404()) {
        echo $chevron_icon . '404';
    }

    echo '</nav>';
}
