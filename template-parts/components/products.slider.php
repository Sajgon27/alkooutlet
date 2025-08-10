<?php
/**
 * Products Slider Template Part
 * 
 * @package alkooutlet
 * 
 * Parameters (via set_query_var):
 * - icon: Icon path/URL
 * - small_text: Small text next to icon
 * - title: Section title
 * - description: Section description
 * - product_ids: Array of product IDs to display (optional)
 * - category_id: Category ID to pull products from (optional)
 * - theme: 'light' or 'dark' (default: 'light')
 * - max_products: Maximum number of products to show (default: 6)
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Get parameters
$icon = get_query_var('icon', '');
$small_text = get_query_var('small_text', '');
$title = get_query_var('title', '');
$description = get_query_var('description', '');
$product_ids = get_query_var('product_ids', []);
$category_id = get_query_var('category_id', 0);
$theme = get_query_var('theme', 'light');
$max_products = get_query_var('max_products', 6);

// Set background image based on theme
$bg_image = ($theme === 'dark') ? 
    get_template_directory_uri() . '/assets/images/dude-dark.svg' : 
    get_template_directory_uri() . '/assets/images/dude-light.svg';

// Theme class
$theme_class = ($theme === 'dark') ? 'products-slider--dark' : 'products-slider--light';

// Query products
$query_args = [
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => $max_products,
];

// Either use specific product IDs or get from category
if (!empty($product_ids)) {
    $query_args['post__in'] = $product_ids;
    $query_args['orderby'] = 'post__in';
} elseif ($category_id > 0) {
    $query_args['tax_query'] = [
        [
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => $category_id,
        ],
    ];
}

$products_query = new WP_Query($query_args);

// Only proceed if we have products
if ($products_query->have_posts()) :
?>

<section class="products-slider <?php echo esc_attr($theme_class); ?>">
  
    <div class="container">
        <div class="products-slider__wrapper">
            <div class="products-slider__content">
                  <div class="products-slider__background" style="background-image: url('<?php echo esc_url($bg_image); ?>');" aria-hidden="true"></div>
                <?php if (!empty($icon) || !empty($small_text)) : ?>
                    <div class="products-slider__label section-label">
                        <?php if (!empty($icon)) : ?>
                            
                                <img src="<?php echo esc_url($icon); ?>" alt="" aria-hidden="true">
                          
                        <?php endif; ?>
                        <?php if (!empty($small_text)) : ?>
                            <span class="products-slider__small-text section-label__text"><?php echo esc_html($small_text); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="products-slider__intro">
                    <?php if (!empty($title)) : ?>
                        <h2 class="products-slider__title"><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>
                    
                    <?php if (!empty($description)) : ?>
                        <div class="products-slider__description">
                            <p><?php echo esc_html($description); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="products-slider__nav">
                    <button class="products-slider__nav-button products-slider__nav-button--prev" aria-label="<?php esc_attr_e('Previous slide', 'alkooutlet'); ?>">
                        <span class="arrow-left"></span>
                    </button>
                    <button class="products-slider__nav-button products-slider__nav-button--next" aria-label="<?php esc_attr_e('Next slide', 'alkooutlet'); ?>">
                        <span class="arrow-right"></span>
                    </button>
                </div>
            </div>

            <div class="products-slider__swiper-container">
                <div class="products-slider__swiper swiper productsSwiper">
                    <div class="products-slider__products swiper-wrapper">
                <?php 
                while ($products_query->have_posts()) : 
                    $products_query->the_post();
                    global $product;
                    
                    // Start swiper-slide div
                    echo '<div class="swiper-slide">';
                    
                    // Use WooCommerce's content-product.php template
                    wc_get_template_part('content', 'product');
                    
                    // End swiper-slide div
                    echo '</div>';
                endwhile;
                
                wp_reset_postdata();
                ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php endif; ?>