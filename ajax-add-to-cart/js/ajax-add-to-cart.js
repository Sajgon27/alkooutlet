/**
 * AJAX Add to Cart - Główny plik JavaScript
 * 
 * @package WC_AJAX_Add_To_Cart
 * @version 1.1.0
 */
(function($) {
    'use strict';

    // Pokazuje powiadomienie o dodaniu do koszyka
    function showNotification(productTitle) {
        
        var $notification = $('#wc-ajax-notification');
        
        // Ustaw treść
        $notification.find('.message').text(productTitle + ' ' + wcAjaxCart.i18n_added);
        
        // Pokaż powiadomienie
        $notification.css('display', 'flex').addClass('show');
        
        // Ukryj po 3 sekundach
        setTimeout(function() {
            $notification.removeClass('show');
            setTimeout(function() {
                $notification.css('display', 'none');
            }, 300);
        }, 3000);
    }

    // Usuń atrybuty href z przycisków produktów wariantowych po załadowaniu strony
    function removeHrefFromVariantButtons() {
        $('.product-type-variable.product-card__button').each(function() {
            $(this).removeAttr('href');
            // Dodaj cursor: pointer, aby przycisk nadal wyglądał jak kliknięty
            $(this).css('cursor', 'pointer');
        });
    }

    // Obsługa dodawania z listy produktów
    $(document).on('click', '.ajax_add_to_cart', function(e) {
        var $thisButton = $(this);
    
        function handleAddedToCart(event, fragments, cart_hash, $button) {
            if ($button[0] === $thisButton[0]) {
                var productTitle = $thisButton.closest('.product').find('.woocommerce-loop-product__title').text() || $thisButton.attr('data-product_name') || 'Produkt';
                showNotification(productTitle);
                $(document.body).off('added_to_cart', handleAddedToCart);
            }
        }
    
        $(document.body).on('added_to_cart', handleAddedToCart);
    });

    // Obsługa przycisku na stronie produktu
    $(document).on('click', '.single_add_to_cart_button:not(.disabled)', function(e) {
        var $thisButton = $(this);
        var $form = $thisButton.closest('form.cart');
        
        // Jeśli to nie jest formularz lub przycisk zewnętrzny, nic nie rób
        if (!$form.length || $thisButton.hasClass('external')) {
            return;
        }
        
        // Sprawdź czy to produkt wariantowy
        var isVariable = $form.hasClass('variations_form');

        if (isVariable) {
            // Sprawdź czy wszystkie atrybuty są wybrane
            var allSelected = true;
            $form.find('select[name^="attribute_"]').each(function() {
                if ($(this).val() === '' && !$(this).attr('data-attribute_allows_any')) {
                    allSelected = false;
                }
            });
            
            // Jeśli nie wszystkie atrybuty są wybrane, pozwól na standardową walidację WooCommerce
            if (!allSelected) {
                return;
            }
            
            // Sprawdź czy wariant jest wybrany
            var variationId = $form.find('input[name="variation_id"]').val();
            if (!variationId || variationId == 0) {
                // Tu jest problem - lepiej pokazać błąd niż kontynuować
                alert(wcAjaxCart.i18n_select_variation || 'Wybierz wariant produktu');
                return;
            }
        }
        
        // Zapobiegaj domyślnemu zachowaniu tylko dla walidowanych formularzy
        e.preventDefault();
        
        // Przygotuj dane formularza
        var formData = new FormData($form[0]);
        formData.append('add-to-cart', $form.find('[name="add-to-cart"]').val() || $thisButton.val());
        
        // Zapisz tytuł produktu do użycia w powiadomieniu
        var productTitle = $('.product_title').text() || 'Produkt';
        
        // Wyzwól zdarzenie dodawania do koszyka
        $(document.body).trigger('adding_to_cart', [$thisButton, {}]);
        
        // Oznacz przycisk jako ładujący
        $thisButton.addClass('loading');
        
        // Wyślij żądanie AJAX
        $.ajax({
            type: 'POST',
            url: $form.attr('action') || window.location.href,
            data: formData,
            processData: false,
            contentType: false,
            complete: function() {
                $thisButton.removeClass('loading');
            },
            success: function(response) {
                // Sprawdź czy odpowiedź jest poprawna
                if (response.error) {
                    // W przypadku błędu, pokaż komunikat
                    if (response.product_url) {
                        window.location = response.product_url;
                        return;
                    }
                    
                    return;
                }
                // Pobierz automatycznie fragmenty z WooCommerce
                $.ajax({
                    url: wcAjaxCart.wc_ajax_url.replace('%%endpoint%%', 'get_refreshed_fragments'),
                    type: 'POST',
                    success: function(fragmentResponse) {
                        if (fragmentResponse && fragmentResponse.fragments) {
                            $.each(fragmentResponse.fragments, function(key, value) {
                                $(key).html(value);
                            });
                        }

                        // Pokaż powiadomienie o dodaniu
                        showNotification(productTitle);

                        // Śledzenie dodania do koszyka
                        var productId = $form.find('[name="add-to-cart"]').val() || $thisButton.val();
                        var quantity = $form.find('[name="quantity"]').val() || 1;
                        var variationId = $form.find('[name="variation_id"]').val() || 0;
                        var price = $form.find('[data-product_price]').val() || $thisButton.data('price') || '';
                        var category = $thisButton.data('category') || '';

                        trackAddToCart(productId, productTitle, quantity, price, category, variationId);

                        // Wyzwól zdarzenie dodania do koszyka
                        $(document.body).trigger('added_to_cart', [fragmentResponse.fragments, '', $thisButton]);
                    }
                });
            },
            error: function() {
                // W przypadku błędu, odświeżamy stronę
                window.location.reload();
            }
        });
    });

    // Obsługa kliknięcia dla produktów wariantowych - zapobiegaj domyślnemu zachowaniu
    $(document).on('click', '.product-type-variable.product-card__button', function(e) {
        e.preventDefault(); // Zatrzymaj domyślne zachowanie (nawet jeśli href istnieje)
    });

    // Oznaczanie selektorów wariantów, które pozwalają na opcję "any"
    $(document).on('found_variation', function(event, variation) {
        var $form = $(event.target);
        
        // Znajdź selektory wariantów
        $form.find('select[name^="attribute_"]').each(function() {
            var $select = $(this);
            var attrName = $select.attr('name');
            
            // Sprawdź czy ten atrybut pozwala na opcję "any"
            if ($select.find('option[value=""]').length > 0) {
                $select.attr('data-attribute_allows_any', 'yes');
            }
        });
    });
    
    // Obsługa resetowania formularza
    $(document).on('reset_data', '.variations_form', function() {
        var $form = $(this);
        
        // Przywróć atrybuty do stanu początkowego
        $form.find('select[name^="attribute_"]').each(function() {
            $(this).removeAttr('data-attribute_allows_any');
        });
    });

    // Funkcja śledzenia dodania do koszyka
    function trackAddToCart(productId, productTitle, quantity, price, category, variationId) {
        // Google Tag Manager
        if (typeof window.dataLayer !== 'undefined') {
            window.dataLayer.push({
                'event': 'add_to_cart',
                'ecommerce': {
                    'currencyCode': wcAjaxCart.currency,
                    'add': {
                        'products': [{
                            'id': productId,
                            'name': productTitle,
                            'price': price || '',
                            'category': category || '',
                            'variant': variationId || '',
                            'quantity': quantity || 1
                        }]
                    }
                }
            });
        }
        
        // Facebook Pixel
        if (typeof fbq !== 'undefined') {
            fbq('track', 'AddToCart', {
                'content_ids': [productId],
                'content_type': 'product',
                'value': price || 0,
                'currency': wcAjaxCart.currency
            });
        }
        
        // Zdarzenie do niestandardowych integracji
        $(document.body).trigger('wc_ajax_cart_tracked', [{
            product_id: productId,
            product_title: productTitle,
            quantity: quantity,
            price: price,
            category: category,
            variation_id: variationId,
            currency: wcAjaxCart.currency,
            currency_symbol: wcAjaxCart.currency_symbol
        }]);
    }

    // Inicjalizacja po załadowaniu dokumentu
    $(document).ready(function() {
        // Usuń atrybuty href z przycisków produktów wariantowych
        removeHrefFromVariantButtons();
        
        // Usuń atrybuty href po ładowaniu AJAX (np. podczas filtrowania produktów)
        $(document).ajaxComplete(function() {
            removeHrefFromVariantButtons();
        });
    });

})(jQuery);