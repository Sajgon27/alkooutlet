<?php

/**
 * File: includes/woo-checkout-customer-type-nip.php
 * Title: Woo – Zakup jako (Osoba/Firma) + NIP (PL) – Checkout, Admin, E-maile
 *
 * Funkcje:
 *  - Checkout: przełącznik **Zakup jako:** (Osoba prywatna / Firma) z ikonami i CSS (bez borderu fieldsetu).
 *  - Gdy wybrane „Firma” → **Nazwa firmy** i **NIP** są wymagane (UI + walidacja po stronie serwera).
 *  - Walidacja NIP (PL) – checksum; zapisujemy do meta `_billing_nip`.
 *  - Zapamiętujemy `_customer_type` (company/private) w zamówieniu (+ preferencja usera).
 *  - Admin (Edycja zamówienia): NIP jako pole **zaraz po „Firma”**; wyświetlamy też **„Zakup jako: …”**.
 *  - Front (strona zamówienia / „Dziękujemy”): pokazujemy **„Zakup jako: …”** i **NIP**.
 *  - E-maile: dodajemy **tylko NIP** (bez „Zakup jako”).
 *
 * Wymagania: WooCommerce aktywny, klasyczny checkout.
 */

if (! defined('ABSPATH')) {
	exit;
}
if (! function_exists('WC')) {
	return;
}

/* ----------------------------- Helpers ----------------------------- */

/** Bieżący wybór typu klienta. */
function mywcc_selected_customer_type(): string
{
	if (isset($_POST['customer_type'])) {
		$t = sanitize_text_field(wp_unslash($_POST['customer_type']));
	} else {
		$t = WC()->checkout ? WC()->checkout->get_value('customer_type') : '';
		if (! $t && is_user_logged_in()) {
			$t = get_user_meta(get_current_user_id(), 'mywcc_customer_type', true);
		}
	}
	return in_array($t, array('private', 'company'), true) ? $t : 'private';
}
function mywcc_is_company(): bool
{
	return mywcc_selected_customer_type() === 'company';
}

/** Walidacja NIP (PL, 10 cyfr). */
function mywcc_validate_pl_nip(string $nip): bool
{
	$nip = preg_replace('/\D+/', '', $nip);
	if (! preg_match('/^\d{10}$/', $nip)) {
		return false;
	}
	$w = array(6, 5, 7, 2, 3, 4, 5, 6, 7);
	$s = 0;
	for ($i = 0; $i < 9; $i++) {
		$s += (int)$nip[$i] * $w[$i];
	}
	$c = $s % 11;
	return ($c !== 10) && ($c === (int)$nip[9]);
}

/* ------------------ UI: „Zakup jako” + ikony + style ------------------ */

add_action('woocommerce_before_checkout_billing_form', function () {
	$selected = mywcc_selected_customer_type();
?>
	<fieldset id="customer-type-switcher" class="mywcc-customer-type" role="radiogroup" aria-describedby="customer-type-help">

		<legend class="screen-reader-text"><?php esc_html_e('Rodzaj klienta', 'mywcc'); ?></legend>
		<p id="customer-type-help" class="screen-reader-text"><?php esc_html_e('Wybierz, czy kupujesz jako osoba prywatna czy firma.', 'mywcc'); ?></p>

		<div class="mywcc-segmented">
			<label class="mywcc-segment">
				<input type="radio" name="customer_type" value="private" <?php checked($selected, 'private'); ?> />

				<span class="mywcc-text"><?php esc_html_e('Kupuję jako osoba prywatna', 'mywcc'); ?></span>
			</label>

			<label class="mywcc-segment">
				<input type="radio" name="customer_type" value="company" <?php checked($selected, 'company'); ?> />

				<span class="mywcc-text"><?php esc_html_e('Kupuję jako firma', 'mywcc'); ?></span>
			</label>
		</div>
	</fieldset>
<?php
}, 10);

/* ---------------- Checkout fields: required + pozycje + klasy --------------- */

