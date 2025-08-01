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

