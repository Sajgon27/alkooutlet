<?php

/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined('ABSPATH') || exit;

get_header('shop');

?> <div class="shop-archive">

    <div class="shop-archive__header">
        <div class="container">
            <div class="shop-archive__header-content">
                <div class="shop-archive__breadcrumbs">
                    <?php woocommerce_breadcrumb(); ?>
                </div>
                <?php if (is_search()) : ?>
                    <h1 class="shop-archive__title">
                        <?php
                        /* translators: %s: search query */
                        printf(esc_html__('Wyniki wyszukiwania dla: "%s"', 'alko'), get_search_query());
                        ?>
                    </h1>
                <?php elseif (is_product_category()) : ?>
                    <h1 class="shop-archive__title">
                        <?php single_cat_title(); ?>
                    </h1>
                <?php else : ?>
                    <h1 class="shop-archive__title"><?php esc_html_e('Nasze produkty', 'alko'); ?></h1>
                <?php endif; ?>
                <?php do_action('alko_after_custom_header'); ?>
            </div>
        </div>
    </div> <?php

            /**
             * Hook: woocommerce_before_main_content.
             *
             * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
             * @hooked woocommerce_breadcrumb - 20
             * @hooked WC_Structured_Data::generate_website_data() - 30
             */
            do_action('woocommerce_before_main_content');

            /**
             * Hook: woocommerce_shop_loop_header.
             *
             * @since 8.6.0
             *
             * @hooked woocommerce_product_taxonomy_archive_header - 10
             */
            do_action('woocommerce_shop_loop_header');

            if (woocommerce_product_loop()) {
            ?> <div class="shop-archive__content">
            <div class="container">
                       <div class="shop-archive__sidebar">
                    <div class="shop-archive__sidebar-header">
                        <div class="shop-archive__sidebar-title">
                            <h3>Filtry</h3>
                            <?php echo do_shortcode('[facetwp facet="reset_all_shop"]'); ?>
                        </div>

                        <!-- SKrypt do "Pokazano tyl z tylu" -->
                        <script>
                            (function($) {
                                $(document).on('facetwp-loaded', function() {
                                    var total = FWP.settings.pager.total_rows;
                                    var shown = $('.facetwp-template .product').length;

                                    $('.products-showing-count .shown').text(shown);
                                    $('.products-showing-count .total').text(total);
                                });
                            })(jQuery);
                        </script>
                        <?php



                        echo '<p class="products-showing-count">Pokazano <span class="shown">0</span> z <span class="total">0</span> </p>';
                        ?>
                    </div>
                    <div class="shop-archive__sidebar-search">
                        <div class="shop-archive__sidebar-single-title">
                            <h4>Szukaj w sklepie</h4>
                            <?php echo do_shortcode('[facetwp facet="rest_search"]'); ?>
                        </div>

                        <?php echo do_shortcode('[facetwp facet="search_shop"]'); ?>
                    </div>
                    <div class="shop-archive__sidebar-categories">
                        <div class="shop-archive__sidebar-single-title">
                            <h4>Kategorie</h4>
                            <?php echo do_shortcode('[facetwp facet="reset_categories"]'); ?>
                        </div>

                        <?php echo do_shortcode('[facetwp facet="categories_shop"]'); ?>
                    </div>
                    <div class="shop-archive__sidebar-price">
                        <div class="shop-archive__sidebar-single-title">
                            <h4>Cena</h4>
                            <?php echo do_shortcode('[facetwp facet="reset_price"]'); ?>
                        </div>
                        <div class="shop-archive__sidebar-price-labels"><span>Od</span> <span>Do</span></div>
                        <?php echo do_shortcode('[facetwp facet="price2_shop"]'); ?>
                        <?php //echo do_shortcode('[facetwp facet="price_shop"]'); 
                        ?>
                    </div>
                    <div class="shop-archive__sidebar-pojemnosc">
                        <div class="shop-archive__sidebar-single-title">
                            <h4>Pojemność</h4>
                            <?php echo do_shortcode('[facetwp facet="reset_pojemno"]'); ?>
                        </div>
                       
                        <?php echo do_shortcode('[facetwp facet="pojemno_shop"]'); ?>
                     
                    </div>

                </div>
                <?php


                /**
                 * Hook: woocommerce_before_shop_loop.
                 *
                 * @hooked woocommerce_output_all_notices - 10
                 * @hooked woocommerce_result_count - 20
                 * @hooked woocommerce_catalog_ordering - 30
                 */
                ?> <div class="facetwp-template">
                    <?php
                    do_action('woocommerce_before_shop_loop');

                    woocommerce_product_loop_start();

                    if (wc_get_loop_prop('total')) {
                        while (have_posts()) {
                            the_post();

                            /**
                             * Hook: woocommerce_shop_loop.
                             */
                            do_action('woocommerce_shop_loop');

                            wc_get_template_part('content', 'product');
                        }
                    }

                    woocommerce_product_loop_end();

                    /**
                     * Hook: woocommerce_after_shop_loop.
                     *
                     * @hooked woocommerce_pagination - 10
                     */
                    do_action('woocommerce_after_shop_loop');
                    ?> </div> <?php
                                ?>
            </div>
        </div><?php
            } else {
                /**
                 * Hook: woocommerce_no_products_found.
                 *
                 * @hooked wc_no_products_found - 10
                 */
                do_action('woocommerce_no_products_found');
            }

            /**
             * Hook: woocommerce_after_main_content.
             *
             * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
             */
            do_action('woocommerce_after_main_content');

            /**
             * Hook: woocommerce_sidebar.
             *
             * @hooked woocommerce_get_sidebar - 10
             */
            do_action('woocommerce_sidebar');

                ?>

</div> <?php

        get_footer('shop');
