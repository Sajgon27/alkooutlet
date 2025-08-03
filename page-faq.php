<?php

/**
 * Template Name: FAQ Page
 * Template Post Type: page
 *
 */

get_header();
?>

<main class="faq-page">
    <div class="faq-layout">
        <div class="container">
            <div class="faq-layout__content">
                <div class="faq-layout__intro">
                    <?php basic_wp_breadcrumbs(); ?>
                   
                    <h1 class="faq-layout__title">FAQ</h1>
                    <p class="faq-layout__text">
                        Masz pytania dotyczące oferty, produktów lub zamówienia? Skontaktuj się z nami – jesteśmy tu, by pomóc, doradzić i zadbać o Twoje zadowolenie. Cenimy bezpośredni kontakt i szybką odpowiedź.
                    </p>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('kontakt'))); ?>" class="button button--primary">Zadaj pytanie</a>
                </div>
                <div class="faq-layout__accordion">
                    <?php
                    if (have_rows('karta')) :
                    ?>
                        <div class="accordion">
                            <?php

                            while (have_rows('karta')) : the_row();
                                $tytul = get_sub_field('pytanie');
                                $tresc = get_sub_field('odpowiedz');
                            ?>
                                <div class="accordion__item">
                                    <button class="accordion__header">
                                        <span class="accordion__title"><?php echo esc_html($tytul); ?></span>
                                        <span class="accordion__icon"></span>
                                    </button>
                                    <div class="accordion__content">
                                        <div class="accordion__content-inner">
                                            <?php echo wp_kses_post($tresc); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php
                    else :
                    ?>
                        <div class="faq-empty">
                            <p>Aktualnie nie ma dostępnych pytań i odpowiedzi.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>