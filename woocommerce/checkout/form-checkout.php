<?php

/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if (! defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_before_checkout_form', $checkout);

// If checkout registration is disabled and not logged in, the user cannot checkout.
if (! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}

?>

<div class="checkout-page">
    <div class="page-title-wrapper">
        <div class="container">
            <div class="page-title-text">
                <?php woocommerce_breadcrumb(); ?>
                <h1 class="page-title"><?php esc_html_e('Finalizacja zamówienia', 'alko'); ?></h1>
            </div>
          
        </div>
    </div>

    <div class="checkout-content">
        <div class="container">
            <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
                <div class="checkout-wrapper">

                    <div class="checkout-form">
                        <?php if ($checkout->get_checkout_fields()) : ?>

                            <div class="checkout-form__section">
                                <h4 class="checkout-form__title"><?php esc_html_e('Adres dostawy', 'alko'); ?></h4>
                            </div>
                            <div id="customer_details">
                                <div class="checkout-form__section">
                                    <?php do_action('woocommerce_checkout_billing'); ?>
                                </div>

                                <?php do_action('woocommerce_checkout_shipping'); ?>
                            </div>

                        <?php endif; ?>

                        <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>

                        <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
                            <div class="checkout-form__section">
                                <h4 class="checkout-form__title"><?php esc_html_e('Sposób dostawy', 'alko'); ?></h4>
                                <div class="checkout-form__shipping-methods">
                                    <?php do_action('woocommerce_review_order_before_shipping'); ?>
                                    <?php wc_cart_totals_shipping_html(); ?>
                                    <?php do_action('woocommerce_review_order_after_shipping'); ?>

                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="checkout-form__section">
                            <h3 class="checkout-form__title" id="order_review_heading"><?php esc_html_e('Sposób płatności', 'alko'); ?></h3>
                        </div>

                        <?php do_action('woocommerce_checkout_before_order_review'); ?>

                        <div id="order_review" class="woocommerce-checkout-review-order">
                            <?php do_action('woocommerce_checkout_order_review'); ?>
                        </div>

                        <?php do_action('woocommerce_checkout_after_order_review'); ?>
                    </div>

                    <div class="checkout-summary">
                        <h3 class="checkout-summary__title"><?php esc_html_e('Twój koszyk', 'alko'); ?></h3>

                        <?php woocommerce_order_review(); ?>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>