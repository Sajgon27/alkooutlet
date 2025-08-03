<?php
/**
 * Newsletter Component Template
 *
 * @package alkooutlet
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Background image is set directly on the .newsletter element in _newsletter.scss
// The overlay element below creates a dark layer on top of the background image
?>

<section class="newsletter">
    <div class="newsletter__background" aria-hidden="true"></div>
    <div class="container">
        <div class="newsletter__wrapper">
            <div class="newsletter__content">
                <h2 class="newsletter__title">NEWSLETTER</h2>
                <p class="newsletter__description">Dołącz do grona naszych subskrybentów i bądź na bieżąco z wyjątkowymi promocjami, nowościami i limitowanymi edycjami.</p>
                
                <form class="newsletter__form" action="#" method="post">
                    <div class="newsletter__input-wrapper">
                        <input 
                            type="email" 
                            name="newsletter_email" 
                            class="newsletter__input" 
                            placeholder="Wpisz adres e-mail" 
                            required
                        >
                    </div>
                    
                    <p class="newsletter__consent">Zapisując się do newslettera, wyrażasz zgodę na otrzymywanie informacji handlowych drogą elektroniczną.</p>
                    
                    <button type="submit" class="newsletter__button button button--primary">
                        Zapisz się
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>