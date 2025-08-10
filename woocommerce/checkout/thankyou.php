<?php

/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.1.0
 *
 * @var WC_Order $order
 */

defined('ABSPATH') || exit;
?>

<div class="woocommerce-order">
    <?php if ($order) : ?>
        <section class="thankyou-page">
            <div class="container">
                <div class="thankyou-page__header">
                    <div class="thankyou-page__content">
                       
                        <div class="section-label">
                            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/icons/bookmark.svg" alt="<?php esc_attr_e('Zamówienie potwierdzone', 'alko'); ?>" />
                            <span class="section-label__text"><?php esc_html_e('Dziękujemy za zaufanie', 'alko'); ?></span>
                        </div>
                        <h2 class="thankyou-page__title"><?php esc_html_e('Zamówienie przyjęte z klasą', 'alko'); ?></h2>
                        <p class="thankyou-page__description">
                            <?php esc_html_e('Twoje zamówienie zostało pomyślnie złożone. Zadbamy teraz o każdy szczegół, aby dotarło do Ciebie szybko, bezpiecznie i w idealnym stanie. Cieszymy się, że wybrałeś nas i już wkrótce będziesz mógł cieszyć się swoim zakupem.', 'alko'); ?>
                        </p>
                        <div class="thankyou-page__buttons">
                            <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="button button--primary">
                                <?php esc_html_e('Sprawdź szczegóły zamówienia', 'alko'); ?>
                            </a>
                            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="button button--secondary">
                                <?php esc_html_e('Sprawdź ofertę', 'alko'); ?>
                            </a>
                        </div>
                    </div>
            
                </div>
                <div class="thankyou-page__image">
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/thankyou.webp" alt="<?php esc_attr_e('Zamówienie potwierdzone', 'alko'); ?>" />
                </div>
            </div>
        </section>
    <?php else : ?>
        <?php wc_get_template('checkout/order-received.php', array('order' => false)); ?>
    <?php endif; ?>
</div>