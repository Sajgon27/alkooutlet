<?php


get_header(); ?>

<section class="favorites-page">
    <div class="container">

        <div class="page-header">
            <?php basic_wp_breadcrumbs() ?>
            <h1 class="page-title"> <?php _e('Ulubione', 'alko'); ?></h1>
        </div>

        <?php
        // Pobierz ulubione produkty z cookies
        $favorites = array();
        if (isset($_COOKIE['wc_favorites'])) {
            $favorites = json_decode(stripslashes($_COOKIE['wc_favorites']), true);
        }
        
        if (!empty($favorites) && is_array($favorites)) :
            // Filtruj tylko istniejące produkty
            $existing_favorites = array();
            foreach ($favorites as $product_id) {
                $product = wc_get_product($product_id);
                if ($product && $product->exists()) {
                    $existing_favorites[] = $product_id;
                }
            }
            
            if (!empty($existing_favorites)) :
                // Zapytanie WooCommerce o produkty
                $args = array(
                    'post_type' => 'product',
                    'post_status' => 'publish',
                    'post__in' => $existing_favorites,
                    'posts_per_page' => -1,
                    'orderby' => 'post__in'
                );
                
                $favorites_query = new WP_Query($args);
                
                if ($favorites_query->have_posts()) : ?>

        <div class="favorites-controls">
            <div class="favorites-count-info">
                <span class="count"><?php echo count($existing_favorites); ?></span>
                <?php echo (count($existing_favorites) == 1) ? 'produkt' : ((count($existing_favorites) < 5) ? 'produkty' : 'produktów'); ?>
            </div>

            <button class="button button--secondary" onclick="clearAllFavorites()">
                <?php _e('Usuń ulubione', 'alko'); ?>
            </button>
        </div>

        <div class="favorites-grid">
            <?php while ($favorites_query->have_posts()) : $favorites_query->the_post();
             
                global $product;
                $product = wc_get_product(get_the_ID());
                
                if (!$product) continue;
                  wc_get_template_part('content', 'product');
                // Użyj template part produktu
               // get_template_part('template-parts/product-card/product-card');
                
            endwhile; ?>
        </div>

        <?php else : ?>
        <div class="no-favorites">
            <div class="no-favorites-icon">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/fav.svg" alt="Brak ulubionych">
            </div>
            <h2> <?php _e('Brak ulubionych produktów', 'alko'); ?></h2>
            <p><?php _e('Nie masz jeszcze żadnych ulubionych produktów. Przeglądaj naszą ofertę i dodawaj produkty do ulubionych! ', 'alko'); ?>
            </p>
            <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="button button--primary">
                <?php _e('Przeglądaj produkty', 'alko'); ?>
            </a>
        </div>
        <?php endif;
                
                wp_reset_postdata();
                
            else : ?>
        <div class="no-favorites">
            <div class="no-favorites-icon">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/fav.svg" alt="Brak ulubionych">
            </div>
            <h2> <?php _e('Brak ulubionych produktów', 'alko'); ?></h2>
            <p> <?php _e('Nie masz jeszcze żadnych ulubionych produktów. Przeglądaj naszą ofertę i dodawaj produkty do ulubionych!', 'alko'); ?>
            </p>
            <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="button button--primary">
                <?php _e('Przeglądaj produkty ', 'alko'); ?>
            </a>
        </div>
        <?php endif;
            
        else : ?>
        <div class="no-favorites">
            <div class="no-favorites-icon">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/fav.svg" alt="Brak ulubionych">
            </div>
            <h2> <?php _e('Brak ulubionych produktów', 'alko'); ?></h2>
            <p> <?php _e('Nie masz jeszcze żadnych ulubionych produktów. Przeglądaj naszą ofertę i dodawaj produkty do ulubionych!', 'alko'); ?>
            </p>
            <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="button button--primary">
                <?php _e('Przeglądaj produkty ', 'alko'); ?>
            </a>
        </div>
        <?php endif; ?>

    </div>
</section>

<?php get_footer(); ?>