add_filter('woocommerce_checkout_fields', function (array $fields): array {
	$is_company = mywcc_is_company();

	// Nazwa firmy – sztywny układ i priorytet
	if (empty($fields['billing']['billing_company'])) {
		$fields['billing']['billing_company'] = array();
	}
	$fields['billing']['billing_company']['label']        = __('Nazwa firmy', 'mywcc');
	$fields['billing']['billing_company']['type']         = 'text';
	$fields['billing']['billing_company']['required']     = $is_company;
	$fields['billing']['billing_company']['priority']     = 120;
	$fields['billing']['billing_company']['class']        = array('form-row-first');
	$fields['billing']['billing_company']['autocomplete'] = 'organization';

	// NIP – druga kolumna
	$fields['billing']['billing_nip'] = array(
		'label'        => __('NIP', 'mywcc'),
		'type'         => 'text',
		'required'     => $is_company,
		'priority'     => 121,
		'class'        => array('form-row-last'),
		'autocomplete' => 'off',
	);

	if ($is_company) {
		$fields['billing']['billing_company']['class'][] = 'validate-required';
		$fields['billing']['billing_nip']['class'][]     = 'validate-required';
	}

	return $fields;
}, 9999);

/* -------------------- Style + JS (checkout, front) -------------------- */

add_action('wp_enqueue_scripts', function () {
	if (! is_checkout()) {
		return;
	}

	$css = <<<CSS
/* Dostępność */
.screen-reader-text{position:absolute!important;height:1px;width:1px;overflow:hidden;clip:rect(1px,1px,1px,1px);white-space:nowrap}

/* Fieldset bez obramowania (zgodnie z prośbą) */
.mywcc-customer-type{border:0;padding:0;margin:0 0 16px}

/* Tytuł */

.mywcc-title {margin: 1rem 0 .5rem 0;}

.mywcc-title strong {font-size: 16px; font-weight: 500; color: #312B2B;}


/* Kafelki */
.mywcc-segmented{display:flex;gap:10px;margin:0 0 20px}
@media only screen and (max-width: 500px) {
  .mywcc-segmented {
    flex-direction: column;
  }
}

/* Styl przycisku wg Twoich wytycznych + prefixy */
.mywcc-segment{
	-webkit-box-flex:1; -ms-flex:1; flex:1;
	display:-webkit-box; display:-ms-flexbox; display:flex;
	-webkit-box-align:center; -ms-flex-align:center; align-items:center;
	-webkit-box-pack:center; -ms-flex-pack:center; justify-content:center;
	gap:8px;
	background-color:white;
	border:1px solid #990F02;
	color:#111111CC;
	padding:12px 16px;
	font-size:16px;
	text-align:center;
    font-weight: 400;
	cursor:pointer;
	-webkit-transition:all .2s ease; transition:all .2s ease;
	user-select:none;
}
.mywcc-segment.is-active span {color:#fff;}
.mywcc-segment.is-active{background-color:#990F02;color:#fff !important;border-color:#990F02}
.mywcc-segment input[type="radio"]{position:absolute;opacity:0;width:1px;height:1px;pointer-events:none}
.mywcc-segment input[type="radio"]:focus-visible + .mywcc-icon,
.mywcc-segment:focus-within{outline:2px solid currentColor;outline-offset:2px;}

/* Ikony – kolory przez currentColor */
.mywcc-icon{width:18px;height:18px;display:inline-block;line-height:0}
.mywcc-icon svg{display:block}
.mywcc-icon svg path{fill:currentColor}

/* Ukrywanie wierszy firmy gdy nie dotyczy */
#billing_company_field[hidden],#billing_nip_field[hidden]{display:none!important}

/* Sufiks gwiazdki/opcjonalne w labelach */
.mywcc-label-suffix{margin-left:.25em}
CSS;

	$js = <<<JS
(function(){
  function initCustomerTypeUI(){
    var switcher = document.getElementById('customer-type-switcher');
    if (!switcher || switcher.dataset.bound === '1') return;
    switcher.dataset.bound = '1';

    function isCompany(){
      var c = switcher.querySelector('input[name="customer_type"]:checked');
      return c && c.value === 'company';
    }
    function updateSegmentsActive(){
      switcher.querySelectorAll('.mywcc-segment').forEach(function(lbl){
        var inp = lbl.querySelector('input[type="radio"]');
        lbl.classList.toggle('is-active', !!(inp && inp.checked));
      });
    }
    function setRequiredUI(inputId, required){
      var row   = document.getElementById(inputId + '_field');
      var input = document.getElementById(inputId);
      if (!row || !input) return;

      // LABEL reset + suffix
      var label = row.querySelector('label');
      if (label){
        if (!label.dataset.baseText){
          var base = (label.firstChild && label.firstChild.nodeType === 3) ? label.firstChild.textContent : label.textContent;
          label.dataset.baseText = (base || '').replace(/\\*|\\(opcjonalne\\)/gi,'').trim();
        }
        label.textContent = label.dataset.baseText;
        var suffix = document.createElement('span'); suffix.className='mywcc-label-suffix';
        if (required){
          var star = document.createElement('span'); star.className='required'; star.setAttribute('aria-hidden','true'); star.textContent='*';
          suffix.appendChild(star); label.classList.add('required_field');
        } else {
          var opt = document.createElement('span'); opt.className='optional'; opt.textContent='(opcjonalne)';
          suffix.appendChild(opt); label.classList.remove('required_field');
        }
        label.appendChild(suffix);
      }

      row.classList.toggle('validate-required', required);
      if (required){ input.disabled = false; input.setAttribute('required','required'); input.setAttribute('aria-required','true'); }
      else { input.removeAttribute('required'); input.removeAttribute('aria-required'); input.disabled = true; }
    }
    function toggleCompanyFields(){
      var company = isCompany();
      ['billing_company','billing_nip'].forEach(function(id){
        var row = document.getElementById(id + '_field');
        if (row){ row.hidden = !company; row.setAttribute('aria-hidden', String(!company)); }
        setRequiredUI(id, company);
      });
      updateSegmentsActive();
    }

    switcher.querySelectorAll('input[name="customer_type"]').forEach(function(r){ r.addEventListener('change', toggleCompanyFields, {passive:true}); });
    toggleCompanyFields();
  }

  document.addEventListener('DOMContentLoaded', initCustomerTypeUI);
  if (window.jQuery){ jQuery(function($){ $(document.body).on('updated_checkout', function(){ setTimeout(initCustomerTypeUI,0); }); }); }
})();
JS;

	wp_register_style('mywcc-checkout', false, array(), null);
	wp_enqueue_style('mywcc-checkout');
	wp_add_inline_style('mywcc-checkout', $css);

	wp_register_script('mywcc-checkout', '', array(), null, true);
	wp_enqueue_script('mywcc-checkout');
	wp_add_inline_script('mywcc-checkout', $js);
}, 20);

/* -------- Walidacja submit (bez dublowania braków) + zapamiętanie -------- */

add_action('woocommerce_checkout_process', function () {
	$type = isset($_POST['customer_type']) ? sanitize_text_field(wp_unslash($_POST['customer_type'])) : 'private';
	if ('company' === $type) {
		$nip_raw = isset($_POST['billing_nip']) ? wp_unslash($_POST['billing_nip']) : '';
		$nip     = preg_replace('/\D+/', '', $nip_raw);
		$_POST['billing_nip'] = $nip;
		if ($nip && ! mywcc_validate_pl_nip($nip)) {
			wc_add_notice(__('NIP jest nieprawidłowy.', 'mywcc'), 'error');
		}
	}
	$_POST['customer_type'] = in_array($type, array('private', 'company'), true) ? $type : 'private';
});

/* -------------------- Zapis meta + preferencja usera -------------------- */

add_action('woocommerce_checkout_update_order_meta', function ($order_id) {
	if (! empty($_POST['customer_type'])) {
		update_post_meta($order_id, '_customer_type', sanitize_text_field(wp_unslash($_POST['customer_type'])));
	}
	if (isset($_POST['billing_nip'])) {
		update_post_meta($order_id, '_billing_nip', preg_replace('/\D+/', '', wp_unslash($_POST['billing_nip'])));
	}
	if (is_user_logged_in() && ! empty($_POST['customer_type'])) {
		update_user_meta(get_current_user_id(), 'mywcc_customer_type', sanitize_text_field(wp_unslash($_POST['customer_type'])));
	}
}, 10);

/* ---------------- Admin: NIP jako pole po „Firma” w edycji ---------------- */

add_filter('woocommerce_admin_billing_fields', function (array $fields, $order, string $context) {
	$nip_field = array(
		'label'         => __('NIP', 'mywcc'),
		'placeholder'   => 'Wpisz NIP',
		'show'          => true,
		'class'         => 'short',
		'wrapper_class' => 'form-field-wide',
	);
	$new = array();
	foreach ($fields as $key => $data) {
		$new[$key] = $data;
		if ('company' === $key) {
			$new['nip'] = $nip_field;
		} // zapis/odczyt z meta `_billing_nip`
	}
	if (! isset($new['nip'])) {
		$new['nip'] = $nip_field;
	} // awaryjnie na końcu
	return $new;
}, 10, 3);

/* -------- Admin: „Zakup jako: …” (pod danymi do faktury lub niżej) -------- */

add_action('woocommerce_admin_order_data_after_billing_address', function ($order) {
	$type = $order instanceof WC_Order ? $order->get_meta('_customer_type') : '';
	if ($type) {
		echo '<p><strong>' . esc_html__('Zakup jako:', 'mywcc') . '</strong> ' . esc_html($type === 'company' ? __('Firma', 'mywcc') : __('Osoba prywatna', 'mywcc')) . '</p>';
	}
}, 99);

// Fallback (jeśli motyw/plugin zmienił układ i powyższe nie widać):
add_action('woocommerce_admin_order_data_after_order_details', function ($order) {
	$type = $order instanceof WC_Order ? $order->get_meta('_customer_type') : '';
	if ($type) {
		echo '<p style="margin-top:8px;"><strong>' . esc_html__('Zakup jako:', 'mywcc') . '</strong> ' . esc_html($type === 'company' ? __('Firma', 'mywcc') : __('Osoba prywatna', 'mywcc')) . '</p>';
	}
}, 99);

/* --------------- E-maile: tylko NIP (bez „Zakup jako”) --------------- */

add_filter('woocommerce_email_order_meta_fields', function (array $fields, $sent_to_admin, $order) {
	if ($order instanceof WC_Order) {
		$nip = $order->get_meta('_billing_nip');
		if ($nip) {
			$fields['billing_nip'] = array('label' => __('NIP', 'mywcc'), 'value' => $nip);
		}
	}
	return $fields;
}, 10, 3);

/* --------------- Front: „Zakup jako” + NIP na podsumowaniu --------------- */

add_action('woocommerce_order_details_after_customer_details', function ($order) {
	if (! $order instanceof WC_Order) {
		return;
	}
	$type = $order->get_meta('_customer_type');
	$nip  = $order->get_meta('_billing_nip');
	echo '<section class="woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses"><div class="woocommerce-column">';
	if ($type) {
		echo '<p><strong>' . esc_html__('Zakup jako:', 'mywcc') . '</strong> ' . esc_html($type === 'company' ? __('Firma', 'mywcc') : __('Osoba prywatna', 'mywcc')) . '</p>';
	}
	if ($nip) {
		echo '<p><strong>' . esc_html__('NIP', 'mywcc') . ':</strong> ' . esc_html($nip) . '</p>';
	}
	echo '</div></section>';
}, 10);

// Fallback (gdy motyw nie wywołuje poprzedniego hooka):
add_action('woocommerce_order_details_after_order_table', function ($order) {
	if (! $order instanceof WC_Order) {
		return;
	}
	$type = $order->get_meta('_customer_type');
	$nip  = $order->get_meta('_billing_nip');
	if (! $type && ! $nip) {
		return;
	}
	echo '<div class="woocommerce-order-overview__customer-type" style="margin-top:12px">';
	if ($type) {
		echo '<p><strong>' . esc_html__('Zakup jako:', 'mywcc') . '</strong> ' . esc_html($type === 'company' ? __('Firma', 'mywcc') : __('Osoba prywatna', 'mywcc')) . '</p>';
	}
	if ($nip) {
		echo '<p><strong>' . esc_html__('NIP', 'mywcc') . ':</strong> ' . esc_html($nip) . '</p>';
	}
	echo '</div>';
}, 99);
