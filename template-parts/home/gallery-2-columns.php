<?php
/**
 * Gallery 2 Columns Section Template
 *
 * @package AlkoOutlet
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Get ACF group field
$gallery_group = get_field('galeria_2_kolumny','options');

// Check if the group exists and extract the fields
if (is_array($gallery_group)) {
    $obrazek_1 = isset($gallery_group['obrazek_1']) ? $gallery_group['obrazek_1'] : '';
    $link_1 = isset($gallery_group['link_1']) ? $gallery_group['link_1'] : '';
    $obrazek_2 = isset($gallery_group['obrazek_2']) ? $gallery_group['obrazek_2'] : '';
    $link_2 = isset($gallery_group['link_2']) ? $gallery_group['link_2'] : '';
} else {
    $obrazek_1 = $link_1 = $obrazek_2 = $link_2 = '';
}

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
        
        <!-- Mobile swiper layout -->
        <div class="gallery-2-columns__swiper swiper mobile-only">
            <div class="swiper-wrapper">
                <?php if (!empty($obrazek_1) && $image_url_1) : ?>
                <div class="swiper-slide gallery-2-columns__item">
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
                <?php endif; ?>
                
                <?php if (!empty($obrazek_2) && $image_url_2) : ?>
                <div class="swiper-slide gallery-2-columns__item">
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
                <?php endif; ?>
            </div>
         
        </div>
    </div>
</section>
