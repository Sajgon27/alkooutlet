<?php
/**
 * Template part for displaying box categories section on the front page
 *
 * @package alkooutlet
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get box categories from ACF field
$boxes_data = get_field('boxy_kategorie');

// Proceed only if we have boxes
if ($boxes_data && !empty($boxes_data['box'])) :
    $boxes = $boxes_data['box'];
    
    // Limit to maximum 5 boxes (3 for top row, 2 for bottom row)
    $boxes = array_slice($boxes, 0, 5);
    
    // Only proceed if we have at least one box
    if (!empty($boxes)) :
?>

<section class="box-categories">
    <div class="container">
        <div class="box-categories__grid swiper boxCategoriesSwiper">
            <div class="swiper-wrapper">
                <?php 
                // Counter to determine the position of each box
                $counter = 0;
                
                foreach ($boxes as $box) :
                    // Get box fields
                    $title = $box['tytul'] ?? '';
                    $image = $box['zdjecie'] ?? '';
                    $link = $box['link'] ?? '';
                    
                    // Skip if no image or title
                    if (empty($image) && empty($title)) continue;
                    
                    // Increment counter
                    $counter++;
                    
                    // Add specific class based on position
                    $position_class = '';
                    if ($counter <= 3) {
                        $position_class = 'box-categories__item--top';
                    } else {
                        $position_class = 'box-categories__item--bottom';
                    }
                    
                    // Get image URL
                    $image_url = '';
                    if (!empty($image) && is_numeric($image)) {
                        $image_url = wp_get_attachment_image_url($image, 'full');
                    }
                ?>
                <div class="box-categories__item swiper-slide <?php echo esc_attr($position_class); ?>">
                    <?php if (!empty($link)) : ?>
                        <a href="<?php echo esc_url($link); ?>" class="box-categories__link">
                    <?php else: ?>
                        <div class="box-categories__link">
                    <?php endif; ?>
                    
                        <?php if (!empty($image_url)) : ?>
                            <div class="box-categories__image" style="background-image: url('<?php echo esc_url($image_url); ?>');">
                                <div class="box-categories__overlay"></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($title)) : ?>
                            <h3 class="box-categories__title"><?php echo esc_html($title); ?></h3>
                        <?php endif; ?>
                    
                    <?php if (!empty($link)) : ?>
                        </a>
                    <?php else: ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

    <?php endif; // End of boxes check ?>
<?php endif; // End of boxes_data check ?>
