<?php

/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined('ABSPATH') || exit;
?> <div class="my-account-page">
    <div class="container">
        <div class="my-account-page__header">
            <div class="my-account-page__content">
                <div class="my-account-page__breadcrumbs">
                    <?php
                    woocommerce_breadcrumb();
                    ?>
                </div>
       
                <h1 class="my-account-page__title"><?php esc_html_e('Twoje konto', 'alko'); ?></h1>
                <div class="my-account-page__description">Tutaj możesz zarządzać swoimi danymi, hasłem, adresami dostawy oraz zapisanymi metodami płatności. Wszystko w jednym miejscu – wygodnie i bezpiecznie.</div>
            </div>
        
        </div>
        <?php
        /**
         * My Account navigation.
         *
         * @since 2.6.0
         */

        ?>
        <div class="my-account-page-dashboard">


            <?php
            do_action('woocommerce_account_navigation'); ?>

            <div class="woocommerce-MyAccount-content">

                <?php
                /**
                 * My Account content.
                 *
                 * @since 2.6.0
                 */
                do_action('woocommerce_account_content');
                ?>
            </div>
        </div>
    </div>
</div>