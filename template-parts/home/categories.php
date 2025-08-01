<?php
/**
 * Template part for displaying product categories section on the front page
 *
 * @package alkooutlet
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get selected categories from ACF field
$selected_categories = get_field('kategorie_home_section');
// Proceed only if categories are selected
if ($selected_categories && !empty($selected_categories)) :
?>

<section class="categories">
    <div class="container">
        <div class="categories__grid swiper categoriesSwiper">
            <div class="swiper-wrapper">
                <?php
                // Loop through selected category IDs
                foreach ($selected_categories as $category_id) :
                    // Get the category object
                    $category = get_term($category_id, 'product_cat');

                    if ($category && !is_wp_error($category)) :
                        // Get category image
                        $thumbnail_id = get_term_meta($category_id, 'thumbnail_id', true);
                        
                        // Get category link
                        $category_link = get_term_link($category_id, 'product_cat');
                        ?>
                        <div class="categories__item swiper-slide">
                            <a href="<?php echo esc_url($category_link); ?>" class="categories__link">
                                <?php if ($thumbnail_id) : ?>
                                    <div class="categories__image-wrapper">
                                        <?php echo wp_get_attachment_image($thumbnail_id, 'medium', false, array('class' => 'categories__image')); ?>
                                    </div>
                                <?php endif; ?>
                                <h3 class="categories__title"><?php echo esc_html($category->name); ?></h3>
                            </a>
                        </div>
                        <?php
                    endif;
                endforeach;
                ?>
            </div>
        </div>
    </div>
</section>

<?php endif; ?>
