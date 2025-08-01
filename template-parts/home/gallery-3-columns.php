<?php
/**
 * Template part for displaying a 3-column gallery section on the front page
 *
 * @package alkooutlet
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get gallery images from ACF field
$gallery = get_field('galeria_3_kolumny');
$image_1 = $gallery['zdjecie_1'] ?? '';
$image_2 = $gallery['zdjecie_2'] ?? '';
$image_3 = $gallery['zdjecie_3'] ?? '';

// Proceed only if at least one image is available
if ($image_1 || $image_2 || $image_3) :
?>

<section class="gallery-3-columns">
    <div class="container">
        <div class="gallery-3-columns__grid swiper gallery3ColumnsSwiper">
            <div class="swiper-wrapper">
                <?php if ($image_1) : ?>
                    <div class="gallery-3-columns__item swiper-slide">
                        <?php 
                            $image_url = wp_get_attachment_image_url($image_1, 'full');
                            if ($image_url) :
                        ?>
                        <div class="gallery-3-columns__image" style="background-image: url('<?php echo esc_url($image_url); ?>');"></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($image_2) : ?>
                    <div class="gallery-3-columns__item swiper-slide">
                        <?php 
                            $image_url = wp_get_attachment_image_url($image_2, 'full');
                            if ($image_url) :
                        ?>
                        <div class="gallery-3-columns__image" style="background-image: url('<?php echo esc_url($image_url); ?>');"></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($image_3) : ?>
                    <div class="gallery-3-columns__item swiper-slide">
                        <?php 
                            $image_url = wp_get_attachment_image_url($image_3, 'full');
                            if ($image_url) :
                        ?>
                        <div class="gallery-3-columns__image" style="background-image: url('<?php echo esc_url($image_url); ?>');"></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php endif; ?>
