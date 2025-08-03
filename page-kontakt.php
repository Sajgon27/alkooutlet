<?php
/**
 * Template Name: Contact Page
 * 
 * Template for displaying contact information with cards and images.
 *
 * @package alkooutlet
 */

get_header();
?>

<main class="contact">
    <div class="container">
        <div class="contact__grid">
            <!-- Left Column - Breadcrumbs, Title, Text -->
            <div class="contact__column contact__column--left">
                <?php 
                // Display breadcrumbs if function exists
                if (function_exists('basic_wp_breadcrumbs')) {
                    basic_wp_breadcrumbs();
                }
                ?>
                
                <h1 class="contact__title">KONTAKT</h1>
                
                <div class="contact__intro">
                    <p>Masz pytania dotyczące oferty, produktów lub zamówienia? Skontaktuj się z nami – jesteśmy tu, by pomóc, doradzić i zadbać o Twoje zadowolenie. Cenimy bezpośredni kontakt i szybką odpowiedź.</p>
                </div>
            </div>
            
            <!-- Middle Column - Card Box + Image -->
            <div class="contact__column contact__column--middle">
                <div class="contact__grid-content">
                    <?php
                    // Check if ACF is active and the repeater field exists
                    if (function_exists('have_rows') && have_rows('box')) {
                        $box_count = 0;
                        while (have_rows('box')) {
                            the_row();
                            
                            // Display only the first box in the middle column
                            if ($box_count == 0) {
                                $icon_id = get_sub_field('ikonka');
                                $title = get_sub_field('tytul');
                                $text = get_sub_field('tekst');
                                $button_text = get_sub_field('przycisk');
                                $link = get_sub_field('link');
                                $value = get_sub_field('wartosc');
                                ?>
                                
                                <div class="contact__card">
                                    <?php if ($icon_id) : ?>
                                        <div class="contact__card-icon">
                                            <?php echo wp_get_attachment_image($icon_id, 'thumbnail', false, array('alt' => esc_attr($title))); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($title) : ?>
                                        <h2 class="contact__card-title"><?php echo esc_html($title); ?></h2>
                                    <?php endif; ?>
                                    
                                    <?php if ($text) : ?>
                                        <p class="contact__card-text"><?php echo esc_html($text); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if ($value) : ?>
                                        <p class="contact__card-value"><?php echo esc_html($value); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if ($button_text && $link) : ?>
                                        <a href="<?php echo esc_url($link); ?>" class="contact__card-button button button--secondary"><?php echo esc_html($button_text); ?></a>
                                    <?php endif; ?>
                                </div>
                                
                                <?php
                            }
                            $box_count++;
                        }
                        // Reset the row position for later use
                        reset_rows();
                    }
                    ?>
                    
                    <div class="contact__image contact__image--middle">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/contact/1.webp" alt="Contact Image">
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Two Card Boxes + Image -->
            <div class="contact__column contact__column--right">
                <div class="contact__grid-content">
                    <div class="contact__image contact__image--right">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/contact/2.webp" alt="Contact Image">
                    </div>
                    
                    <?php
                    // Check if ACF is active and the repeater field exists
                    if (function_exists('have_rows') && have_rows('box')) {
                        $box_count = 0;
                        while (have_rows('box')) {
                            the_row();
                            
                            // Skip the first box (already displayed in middle column)
                            if ($box_count > 0) {
                                $icon_id = get_sub_field('ikonka');
                                $title = get_sub_field('tytul');
                                $text = get_sub_field('tekst');
                                $button_text = get_sub_field('przycisk');
                                $link = get_sub_field('link');
                                $value = get_sub_field('wartosc');
                                ?>
                                
                                <div class="contact__card">
                                    <?php if ($icon_id) : ?>
                                        <div class="contact__card-icon">
                                            <?php echo wp_get_attachment_image($icon_id, 'thumbnail', false, array('alt' => esc_attr($title))); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($title) : ?>
                                        <h2 class="contact__card-title"><?php echo esc_html($title); ?></h2>
                                    <?php endif; ?>
                                    
                                    <?php if ($text) : ?>
                                        <p class="contact__card-text"><?php echo esc_html($text); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if ($value) : ?>
                                        <p class="contact__card-value"><?php echo esc_html($value); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if ($button_text && $link) : ?>
                                        <a href="<?php echo esc_url($link); ?>" class="contact__card-button button button--outline"><?php echo esc_html($button_text); ?></a>
                                    <?php endif; ?>
                                </div>
                                
                                <?php
                            }
                            $box_count++;
                        }
                    }
                    ?>
                </div>
                
             
            </div>
        </div>
    </div>
</main>

<?php
get_footer();
