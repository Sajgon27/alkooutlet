<?php
get_header();

// Pobierz produkty z sesji lub parametrów URL
$compare_products = array();

if (isset($_SESSION['compare_products'])) {
    $compare_products = $_SESSION['compare_products'];
} elseif (isset($_GET['products'])) {
    $product_ids = explode(',', sanitize_text_field($_GET['products']));
    $compare_products = array_slice($product_ids, 0, 3); // Max 3 produkty
}

// Usuń nieistniejące produkty
$compare_products = array_filter($compare_products, function ($id) {
    return get_post_status($id) === 'publish';
});


$product_objects = array();
foreach ($compare_products as $product_id) {
    $product = wc_get_product($product_id);
    if ($product) {
        $product_objects[] = $product;
    }
}


// Przygotuj standardowe atrybuty do wyświetlenia
$standard_attributes = array(
    'short_description' => __('Krótki opis', 'bayonet'),
    'akceptowana-grubosc-tasmy' => __('Akceptowana grubość taśmy', 'bayonet'),
    'dlugosc' => __('Długość', 'bayonet'),
    'klamra' => __('Klamra', 'bayonet'),
    'kolor-klamry' => __('Kolor klamry', 'bayonet'),
    'kolor-pasa' => __('Kolor pasa', 'bayonet'),
    'material' => __('Materiał', 'bayonet'),
    'model' => __('Model', 'bayonet'),
    'otwor-bramy' => __('Otwór bramy', 'bayonet'),
    'pokrycie-klamry' => __('Pokrycie klamry', 'bayonet'),
    'rodzaj-klamry' => __('Rodzaj klamry', 'bayonet'),
    'rodzaj-klamry-pasa' => __('Rodzaj klamry pasa', 'bayonet'),
    'rodzaj-pasa' => __('Rodzaj pasa', 'bayonet'),
    'rozmiar' => __('Rozmiar', 'bayonet'),
    'szerokosc' => __('Szerokość', 'bayonet'),
    'szerokosc-klamry' => __('Szerokość klamry', 'bayonet'),
    'szerokosc-pasa' => __('Szerokość pasa', 'bayonet'),
    'sztywnosc' => __('Sztywność', 'bayonet'),
    'sztywnosc-pasa' => __('Sztywność pasa', 'bayonet'),
    'tasma-pas' => __('Taśma/pas', 'bayonet'),
    'typ-montazu' => __('Typ montażu', 'bayonet'),
    'usztywnienie' => __('Usztywnienie', 'bayonet'),
    'waga' => __('Waga', 'bayonet'),
    'wytrzymalosc-klamry' => __('Wytrzymałość klamry', 'bayonet'),
    'zamkniecie' => __('Zamknięcie', 'bayonet'),
    'zastosowanie' => __('Zastosowanie', 'bayonet')
);

// Pobierz wszystkie atrybuty z produktów
$all_attributes = array();
foreach ($product_objects as $product) {
    $attributes = $product->get_attributes();
    foreach ($attributes as $attribute) {
        $name = $attribute->get_name();
        if (!isset($all_attributes[$name])) {
            $all_attributes[$name] = wc_attribute_label($name);
        }
    }
}

// Połącz standardowe z dostępnymi atrybutami
$all_display_attributes = array_merge($standard_attributes, $all_attributes);

// Funkcja sprawdzająca czy atrybut ma wartość w którymkolwiek z produktów
function attribute_exists_in_products($products, $attr_key)
{
    foreach ($products as $product) {
        $value = get_product_attribute_value($product, $attr_key);
        if (!empty($value)) {
            return true;
        }
    }
    return false;
}

// Funkcja do pobierania wartości atrybutu
function get_product_attribute_value($product, $attr_key)
{
    if ($attr_key === 'short_description') {
        return $product->get_short_description();
    } else {
        $attributes = $product->get_attributes();
        if (isset($attributes[$attr_key])) {
            $attribute = $attributes[$attr_key];
            if ($attribute->is_taxonomy()) {
                $terms = wp_get_post_terms($product->get_id(), $attribute->get_name());
                return implode(', ', wp_list_pluck($terms, 'name'));
            } else {
                return implode(', ', $attribute->get_options());
            }
        }
    }
    return '';
}

// Sprawdź czy atrybut ma różne wartości między produktami
function has_attribute_differences($products, $attr_key)
{
    $values = array();
    foreach ($products as $product) {
        $value = get_product_attribute_value($product, $attr_key);
        if (!empty($value)) {
            $values[] = $value;
        }
    }
    return count(array_unique($values)) > 1;
}

// Filtruj atrybuty, które istnieją w przynajmniej jednym produkcie
$display_attributes = array();
foreach ($all_display_attributes as $attr_key => $attr_label) {
    if (attribute_exists_in_products($product_objects, $attr_key)) {
        $display_attributes[$attr_key] = $attr_label;
    }
}
?>


