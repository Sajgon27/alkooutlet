<?php


remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
//add_action('alko_after_custom_header', 'woocommerce_catalog_ordering', 5);

add_action('alko_after_custom_header', 'mobile_filters_button', 10);
function mobile_filters_button()
{
?>
    <div class="shop-toolbar">
      <?php woocommerce_catalog_ordering(); ?>
        <button class="button button--outline button--mobile-filters">Filtry
            <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/filters.svg" alt="Filtry" /></button>
  
    </div>

    <?php
}

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



// Remove result count (e.g., "Showing 1–12 of 45 results")
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
// Remove product taxonomy archive header
remove_action('woocommerce_shop_loop_header', 'woocommerce_product_taxonomy_archive_header', 10);


//SHOP ARCHIVE
add_action('woocommerce_after_main_content', 'shop_archive_template', 5);
function shop_archive_template()
{

    if ((is_shop() || is_search()) && !isset($_GET['_search_shop'])) {

        $obrazek_1 = get_field('obrazek_1_archive', 'options');
        $link_1 = get_field('link_1_archive', 'options');
        $obrazek_2 = get_field('obrazek_2_archive', 'options');
        $link_2 = get_field('link_2_archive', 'options');


        // Check if we have at least one image to display
        if (empty($obrazek_1) && empty($obrazek_2)) {
            return; // Don't output anything if no images
        }

    ?>
        <section class="gallery-2-columns">
            <div class="container">
                <!-- Desktop grid layout -->
                <div class="gallery-2-columns__grid desktop-only">
                    <?php if (!empty($obrazek_1)) :
                        $image_url_1 = wp_get_attachment_image_url($obrazek_1, 'full');
                        if ($image_url_1) :
                    ?>
                            <div class="gallery-2-columns__item">
                                <?php if (!empty($link_1)) : ?>
                                    <a href="<?php echo esc_url($link_1); ?>" class="gallery-2-columns__link">
                                        <div class="gallery-2-columns__image" style="background-image: url('<?php echo esc_url($image_url_1); ?>');">
                                        </div>
                                    </a>
                                <?php else : ?>
                                    <div class="gallery-2-columns__image" style="background-image: url('<?php echo esc_url($image_url_1); ?>');">
                                    </div>
                                <?php endif; ?>
                            </div>
                    <?php
                        endif;
                    endif; ?>

                    <?php if (!empty($obrazek_2)) :
                        $image_url_2 = wp_get_attachment_image_url($obrazek_2, 'full');
                        if ($image_url_2) :
                    ?>
                            <div class="gallery-2-columns__item">
                                <?php if (!empty($link_2)) : ?>
                                    <a href="<?php echo esc_url($link_2); ?>" class="gallery-2-columns__link">
                                        <div class="gallery-2-columns__image" style="background-image: url('<?php echo esc_url($image_url_2); ?>');">
                                        </div>
                                    </a>
                                <?php else : ?>
                                    <div class="gallery-2-columns__image" style="background-image: url('<?php echo esc_url($image_url_2); ?>');">
                                    </div>
                                <?php endif; ?>
                            </div>
                    <?php
                        endif;
                    endif; ?>
                </div>


            </div>
        </section>

    <?php
        get_template_part('template-parts/components/cechy');
    }
}



add_action('facetwp_scripts', function () {
    ?>
    <script>
        (function($) {
            $(document).on('focusout', '.facetwp-type-number_range .facetwp-number', function() {
                FWP.autoload(); // Refresh
            });
        })(jQuery);
    </script>
<?php
}, 100);



// Scroll to the top of the page on archive pagination
add_action('wp_head', function () {
?>
    <script>
        (function($) {
            $(document).on('facetwp-loaded', function() {
                $('html, body').animate({
                    scrollTop: 200
                }, 10);
            });
        })(jQuery);
    </script>
<?php });