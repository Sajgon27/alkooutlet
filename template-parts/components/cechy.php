<?php
/**
 * Features Section Template
 *
 * @package AlkoOutlet
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Get features from options page
$features = get_field('cecha', 'option');

// Check if we have features to display
if (empty($features) || !is_array($features)) {
    return; // Don't output anything if no features
}
?>

<section class="features">
    <div class="container">
        <div class="features__grid">
            <?php foreach ($features as $feature) : 
                $title = isset($feature['tytul']) ? $feature['tytul'] : '';
                $text = isset($feature['tekst']) ? $feature['tekst'] : '';
                $icon = isset($feature['ikonka']) ? $feature['ikonka'] : '';
                
                if (empty($title) && empty($text) && empty($icon)) {
                    continue; // Skip empty features
                }
            ?>
                <div class="features__item">
                    <?php if (!empty($icon)) : 
                        // SVG icon from ACF field
                        $icon_url = wp_get_attachment_url($icon);
                        if ($icon_url) :
                    ?>
                        <div class="features__icon">
                            <img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($title); ?>">
                        </div>
                    <?php endif; endif; ?>
                    
                    <?php if (!empty($title)) : ?>
                        <h3 class="features__title"><?php echo esc_html($title); ?></h3>
                    <?php endif; ?>
                    
                    <?php if (!empty($text)) : ?>
                        <div class="features__text"><?php echo wp_kses_post($text); ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
