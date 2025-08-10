<?php

/**
 * The template for displaying blog archives
 *
 * @package alkooutlet
 */

get_header();
?>

<div class="blog-page">
    <div class="container">
        <div class="blog__breadcrumbs">
            <?php
            basic_wp_breadcrumbs(); ?>

        </div>

        <div class="blog__header">
            <h2 class="blog__title">BLOG</h2>

            <div class="blog__search">
                <?php echo do_shortcode('[facetwp facet="search_blog"]'); ?>
            </div>
        </div>

        <div class="blog__categories">
            <?php echo do_shortcode('[facetwp facet="categories"]'); ?>
        </div>

        <div class="blog__grid">
            <?php
            if (have_posts()) :
                while (have_posts()) :
                    the_post();
                    get_template_part('template-parts/components/blog-card');
                endwhile;
            else :
            ?>
                <div class="blog__no-results">
                    <p>Nie znaleziono pasujących artykułów.</p>
                </div>
            <?php
            endif;
            ?>
        </div>

        <div class="blog__pagination">
            <?php
            the_posts_pagination(
                array(
                    'prev_text' => '<span class="screen-reader-text">' . __('Poprzednia strona', 'alkooutlet') . '</span>',
                    'next_text' => '<span class="screen-reader-text">' . __('Następna strona', 'alkooutlet') . '</span>',
                    'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Strona', 'alkooutlet') . ' </span>',
                )
            );
            ?>
        </div>

        <div class="blog__refresh">
            <?php echo do_shortcode('[facetwp template="blog"]'); ?>
        </div>
    </div>
   
</div>
 <?php get_template_part('template-parts/components/newsletter'); ?>
<?php
get_footer();