<section class="porownywarka-table-layout">
    <div class="container">


   

        <?php if (empty($product_objects)): ?>
            <!-- Pusty stan -->
            <div class="empty-compare-state">
                  <?php basic_wp_breadcrumbs(); ?>
                <h1><?php _e('Ups! nie wybrałeś żadnych produktów do porównania.', 'bayonet'); ?></h1>
                <p><?php _e('Wybierz kategorię produktów i zaznacz min. 2 produkty, aby je porównać.', 'bayonet'); ?></p>
            </div>

        <?php else: ?>
                 <div class="porownywarka-header">
            <?php basic_wp_breadcrumbs(); ?>
            <h1><?php _e('Zestawienie smaków, pojemności i okazji', 'bayonet'); ?></h1>

        </div>

            <!-- Tabela porównawcza -->
            <div class="compare-table">
                <!-- Wiersz z produktami -->
                <div class="products-table-row">
                    <div class="row-label-cell">

                    </div>

                    <?php
                    // Zawsze wyświetl 3 kolumny
                    for ($i = 0; $i < 3; $i++):
                        if (isset($product_objects[$i])):
                            $product = $product_objects[$i];

                            // Prawidłowe ustawienie globalnych zmiennych
                            global $post, $product;
                            $post = get_post($product->get_id());
                            $GLOBALS['post'] = $post;
                            $GLOBALS['product'] = $product;
                            setup_postdata($post);
                    ?>
                            <div class="product-table-cell">
                                <div class="product-header-compare">
                                    <div class="compare-specific-actions">
                                        <a href="#" class="remove-product-btn" data-product-id="<?php echo $product->get_id(); ?>">
                                            <?php _e('✕   Usuń z porównania', 'bayonet'); ?>
                                        </a>
                                    </div>
                                    <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" class="product-image-link">
                                        <?php echo $product->get_image('medium'); ?>
                                    </a>
                                    <h6 class="product-title">
                                        <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>">
                                            <?php echo esc_html($product->get_name()); ?>
                                        </a>
                                    </h6>
                                </div>
                            </div>
                        <?php
                            wp_reset_postdata();
                        else:
                        ?>
                            <div class="product-table-cell empty-cell">
                                <div class="add-product-placeholder">
                                    <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="add-product-large"
                                        title="<?php esc_attr_e('Dodaj produkt do porównania', 'bayonet'); ?>">
                                        <div class="plus-icon">+</div>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>

                <!-- Wiersze z atrybutami -->
                <?php foreach ($display_attributes as $attr_key => $attr_label): ?>
                    <?php $has_differences = has_attribute_differences($product_objects, $attr_key); ?>
                    <div class="attribute-table-row <?php echo $has_differences ? 'has-differences' : ''; ?>">
                        <div class="row-label-cell">
                            <span class="attribute-name"><?php echo esc_html($attr_label); ?></span>
                        </div>

                        <?php
                        // Wartości dla każdego produktu
                        for ($i = 0; $i < 3; $i++):
                            if (isset($product_objects[$i])):
                                $product = $product_objects[$i];
                                $value = get_product_attribute_value($product, $attr_key);
                        ?>
                                <div class="attribute-value-cell">
                                    <span
                                        class="attribute-value <?php echo ($has_differences && $attr_key !== 'short_description') ? 'different' : ''; ?>">
                                        <?php
                                        if (!empty($value)) {
                                            if ($attr_key === 'short_description') {
                                                echo wp_kses_post($value);
                                            } else {
                                                echo esc_html($value);
                                            }
                                        } else {
                                            echo '—';
                                        }
                                        ?>
                                    </span>
                                </div>
                            <?php else: ?>
                                <div class="attribute-value-cell empty-cell">
                                    <span class="attribute-value">—</span>
                                </div>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php get_template_part('template-parts/components/pytanie'); ?>
<?php get_template_part('template-parts/components/newsletter'); ?>

<script>
    // Przekaż dane do JavaScript
    window.compareData = {
        ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
        nonce: '<?php echo wp_create_nonce('compare_products_nonce'); ?>',
        maxProducts: 3,
        currentProducts: <?php echo json_encode(array_map(function ($p) {
                                return $p->get_id();
                            }, $product_objects)); ?>,
        // Dodaj tłumaczenia dla JavaScript
        translations: {
            confirmRemove: '<?php echo esc_js(__('Czy na pewno chcesz usunąć ten produkt z porównania?', 'bayonet')); ?>',
            maxProductsReached: '<?php echo esc_js(__('Możesz porównać maksymalnie 3 produkty.', 'bayonet')); ?>',
            productRemoved: '<?php echo esc_js(__('Produkt został usunięty z porównania.', 'bayonet')); ?>',
            addProduct: '<?php echo esc_js(__('Dodaj produkt', 'bayonet')); ?>',
            noProducts: '<?php echo esc_js(__('Brak produktów do porównania', 'bayonet')); ?>'
        }
    };
</script>

<?php
get_footer();
?>