<?php

/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.0.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart'); ?>


<div class="cart-page">
  <div class="page-title-wrapper">
    <div class="container">
      <div class="page-title-text">
        <?php woocommerce_breadcrumb(); ?>
        <h1 class="page-title"><?php esc_html_e('Twój koszyk', 'alko'); ?></h1>
      </div>
      
    </div>
  </div>

  <div class="cart-content">
    <div class="container">
      <!-- Cart Items Section -->
      <div class="cart-items">
        <?php if (WC()->cart->get_cart_contents_count() > 0) : ?>
          <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
            <?php do_action('woocommerce_before_cart_table'); ?>

            <?php wc_get_template('cart/cart-form.php'); ?>

            <?php do_action('woocommerce_after_cart_table'); ?>
          </form>
        <?php else : ?>
          <div class="cart-empty-message">
            <p><?php esc_html_e('Your cart is currently empty.', 'woocommerce'); ?></p>
            <a href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>" class="button">
              <?php esc_html_e('Return to shop', 'woocommerce'); ?>
            </a>
          </div>
        <?php endif; ?>
      </div>

      <!-- Cart Summary Section -->
      <?php wc_get_template('cart/cart-summary.php'); ?>
    </div>
  </div>
<?php do_action('woocommerce_after_cart'); ?>
</div>

