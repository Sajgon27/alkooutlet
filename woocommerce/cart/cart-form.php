<?php
/**
 * Cart form template
 * 
 * This template is used for the AJAX response fragments
 * 
 * @package SBS
 */

defined('ABSPATH') || exit;
?>

<form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
  <?php do_action('woocommerce_before_cart_table'); ?>

  <!-- For ajax updates -->
<table class="shop_table cart-items__table" cellspacing="0">
    <thead>
      <tr>
        <th class="product-thumbnail"><span class="screen-reader-text"><?php esc_html_e('Thumbnail image', 'woocommerce'); ?></span></th>
        <th class="product-name"><?php esc_html_e('Product', 'woocommerce'); ?></th>
        <th class="product-actions"><?php esc_html_e('Actions', 'woocommerce'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php do_action('woocommerce_before_cart_contents'); ?>

      <?php
      foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
        $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
        $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);

        if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
          $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
      ?>
          <tr class="cart_item" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>">
            <td class="product-thumbnail">
              <?php
              $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
              if (!$product_permalink) {
                echo $thumbnail; // PHPCS: XSS ok.
              } else {
                printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail); // PHPCS: XSS ok.
              }
              ?>
            </td>

            <td class="product-name" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
              <?php
              // Get the top-level product category
              $terms = get_the_terms($product_id, 'product_cat');
              if ($terms && !is_wp_error($terms)) {
                // Find top-level category
                $top_level_cat = null;
                foreach ($terms as $term) {
                  $ancestors = get_ancestors($term->term_id, 'product_cat');
                  if (empty($ancestors)) {
                    $top_level_cat = $term;
                    break;
                  }
                }
                
                // If no top-level found, use the first category
                if (!$top_level_cat && !empty($terms)) {
                  $top_level_cat = $terms[0];
                }
                
                // Display category name if found
                if ($top_level_cat) {
                  echo '<div class="product-category">' . esc_html($top_level_cat->name) . '</div>';
                }
              }
              
              // Display product name
              if (!$product_permalink) {
                echo wp_kses_post($product_name . '&nbsp;');
              } else {
                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
              }

              do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

              // Meta data
              echo wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.


              // Display unit price in product name area for mobile
              //  echo '<span class="product-price">' . apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key) . '</span>';

              if ($_product->is_sold_individually()) {
                $min_quantity = 1;
                $max_quantity = 1;
                echo '<div class="quantity"><input type="number" class="qty" name="cart[' . esc_attr($cart_item_key) . '][qty]" value="1" readonly /></div>';
              } else {
                $min_quantity = 0;
                $max_quantity = $_product->get_max_purchase_quantity();
              ?>
                <div class="quantity">
                  <button type="button" class="qty-btn quantity-button qty-minus">-</button>
                  <input type="number" class="qty" name="cart[<?php echo esc_attr($cart_item_key); ?>][qty]" value="<?php echo esc_attr($cart_item['quantity']); ?>" min="1" max="<?php echo esc_attr($max_quantity); ?>" step="1" inputmode="numeric" />
                  <button type="button" class="qty-btn quantity-button qty-plus">+</button>
                </div>
              <?php
              }
              ?>
            </td>

            <td class="product-actions" data-title="<?php esc_attr_e('Actions', 'woocommerce'); ?>">
              <?php
              echo sprintf(
                '<a href="#" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><img src="%s/assets/icons/trash.svg" alt="%s" /></a>',
                esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), wp_strip_all_tags($product_name))),
                esc_attr($product_id),
                esc_attr($_product->get_sku()),
                esc_url(get_template_directory_uri()),
                esc_attr__('Remove', 'woocommerce')
              );
              ?>
              <div class="product-price-container">
                <span class="product-price-singular">
                  <?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?>
                  / szt.
                </span>

                <span class="product-price-total">
                  <?php
                  echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                  ?>
                </span>
              </div>
            </td>
          </tr>
      <?php
        }
      }
      ?>

      <?php do_action('woocommerce_cart_contents'); ?>
      <tr>
        <td colspan="6" class="actions">
          <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
        </td>
      </tr>

      <?php do_action('woocommerce_after_cart_contents'); ?>
    </tbody>
  </table>

  <div class="cart-items__notice">
    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/information.svg'); ?>" alt="<?php esc_attr_e('Cart Notice', 'sbs'); ?>" />
    <p><?php esc_html_e('Nie zwlekaj z zakupem - dodanie produktów do koszyka nie oznacza ich rezerwacji', 'sbs'); ?></p>
  </div>
  <?php do_action('woocommerce_after_cart_table'); ?>

  <?php
  // Pod loyalty program
  do_action('alko_after_cart_form'); ?>
</form>
