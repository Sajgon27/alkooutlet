<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package alkooutlet
 */

get_header();
?>

<main class="error-404">
    <div class="container">
        <div class="error-404__wrapper">
            <div class="error-404__content">
                <div class="error-404__icon">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/404.svg" alt="Strona nie znaleziona" />
                    <span class="error-404__label">Zgubiliśmy ten smak</span>
                </div>
                
                <h1 class="error-404__title">NIE ZNALEŹLIŚMY TEJ STRONY</h1>
                
                <p class="error-404__text">
                    Wygląda na to, że podany adres jest nieaktualny lub strona została 
                    przeniesiona. Sprawdź, czy nie ma literówki, wróć do strony głównej albo
                    przejrzyj naszą ofertę. Jesteśmy pewni, że znajdziesz coś dla siebie.
                </p>
                
                <div class="error-404__buttons">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="error-404__button error-404__button--primary button button--primary">
                        Wróć do strony głównej
                    </a>
                    <a href="<?php echo esc_url(home_url('/sklep/')); ?>" class="error-404__button error-404__button--secondary button button--outline">
                        Sprawdź ofertę
                    </a>
                </div>
            </div>
            
            <div class="error-404__image">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/404.webp" alt="Shots z pomarańczą" />
            </div>
        </div>
    </div>
</main>

<?php
get_footer();