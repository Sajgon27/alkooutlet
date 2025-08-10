<?php

/**
 * The template for displaying single posts
 *
 * @package alkooutlet
 */

get_header();
?>

<div class="single-post-page">
    <div class="container">
        <div class="single-post__breadcrumbs">
            <?php
            if (function_exists('basic_wp_breadcrumbs')) {
                basic_wp_breadcrumbs();
            }
            ?>
        </div>

        <?php while (have_posts()) : the_post(); ?>

            <h1 class="single-post__title"><?php the_title(); ?></h1>

            <div class="single-post__wrapper">
                <div class="single-post__main">
                    <div class="single-post__date">
                        <?php echo get_the_date('d/m/Y H:i'); ?>
                    </div>

                    <?php if (has_post_thumbnail()) : ?>
                        <div class="single-post__image">
                            <?php the_post_thumbnail('large', array('class' => 'single-post__img')); ?>
                        </div>
                    <?php endif; ?>

                    <div class="single-post__content">
                        <?php the_content(); ?>
                    </div>



                    <?php if (get_the_tags()) : ?>
                        <div class="single-post__tags">
                            <?php the_tags('<span class="single-post__tags-label">Tagi: </span>', ', '); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <aside class="single-post__sidebar">

                    <div class="single-post__related">
                        <?php
                        $recent_posts = new WP_Query(array(
                            'post_type' => 'post',
                            'posts_per_page' => 5,
                            'post__not_in' => array(get_the_ID()),
                            'orderby' => 'date',
                            'order' => 'DESC'
                        ));

                        if ($recent_posts->have_posts()) :
                            while ($recent_posts->have_posts()) : $recent_posts->the_post();
                        ?>
                                <div class="single-post__related-item">
                                    <a href="<?php the_permalink(); ?>" class="single-post__related-link">
                                        <div class="single-post__related-image">
                                            <?php if (has_post_thumbnail()) : ?>
                                                <?php the_post_thumbnail('thumbnail', array('class' => 'single-post__related-img')); ?>
                                            <?php else : ?>
                                                <img class="single-post__related-img" src="<?php echo get_template_directory_uri(); ?>/assets/images/placeholder.webp" alt="<?php the_title_attribute(); ?>" />
                                            <?php endif; ?>
                                        </div>

                                        <div class="single-post__related-content">
                                            <h6 class="single-post__related-title"><?php the_title(); ?></h6>
                                            <span class="single-post__related-more read-more"> Czytaj więcej
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M13.1727 12L8.22266 7.04999L9.63666 5.63599L16.0007 12L9.63666 18.364L8.22266 16.95L13.1727 12Z" fill="currentColor" />
                                                </svg></span>
                                        </div>
                                    </a>
                                </div>
                            <?php
                            endwhile;
                            wp_reset_postdata();
                        else :
                            ?>
                            <p>Brak ostatnich artykułów.</p>
                        <?php
                        endif;
                        ?>
                    </div>

                    <div class="single-post__blog-link">
                        <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="button button--primary">Zobacz więcej</a>
                    </div>
                </aside>
            </div>
            <?php if (get_field('opis') || get_field('produkt')) : ?>
                <div class="single-post__product-section">
                    <div class="single-post__product-wrapper">
                        <div class="single-post__product-description">
                            <div class="single-post__product-header section-label">

                                <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/glass.svg" alt="Glass icon">

                                <span class="single-post__product-label section-label__text">Prosto z artykułu</span>
                            </div>

                            <h2 class="single-post__product-title">Z ARTYKUŁU PROSTO DO KOSZYKA</h2>

                            <?php if (get_field('opis')) : ?>
                                <div class="single-post__product-text">
                                    <?php echo get_field('opis'); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="single-post__product-display">
                            <?php
                            $product_id = get_field('produkt');
                            if ($product_id && function_exists('wc_get_product')) :
                                $product = wc_get_product($product_id);
                                if ($product) :
                                    // Get product data
                                    $image_id = $product->get_image_id();
                                    $image_url = wp_get_attachment_image_url($image_id, 'medium') ?: get_template_directory_uri() . '/assets/images/placeholder.webp';
                                    $product_url = get_permalink($product_id);
                                    $product_name = $product->get_name();
                                    $short_description = $product->get_short_description();
                                    if (!empty($short_description)) {
                                        $product_excerpt = wp_trim_words($short_description, 20);
                                    } else {
                                        // If no excerpt, get first 12 words from product content
                                        $product_excerpt = wp_trim_words($product->get_description(), 12);
                                    }
                                    $product_price = $product->get_price_html();

                                    // Get primary category
                                    $categories = '';
                                    $product_cats = wp_get_post_terms($product_id, 'product_cat');
                                    if (!empty($product_cats) && !is_wp_error($product_cats)) {
                                        $categories = $product_cats[0]->name;
                                    }
                            ?>
                                    <div class="single-post__product-card">
                                        <a href="<?php echo esc_url($product_url); ?>" class="single-post__product-card-link">
                                            <div class="single-post__product-card-image">
                                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($product_name); ?>">

                                            </div>
                                        </a>
                                        <div class="single-post__product-card-content">
                                            <?php if ($categories) : ?>
                                                <div class="single-post__product-card-category">
                                                    <?php echo esc_html($categories); ?>
                                                </div>
                                            <?php endif; ?>

                                            <h3 class="single-post__product-card-title">
                                                <a href="<?php echo esc_url($product_url); ?>">
                                                    <?php echo esc_html($product_name); ?>
                                                </a>
                                            </h3>

                                            <div class="single-post__product-card-excerpt">
                                                <?php echo $product_excerpt; ?>
                                            </div>

                                            <div class="single-post__product-card-bottom">
                                                <div class="single-post__product-card-price">
                                                    <?php echo $product_price; ?>
                                                </div>

                                                <div class="single-post__product-card-button">
                                                    <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" class="button button--primary add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo esc_attr($product_id); ?>">
                                                        Dodaj do koszyka
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                endif;
                            endif;
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endwhile; ?>
    </div>
    <?php get_template_part('template-parts/components/newsletter'); ?>
</div>

<?php
get_footer();
