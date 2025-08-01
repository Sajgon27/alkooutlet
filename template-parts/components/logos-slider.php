<?php
/**
 * Template part for displaying logos slider
 *
 * @package alkooutlet
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get logos from options page
$logos = get_field('loga', 'option');

// Only proceed if we have logos
if ($logos && is_array($logos) && count($logos) > 0) :
?>

<section class="logos-slider">
    <div class="container">
        <div class="logos-slider__container swiper logosSwiper">
            <div class="swiper-wrapper">
                <?php foreach ($logos as $logo_id) : 
                    // Make sure we have a valid image ID
                    if ($logo_id && is_numeric($logo_id)) :
                        $image_url = wp_get_attachment_image_url($logo_id, 'medium');
                        $alt_text = get_post_meta($logo_id, '_wp_attachment_image_alt', true) ?: 'Partner logo';
                        if ($image_url) :
                ?>
                    <div class="logos-slider__item swiper-slide">
                        <div class="logos-slider__logo-wrapper">
                            <img 
                                src="<?php echo esc_url($image_url); ?>" 
                                alt="<?php echo esc_attr($alt_text); ?>" 
                                class="logos-slider__logo"
                                loading="lazy" 
                            >
                        </div>
                    </div>
                <?php 
                        endif;
                    endif;
                endforeach; 
                ?>
            </div>
        </div>
    </div>
</section>

<?php endif; ?>
