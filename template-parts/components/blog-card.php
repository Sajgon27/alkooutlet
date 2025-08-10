<?php
/**
 * Template part for displaying blog cards
 *
 * @package alkooutlet
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('blog-card'); ?>>
    <a href="<?php the_permalink(); ?>" class="blog-card__link">
        <div class="blog-card__image">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('medium_large', array('class' => 'blog-card__img')); ?>
            <?php else : ?>
                <img class="blog-card__img" src="<?php echo get_template_directory_uri(); ?>/assets/images/placeholder.webp" alt="<?php the_title_attribute(); ?>" />
            <?php endif; ?>
        </div>
        
        <div class="blog-card__content">
            <h6 class="blog-card__title"><?php the_title(); ?></h6>
            
            <div class="blog-card__excerpt">
                <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
            </div>
            
            <span class="blog-card__read-more read-more">
                Czytaj więcej
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.1727 12L8.22266 7.04999L9.63666 5.63599L16.0007 12L9.63666 18.364L8.22266 16.95L13.1727 12Z" fill="currentColor"/>
                </svg>
            </span>
        </div>
    </a>
</article>