<?php
/**
 * Blog section for the homepage
 *
 * @package AlkoOutlet
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Get 4 most recent posts
$blog_args = array(
    'post_type'      => 'post',
    'posts_per_page' => 4,
    'post_status'    => 'publish',
);

$blog_query = new WP_Query($blog_args);
?>

<?php if ($blog_query->have_posts()) : ?>
<section class="blog-section">
    <div class="container" style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/dude-light-left.svg' ); ?>'); ">
        <div class="blog-section__header">
            <div class="blog-section__header-left">
                <div class="blog-section__header-top section-label">
                    <?php if (file_exists(get_template_directory() . '/assets/icons/blog-home.svg')) : ?>
                        
                            <?php echo file_get_contents(get_template_directory() . '/assets/icons/blog-home.svg'); ?>
                      
                    <?php endif; ?>
                    <span class="blog-section__small-title section-label__text">Butelki z historią</span>
                </div>
                <h2 class="blog-section__title">HISTORIE ZAPISANE W BUTELKACH</h2>
            </div>
            <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="blog-section__more-link button button--secondary">
                Przejdź do bloga
            </a>
        </div>
        
        <div class="blog-section__content">
            <div class="blog-section__desktop">
                <div class="blog-section__main-post">
                    <?php $blog_query->the_post(); ?>
                    <article class="blog-post blog-post--large">
                        <a href="<?php the_permalink(); ?>" class="blog-post__image-link">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('large', array('class' => 'blog-post__image')); ?>
                            <?php else: ?>
                                <div class="blog-post__image blog-post__image--placeholder"></div>
                            <?php endif; ?>
                        </a>
                        <div class="blog-post__content">
                            <h3 class="blog-post__title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <div class="blog-post__excerpt">
                                <?php 
                                if (has_excerpt()) {
                                    echo wp_kses_post(get_the_excerpt());
                                } else {
                                    echo wp_kses_post(wp_trim_words(get_the_content(), 35, '...'));
                                }
                                ?>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="blog-post__read-more read-more">
                                Czytaj więcej
                                    <svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 1L9 5L4 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                            </a>
                        </div>
                    </article>
                </div>
                
                <div class="blog-section__side-posts">
                    <?php while ($blog_query->have_posts()) : $blog_query->the_post(); ?>
                        <article class="blog-post blog-post--small">
                            <a href="<?php the_permalink(); ?>" class="blog-post__image-link">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium', array('class' => 'blog-post__image')); ?>
                                <?php else: ?>
                                    <div class="blog-post__image blog-post__image--placeholder"></div>
                                <?php endif; ?>
                            </a>
                            <div class="blog-post__content">
                                <h3 class="blog-post__title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <div class="blog-post__excerpt">
                                    <?php 
                                    if (has_excerpt()) {
                                        echo wp_kses_post(wp_trim_words(get_the_excerpt(), 12, '...'));
                                    } else {
                                        echo wp_kses_post(wp_trim_words(get_the_content(), 12, '...'));
                                    }
                                    ?>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="blog-post__read-more read-more">
                                    Czytaj więcej
                                    <svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 1L9 5L4 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <!-- Mobile Swiper -->
            <div class="blog-section__mobile">
                <div class="swiper blogSwiper">
                    <div class="swiper-wrapper">
                        <?php 
                        // Reset the post query to get all 4 posts again for mobile
                        $blog_query->rewind_posts();
                        while ($blog_query->have_posts()) : $blog_query->the_post(); 
                        ?>
                            <div class="swiper-slide">
                                <article class="blog-post">
                                    <a href="<?php the_permalink(); ?>" class="blog-post__image-link">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('medium_large', array('class' => 'blog-post__image')); ?>
                                        <?php else: ?>
                                            <div class="blog-post__image blog-post__image--placeholder"></div>
                                        <?php endif; ?>
                                    </a>
                                    <div class="blog-post__content">
                                        <h3 class="blog-post__title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>
                                        <div class="blog-post__excerpt">
                                            <?php 
                                            if (has_excerpt()) {
                                                echo wp_kses_post(wp_trim_words(get_the_excerpt(), 10, '...'));
                                            } else {
                                                echo wp_kses_post(wp_trim_words(get_the_content(), 10, '...'));
                                            }
                                            ?>
                                        </div>
                                        <a href="<?php the_permalink(); ?>" class="blog-post__read-more">
                                            Czytaj więcej
                                               <svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 1L9 5L4 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                        </a>
                                    </div>
                                </article>
                            </div>
                        <?php endwhile; ?>
                    </div>
               
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php wp_reset_postdata(); ?>
