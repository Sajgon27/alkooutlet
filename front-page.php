<?php
get_header();
?>

<main id="main" class="site-main front-page">
    <?php get_template_part('template-parts/home/hero'); ?>
    <?php get_template_part('template-parts/home/categories'); ?>



    <?php
    // First products section
    $group = get_field('sekcja_produktowa_1');

    // Ensure $group is an array before proceeding
    if (is_array($group)) {
        $icon_id     = isset($group['ikonka']) && is_int($group['ikonka']) ? $group['ikonka'] : null;
        $icon_url    = $icon_id ? wp_get_attachment_url($icon_id) : '';
        $small_text  = isset($group['tekst_ikonka']) ? sanitize_text_field($group['tekst_ikonka']) : '';
        $title       = isset($group['tytul']) ? sanitize_text_field($group['tytul']) : '';
        $description = isset($group['tekst']) ? wp_kses_post($group['tekst']) : '';
        $products    = isset($group['produkt']) && is_array($group['produkt']) ? array_filter($group['produkt'], 'is_int') : [];

        set_query_var('icon', esc_url($icon_url));
        set_query_var('small_text', $small_text);
        set_query_var('title', $title);
        set_query_var('description', $description);
        set_query_var('product_ids', $products);
        set_query_var('theme', 'light');

        // Only include the template if required vars are reasonably valid
        if ($title || !empty($products)) {
            get_template_part('template-parts/components/products.slider');
        }
    }
    ?>

    <?php get_template_part('template-parts/home/grid-boxes'); ?>

    <?php
    // First products section
    $group = get_field('sekcja_produktowa_2');

    // Ensure $group is an array before proceeding
    if (is_array($group)) {
        $icon_id     = isset($group['ikonka']) && is_int($group['ikonka']) ? $group['ikonka'] : null;
        $icon_url    = $icon_id ? wp_get_attachment_url($icon_id) : '';
        $small_text  = isset($group['tekst_ikonka']) ? sanitize_text_field($group['tekst_ikonka']) : '';
        $title       = isset($group['tytul']) ? sanitize_text_field($group['tytul']) : '';
        $description = isset($group['tekst']) ? wp_kses_post($group['tekst']) : '';
        $products    = isset($group['produkt']) && is_array($group['produkt']) ? array_filter($group['produkt'], 'is_int') : [];

        set_query_var('icon', esc_url($icon_url));
        set_query_var('small_text', $small_text);
        set_query_var('title', $title);
        set_query_var('description', $description);
        set_query_var('product_ids', $products);
        set_query_var('theme', 'dark');

        // Only include the template if required vars are reasonably valid
        if ($title || !empty($products)) {
            get_template_part('template-parts/components/products.slider');
        }
    }
    ?>
    <?php get_template_part('template-parts/home/gallery-3-columns'); ?>
    <?php get_template_part('template-parts/components/logos-slider'); ?>
    <?php
    // First products section
    $group = get_field('sekcja_produktowa_3');

    // Ensure $group is an array before proceeding
    if (is_array($group)) {
        $icon_id     = isset($group['ikonka']) && is_int($group['ikonka']) ? $group['ikonka'] : null;
        $icon_url    = $icon_id ? wp_get_attachment_url($icon_id) : '';
        $small_text  = isset($group['tekst_ikonka']) ? sanitize_text_field($group['tekst_ikonka']) : '';
        $title       = isset($group['tytul']) ? sanitize_text_field($group['tytul']) : '';
        $description = isset($group['tekst']) ? wp_kses_post($group['tekst']) : '';
        $products    = isset($group['produkt']) && is_array($group['produkt']) ? array_filter($group['produkt'], 'is_int') : [];

        set_query_var('icon', esc_url($icon_url));
        set_query_var('small_text', $small_text);
        set_query_var('title', $title);
        set_query_var('description', $description);
        set_query_var('product_ids', $products);
        set_query_var('theme', 'light');

        // Only include the template if required vars are reasonably valid
        if ($title || !empty($products)) {
            get_template_part('template-parts/components/products.slider');
        }
    }
    ?>
    <?php get_template_part('template-parts/home/box-categories'); ?>
    <?php
    // First products section
    $group = get_field('sekcja_produktowa_4');

    // Ensure $group is an array before proceeding
    if (is_array($group)) {
        $icon_id     = isset($group['ikonka']) && is_int($group['ikonka']) ? $group['ikonka'] : null;
        $icon_url    = $icon_id ? wp_get_attachment_url($icon_id) : '';
        $small_text  = isset($group['tekst_ikonka']) ? sanitize_text_field($group['tekst_ikonka']) : '';
        $title       = isset($group['tytul']) ? sanitize_text_field($group['tytul']) : '';
        $description = isset($group['tekst']) ? wp_kses_post($group['tekst']) : '';
        $products    = isset($group['produkt']) && is_array($group['produkt']) ? array_filter($group['produkt'], 'is_int') : [];

        set_query_var('icon', esc_url($icon_url));
        set_query_var('small_text', $small_text);
        set_query_var('title', $title);
        set_query_var('description', $description);
        set_query_var('product_ids', $products);
        set_query_var('theme', 'light');

        // Only include the template if required vars are reasonably valid
        if ($title || !empty($products)) {
            get_template_part('template-parts/components/products.slider');
        }
    }
    ?>
    
    <?php get_template_part('template-parts/home/blog'); ?>
    
    <?php get_template_part('template-parts/components/newsletter'); ?>
    
    <?php get_template_part('template-parts/home/gallery-2-columns'); ?>
    <?php get_template_part('template-parts/components/cechy'); ?>
</main>

<?php
get_footer();
?>