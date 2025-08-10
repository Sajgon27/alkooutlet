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

<div class="contact">
    <div class="contact-intro">


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

                <!-- Content Grid - Combined Middle and Right columns into one grid -->
                <div class="contact__column contact__column--content">
                    <div class="contact__content-grid">
                        <?php
                        // Check if ACF is active and the repeater field exists
                        if (function_exists('have_rows') && have_rows('box')) {
                            $box_count = 0;

                            // First Box (Top Left)
                            if (have_rows('box')) {
                                the_row();

                                $icon_id = get_sub_field('ikonka');
                                $title = get_sub_field('tytul');
                                $text = get_sub_field('tekst');
                                $button_text = get_sub_field('przycisk');
                                $link = get_sub_field('link');
                                $value = get_sub_field('wartosc');
                        ?>

                                <div class="contact__card contact__item contact__item--box1">
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

                                $box_count++;
                            }

                            // Image 1 (Bottom Left - spans 2 rows)
                            ?>
                            <div class="contact__image contact__image--middle contact__item contact__item--image1">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/contact/2.webp" alt="Contact Image">
                            </div>

                            <!-- Image 2 (Top Right) -->
                            <div style="background-image:url('<?php echo get_template_directory_uri(); ?>/assets/images/contact/1.webp')" class="contact__image contact__image--right contact__item contact__item--image2">

                            </div>

                            <?php
                            // Second and Third Box (Middle and Bottom Right)
                            if (have_rows('box')) {
                                // Skip the first box (already displayed)
                                while (have_rows('box') && $box_count < 3) {
                                    the_row();

                                    if ($box_count > 0) {
                                        $icon_id = get_sub_field('ikonka');
                                        $title = get_sub_field('tytul');
                                        $text = get_sub_field('tekst');
                                        $button_text = get_sub_field('przycisk');
                                        $link = get_sub_field('link');
                                        $value = get_sub_field('wartosc');

                                        $box_class = ($box_count == 1) ? 'box2' : 'box3';
                            ?>

                                        <div class="contact__card contact__item contact__item--<?php echo $box_class; ?>">
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
                        }
                        ?>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

<section class="contact-form-section">
    <div class="container">
        <div class="contact-form-section__wrapper">
            <div class="contact-form-section__content">
                <div class="contact-form-section__icon-wrap">
                    <div class="contact-form-section__icon">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/pen.svg" alt="Contact">
                    </div>
                    <span class="contact-form-section__contact-text">Skontaktuj się z nami</span>
                </div>
                <h2 class="contact-form-section__title">FORMULARZ KONTAKTOWY</h2>
                <p class="contact-form-section__description">Jesteśmy tu, by słuchać. Napisz do nas, jeśli chcesz zapytać o produkt, podzielić się opinią lub po prostu rozpocząć rozmowę.</p>
            </div>
            <div class="contact-form-section__form">
                <?php echo do_shortcode('[contact-form-7 id="da7b3f8" title="Formularz 1"]'); ?>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
