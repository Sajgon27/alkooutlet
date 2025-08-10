<?php

/**
 * Cart summary template
 * 
 * This template is used for the AJAX response fragments
 * 
 * @package SBS
 */

defined('ABSPATH') || exit;
?>

<!-- Cart Summary Section -->
<div class="cart-summary">
  <h3 class="cart-summary__title"><?php esc_html_e('Podsumowanie', 'sbs'); ?></h3>

  <?php if (WC()->cart->get_cart_contents_count() > 0) : ?>
    <div class="cart-summary__total">
      <?php
      $cart_total = WC()->cart->get_cart_contents_total() + WC()->cart->get_cart_contents_tax(); // Brutto
      $cart_total_formatted = wc_price($cart_total);
      echo $cart_total_formatted;
      ?>
      <?php //wc_price( WC()->cart->get_subtotal() ); 
      ?>
      <?php if (WC()->cart->get_discount_total() > 0) : ?>
        <span class="cart-summary__discount">
          - <?php echo wc_price(WC()->cart->get_discount_total()); ?>
        </span>
      <?php endif; ?>
    </div>

    <div class="cart-summary__shipping">

      <p><?php esc_html_e('+ koszty dostawy', 'sbs'); ?></p>

    </div>

    <?php if (wc_coupons_enabled()) : ?>
      <div class="cart-summary__coupon">
        <label for="coupon_code" class="cart-summary__coupon-label"><?php esc_html_e('Kod rabatowy', 'sbs'); ?></label>
        <form class="cart-summary__coupon-form">
          <input type="text" name="coupon_code" class="cart-summary__coupon-input" id="coupon_code" value="" placeholder="<?php esc_attr_e('Wprowadź kod rabatowy', 'sbs'); ?>" />
          <button type="submit" class="cart-summary__coupon-button">›</button>
        </form>

        <div class="cart-summary__coupon-message"></div>

        <?php if (WC()->cart->get_coupons()) : ?>
          <div class="cart-summary__applied-coupons">
            <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
              <div class="cart-summary__coupon-tag">
                <span><?php echo esc_html($code); ?></span>
                <a href="#" class="sbs-remove-coupon" data-coupon="<?php echo esc_attr($code); ?>">&times;</a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="cart-summary__checkout">
      <?php esc_html_e('Prześlij zamówienie', 'sbs'); ?>
    </a>
  <?php endif; ?>
</div>