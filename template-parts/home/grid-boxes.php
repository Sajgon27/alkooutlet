<?php
/**
 * Template part for displaying grid boxes section on the front page
 *
 * @package alkooutlet
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get boxes from ACF field
$boxes = get_field('boxy');

// Only proceed if we have boxes
if ($boxes && !empty($boxes['box'])) :
?>

<section class="grid-boxes">
    <div class="container">
        <div class="grid-boxes__grid swiper gridBoxesSwiper">
            <div class="swiper-wrapper">
                <?php foreach ($boxes['box'] as $box) :
                    // Get box fields
                    $title = $box['tytul'] ?? '';
                    $text = $box['tekst'] ?? '';
                    $image = $box['zdjecie'] ?? '';
                    $link = $box['link'] ?? '';
                 
                    // Only display if there's at least a title or image
                    if (!empty($title) || !empty($image)) :
                ?>
                    <div class="grid-boxes__item swiper-slide">
                        <?php if (!empty($link)) : ?>
                            <a href="<?php echo esc_url($link); ?>" class="grid-boxes__link">
                        <?php endif; ?>
                            
                            <div class="grid-boxes__content">
                                <?php if (!empty($image)) : 
                                    $image_url = wp_get_attachment_image_url($image, 'full');
                                ?>
                                    <div class="grid-boxes__image-wrapper" style="background-image: url('<?php echo esc_url($image_url); ?>');">
                                    </div>
                                <?php endif; ?>
                                
                                <div class="grid-boxes__text-wrapper">
                                    <?php if (!empty($title)) : ?>
                                        <h3 class="grid-boxes__title"><?php echo esc_html($title); ?></h3>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($text)) : ?>
                                        <p class="grid-boxes__text"><?php echo esc_html($text); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                        <?php if (!empty($link)) : ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php endif; ?>
