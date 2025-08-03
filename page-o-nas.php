<?php

/**
 * Template for the About Us (O nas) page
 *
 * @package AlkoOutlet
 */

get_header();

// Get ACF fields from the hero group
$hero_group = get_field('hero');
$naglowek = isset($hero_group['naglowek']) ? $hero_group['naglowek'] : '';
$tekst = isset($hero_group['tekst']) ? $hero_group['tekst'] : '';
$zdjecie = isset($hero_group['zdjecie']) ? $hero_group['zdjecie'] : '';

// Get image URL if image ID exists
$image_url = '';
if (!empty($zdjecie)) {
    $image_url = wp_get_attachment_image_url($zdjecie, 'large');
}

?>


<section class="about-hero">
    <div class="container">
        <div class="about-hero__top">

            <?php basic_wp_breadcrumbs(); ?>
            <?php if (!empty($naglowek)) : ?>
                <h1 class="about-hero__title"><?php echo esc_html($naglowek); ?></h1>
            <?php else : ?>
                <h1 class="about-hero__title"><?php echo esc_html(get_the_title()); ?></h1>
            <?php endif; ?>

        </div>
        <div class="about-hero__content">
            <div class="about-hero__text-column">



                <?php if (!empty($tekst)) : ?>
                    <div class="about-hero__description">
                        <?php echo wp_kses_post(nl2br($tekst)); ?>
                    </div>
                <?php endif; ?>
                <div class="about-hero__buttons">
                    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="button button--primary">Zobacz naszą ofertę</a>
                    <a href="<?php echo esc_url(home_url('/kontakt')); ?>" class="button button--secondary">Skontaktuj się z nami</a>
                </div>
            </div>
            <?php if (!empty($image_url)) : ?>
                <div class="about-hero__image-column">
                    <div class="about-hero__image-wrapper">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($naglowek); ?>" class="about-hero__image">
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php get_template_part('template-parts/components/logos-slider'); ?>

<?php
// Get ACF fields from the liczby group
$liczby_group = get_field('liczby');
if (is_array($liczby_group) && !empty($liczby_group)) {
    $naglowek = isset($liczby_group['naglowek']) ? $liczby_group['naglowek'] : '';
    $zdjecie = isset($liczby_group['zdjecie']) ? $liczby_group['zdjecie'] : '';
    $ikonka = isset($liczby_group['ikonka']) ? $liczby_group['ikonka'] : '';
    $ikonka_tekst = isset($liczby_group['ikonka_tekst']) ? $liczby_group['ikonka_tekst'] : '';
    $karty = isset($liczby_group['karta']) && is_array($liczby_group['karta']) ? $liczby_group['karta'] : [];

    // Get image URL if image ID exists
    $image_url = '';
    if (!empty($zdjecie)) {
        $image_url = wp_get_attachment_image_url($zdjecie, 'full');
    }

    // Get icon URL if icon ID exists
    $icon_url = '';
    if (!empty($ikonka)) {
        $icon_url = wp_get_attachment_image_url($ikonka, 'full');
    }

    if (!empty($karty) || !empty($naglowek)) :
?>
        <section class="about-numbers">
            <div class="container">
                <?php if (!empty($image_url)) : ?>
                    <div class="about-numbers__image-column">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($naglowek); ?>" class="about-numbers__image">
                    </div>
                <?php endif; ?>

                <div class="about-numbers__content-column">
                    <div class="about-numbers__header">
                        <?php if (!empty($icon_url)) : ?>
                            <div class="about-numbers__icon-wrapper">
                                <img src="<?php echo esc_url($icon_url); ?>" alt="" class="about-numbers__icon">
                                <?php if (!empty($ikonka_tekst)) : ?>
                                    <span class="about-numbers__icon-text"><?php echo esc_html($ikonka_tekst); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($naglowek)) : ?>
                            <h2 class="about-numbers__title"><?php echo esc_html($naglowek); ?></h2>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($karty)) : ?>
                        <div class="about-numbers__cards">
                            <?php foreach ($karty as $karta) :
                                $liczba = isset($karta['liczba']) ? $karta['liczba'] : '';
                                $tytul = isset($karta['tytul']) ? $karta['tytul'] : '';
                                $tekst = isset($karta['tekst']) ? $karta['tekst'] : '';

                                if (empty($liczba) && empty($tytul) && empty($tekst)) {
                                    continue; // Skip empty cards
                                }
                            ?>
                                <div class="about-numbers__card">
                                    <div class="about-numbers__card-left">
                                        <?php if (!empty($liczba)) : ?>
                                            <div class="about-numbers__number"><?php echo esc_html($liczba); ?></div>
                                        <?php endif; ?>

                                        <?php if (!empty($tytul)) : ?>
                                            <div class="about-numbers__card-title"><?php echo esc_html($tytul); ?></div>
                                        <?php endif; ?>
                                    </div>


                                    <?php if (!empty($tekst)) : ?>
                                        <div class="about-numbers__card-text"><?php echo wp_kses_post($tekst); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
<?php
    endif;
}
?>
<?php get_template_part('template-parts/components/cechy'); ?>
    <?php get_template_part('template-parts/components/newsletter'); ?>
<?php
get_footer();
?>