/**
 * AJAX Add to Cart - Modal JavaScript
 * 
 * @package WC_AJAX_Add_To_Cart
 * @version 1.1.0
 */
(function($) {
    'use strict';

    // Dane produktu dla aktualnego modala
    var modalProductData = {
        product_id: 0,
        variations: {},
        variation_id: 0,
        attributes: {},
        product_url: '',
        quantity: 1,
        images: {},
        price_html: ''
    };
    
    // Inicjalizacja funkcjonalności modala
    function initModal() {
        // Stwórz HTML modala jeśli nie istnieje
        if ($('#wc-ajax-modal').length === 0) {
            createModalHTML();
        }
        
        // Powiąż zdarzenia
        bindEvents();
    }
    
    // Stworzenie struktury HTML modala
    function createModalHTML() {
        var modalHTML = `
            <div id="wc-ajax-modal" class="wc-ajax-modal">
                <div class="wc-ajax-modal-content">
                    <div class="wc-ajax-modal-header">
                        <h3 class="wc-ajax-modal-title"></h3>
                        <button type="button" class="wc-ajax-modal-close">&times;</button>
                    </div>
                    <div class="wc-ajax-modal-body">
                        <div class="wc-ajax-modal-product-image">
                            <img src="" alt="" class="wc-ajax-modal-image">
                            <div class="wc-ajax-modal-variant-gallery"></div>
                        </div>
                        <div class="wc-ajax-modal-product-info">
                            <div class="wc-ajax-modal-price"></div>
                            <div class="wc-ajax-modal-attributes"></div>
                            <div class="wc-ajax-modal-quantity">
                                <label for="wc-ajax-modal-quantity-input">Ilość:</label>
                                <div class="wc-ajax-modal-quantity-controls">
                                    <button type="button" class="wc-ajax-modal-quantity-minus">-</button>
                                    <input type="number" id="wc-ajax-modal-quantity-input" class="wc-ajax-modal-quantity-input" value="1" min="1" step="1">
                                    <button type="button" class="wc-ajax-modal-quantity-plus">+</button>
                                </div>
                            </div>
                            <div class="wc-ajax-modal-error"></div>
                            <div class="wc-ajax-modal-actions">
                                <button type="button" class="wc-ajax-modal-add-to-cart">Dodaj do koszyka</button>
                                <a href="#" class="wc-ajax-modal-view-product">Zobacz produkt</a>
                            </div>
                        </div>
                    </div>
                    <div class="wc-ajax-modal-spinner"></div>
                </div>
            </div>
        `;
        
        $('body').append(modalHTML);
    }
    
    // Powiązanie zdarzeń z elementami modala
    function bindEvents() {
        // Obsługa kliknięcia dla produktów wariantowych
        $(document).on('click', '.product-type-variable .product-card__button', function(e) {
            e.preventDefault();
            var $button = $(this);
            var productId = $button.data('product_id');
            
            if (productId) {
                openProductModal(productId);
            } else {
                // Próba wyodrębnienia ID produktu z URL
                var url = $button.attr('href');
                productId = extractProductIdFromUrl(url);
                
                if (productId) {
                    openProductModal(productId);
                } else {
                    // Fallback: przekieruj na stronę produktu
                    var productUrl = $button.closest('.product').find('a.woocommerce-loop-product__link').attr('href');
                    if (productUrl) {
                        window.location.href = productUrl;
                    }
                }
            }
        });
        
        // Zamknięcie modala po kliknięciu przycisku zamknięcia
        $(document).on('click', '.wc-ajax-modal-close', function() {
            closeModal();
        });
        
        // Zamknięcie modala po kliknięciu poza modalem
        $(document).on('click', '.wc-ajax-modal', function(e) {
            if ($(e.target).hasClass('wc-ajax-modal')) {
                closeModal();
            }
        });
        
        // Przycisk zwiększenia ilości
        $(document).on('click', '.wc-ajax-modal-quantity-plus', function() {
            var $input = $('.wc-ajax-modal-quantity-input');
            var currentVal = parseInt($input.val());
            $input.val(currentVal + 1).trigger('change');
        });
        
        // Przycisk zmniejszenia ilości
        $(document).on('click', '.wc-ajax-modal-quantity-minus', function() {
            var $input = $('.wc-ajax-modal-quantity-input');
            var currentVal = parseInt($input.val());
            if (currentVal > 1) {
                $input.val(currentVal - 1).trigger('change');
            }
        });
        
        // Zmiana inputa ilości
        $(document).on('change', '.wc-ajax-modal-quantity-input', function() {
            modalProductData.quantity = parseInt($(this).val());
        });
        
        // Obsługa kliknięcia przycisku opcji wariantu
        $(document).on('click', '.wc-ajax-modal-attribute-button', function() {
            var $button = $(this);
            
            if ($button.hasClass('disabled')) {
                return;
            }
            
            var attribute = $button.data('attribute');
            var value = $button.data('value');
            
            // Usuń aktywną klasę z innych przycisków w tej samej grupie
            $('.wc-ajax-modal-attribute-button[data-attribute="' + attribute + '"]').removeClass('active');
            
            // Dodaj aktywną klasę do klikniętego przycisku
            $button.addClass('active');
            
            // Aktualizacja atrybutu w przechowywanych danych
            modalProductData.attributes[attribute] = value;
            
            // Znajdź pasujący wariant
            findMatchingVariation();
            
            // Aktualizuj cenę i obrazek na podstawie wariantu
            updateVariationDetails();
            
            // Aktualizuj dostępność przycisków wariantów
            updateAttributeButtonsAvailability();
        });
        
        // Kliknięcie obrazka w galerii wariantów
        $(document).on('click', '.wc-ajax-modal-variant-image', function() {
            var $img = $(this);
            var fullSrc = $img.data('full-src');
            
            $('.wc-ajax-modal-variant-image').removeClass('active');
            $img.addClass('active');
            
            // Aktualizacja głównego obrazka
            $('.wc-ajax-modal-image').attr('src', fullSrc);
        });
        
        // Kliknięcie przycisku dodania do koszyka
        $(document).on('click', '.wc-ajax-modal-add-to-cart', function() {
            var $button = $(this);
            
            if ($button.hasClass('loading') || $button.hasClass('disabled')) {
                return;
            }
            
            // Sprawdź czy wariant jest wybrany dla produktu wariantowego
            if (hasAttributes() && !isVariationSelected()) {
                showError('Wybierz wszystkie opcje produktu');
                highlightInvalidAttributes();
                return;
            }
            
            // Sprawdź czy wybrany wariant jest dostępny
            if (modalProductData.variation_id > 0) {
                var variation = modalProductData.variations[modalProductData.variation_id];
                if (variation && !variation.is_in_stock) {
                    showError('Wybrany wariant jest niedostępny');
                    return;
                }
            }
            
            // Dodaj do koszyka przez AJAX
            addToCartAjax($button);
        });
        
        // Obsługa przycisku "Zobacz produkt"
        $(document).on('click', '.wc-ajax-modal-view-product', function(e) {
            e.preventDefault();
            var productUrl = modalProductData.product_url;
            if (productUrl) {
                window.location.href = productUrl;
            }
        });
    }
    
    // Wyodrębnianie ID produktu z URL
    function extractProductIdFromUrl(url) {
        if (!url) return 0;
        
        // Standardowy pattern URL WooCommerce: /product/product-name/?add-to-cart=123
        var matches = url.match(/add-to-cart=(\d+)/);
        if (matches && matches[1]) {
            return parseInt(matches[1]);
        }
        
        // Próba wyodrębnienia z URL produktu: /product/product-name/
        matches = url.match(/\/product\/[^\/]+\/(\d+)\/?/);
        if (matches && matches[1]) {
            return parseInt(matches[1]);
        }
        
        return 0;
    }
    
    // Otwarcie modala produktu
    function openProductModal(productId) {
        // Resetowanie danych modala
        resetModalData();
        
        // Zapisanie ID produktu
        modalProductData.product_id = productId;
        
        // Pokaż stan ładowania
        var $modal = $('#wc-ajax-modal');
        $modal.addClass('loading show');
        
        // Pobierz dane produktu przez AJAX
        $.ajax({
            url: wcAjaxCart.ajax_url,
            type: 'POST',
            data: {
                action: 'wc_ajax_get_product_data',
                product_id: productId,
                nonce: wcAjaxCart.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Zapisz dane produktu
                    modalProductData.variations = response.data.variations || {};
                    modalProductData.images = response.data.images || {};
                    modalProductData.price_html = response.data.price_html || '';
                    modalProductData.product_url = response.data.product_url || '';
                    
                    // Wypełnij modal danymi produktu
                    populateModal(response.data);
                    
                    // Ustaw link do produktu
                    $('.wc-ajax-modal-view-product').attr('href', response.data.product_url);
                    
                    // Usuń stan ładowania
                    $modal.removeClass('loading');
                } else {
                    // Obsługa błędu
                    closeModal();
                    console.error('Nie udało się załadować danych produktu');
                    
                    // Przekieruj na stronę produktu jako fallback
                    if (response.data && response.data.product_url) {
                        window.location.href = response.data.product_url;
                    }
                }
            },
            error: function() {
                // Obsługa błędu AJAX
                closeModal();
                console.error('Błąd AJAX podczas ładowania danych produktu');
                
                // Przekieruj na stronę produktu jako fallback
                var productUrl = $('.product-type-variable[data-product_id="' + productId + '"]').closest('.product').find('a.woocommerce-loop-product__link').attr('href');
                if (productUrl) {
                    window.location.href = productUrl;
                }
            }
        });
    }
    
    // Resetowanie danych modala
    function resetModalData() {
        modalProductData = {
            product_id: 0,
            variations: {},
            variation_id: 0,
            attributes: {},
            product_url: '',
            quantity: 1,
            images: {},
            price_html: ''
        };
        
        // Wyczyść zawartość modala
        $('.wc-ajax-modal-title').html('');
        $('.wc-ajax-modal-image').attr('src', '');
        $('.wc-ajax-modal-price').html('');
        $('.wc-ajax-modal-attributes').empty();
        $('.wc-ajax-modal-variant-gallery').empty();
        $('.wc-ajax-modal-quantity-input').val(1);
        $('.wc-ajax-modal-error').removeClass('show').text('');
    }
    
    // Wypełnienie modala danymi produktu
    function populateModal(data) {
        // Ustaw tytuł
        $('.wc-ajax-modal-title').html(data.title || '');
        
        // Ustaw obrazek
        if (data.image) {
            $('.wc-ajax-modal-image').attr('src', data.image);
        }
        
        // Ustaw cenę
        $('.wc-ajax-modal-price').html(data.price_html);
        
        // Ustaw atrybuty jako przyciski
        if (data.attributes && Object.keys(data.attributes).length > 0) {
            var attributesHTML = '';
            
            $.each(data.attributes, function(name, options) {
                var label = options.label || name;
                attributesHTML += '<div class="wc-ajax-modal-attribute">';
                attributesHTML += '<div class="wc-ajax-modal-attribute-label">' + label + '</div>';
                attributesHTML += '<div class="wc-ajax-modal-attribute-buttons">';
                
                if (options.options) {
                    $.each(options.options, function(index, option) {
                        attributesHTML += '<button type="button" class="wc-ajax-modal-attribute-button" data-attribute="' + name + '" data-value="' + option.value + '">' + option.label + '</button>';
                    });
                }
                
                attributesHTML += '</div>';
                attributesHTML += '</div>';
            });
            
            $('.wc-ajax-modal-attributes').html(attributesHTML);
        }
        
        // Ustaw galerię wariantów
        if (data.gallery && data.gallery.length > 0) {
            var galleryHTML = '';
            
            $.each(data.gallery, function(index, image) {
                var activeClass = index === 0 ? 'active' : '';
                galleryHTML += '<img src="' + image.thumbnail + '" data-full-src="' + image.full + '" class="wc-ajax-modal-variant-image ' + activeClass + '" alt="' + data.title + ' - wariant">';
            });
            
            $('.wc-ajax-modal-variant-gallery').html(galleryHTML);
        }
        
        // Sprawdź czy są dostępne warianty
        checkVariationsAvailability();
    }
    
    // Sprawdzenie dostępności wariantów
    function checkVariationsAvailability() {
        var hasAvailableVariations = false;
        
        // Sprawdź czy jakikolwiek wariant jest dostępny
        $.each(modalProductData.variations, function(variationId, variation) {
            if (variation.is_in_stock) {
                hasAvailableVariations = true;
                return false; // przerwij pętlę
            }
        });
        
        // Jeśli nie ma dostępnych wariantów, pokaż informację i wyłącz przycisk dodania do koszyka
        if (!hasAvailableVariations) {
            showError('Brak dostępnych wariantów');
            $('.wc-ajax-modal-add-to-cart').addClass('disabled');
        } else {
            $('.wc-ajax-modal-add-to-cart').removeClass('disabled');
        }
        
        // Aktualizuj dostępność przycisków wariantów
        updateAttributeButtonsAvailability();
    }
    
    // Aktualizacja dostępności przycisków wariantów
    function updateAttributeButtonsAvailability() {
        // Najpierw zbierz wszystkie atrybuty i ich wartości
        var attributes = {};
        $('.wc-ajax-modal-attribute-button').each(function() {
            var attribute = $(this).data('attribute');
            var value = $(this).data('value');
            
            if (!attributes[attribute]) {
                attributes[attribute] = [];
            }
            
            if (attributes[attribute].indexOf(value) === -1) {
                attributes[attribute].push(value);
            }
        });
        
        // Pobierz aktualnie wybrane atrybuty
        var selectedAttributes = modalProductData.attributes;
        
        // Dla każdego przycisku atrybutu sprawdź, czy jest dostępny z aktualnymi wyborami
        $('.wc-ajax-modal-attribute-button').each(function() {
            var $button = $(this);
            var attribute = $button.data('attribute');
            var value = $button.data('value');
            var isAvailable = false;
            
            // Tymczasowa kopia aktualnych wyborów
            var tempAttributes = $.extend({}, selectedAttributes);
            tempAttributes[attribute] = value;
            
            // Sprawdź, czy istnieje wariant z tymi atrybutami i czy jest dostępny
            $.each(modalProductData.variations, function(variationId, variation) {
                var matches = true;
                
                // Sprawdź czy wszystkie wybrane atrybuty pasują do tego wariantu
                $.each(tempAttributes, function(attr, val) {
                    if (variation.attributes[attr] !== '' && variation.attributes[attr] !== val) {
                        matches = false;
                        return false; // przerwij pętlę
                    }
                });
                
                // Jeśli pasuje i jest dostępny, oznacz jako dostępny
                if (matches && variation.is_in_stock) {
                    isAvailable = true;
                    return false; // przerwij pętlę
                }
            });
            
            // Aktualizuj wygląd przycisku
            if (isAvailable) {
                $button.removeClass('disabled out-of-stock');
            } else {
                $button.addClass('disabled out-of-stock');
            }
        });
    }
    
    // Zamknięcie modala
    function closeModal() {
        $('#wc-ajax-modal').removeClass('show');
        setTimeout(function() {
            resetModalData();
        }, 300);
    }
    
    // Sprawdzenie czy produkt ma atrybuty
    function hasAttributes() {
        return $('.wc-ajax-modal-attribute').length > 0;
    }
    
    // Sprawdzenie czy wariant jest wybrany
    function isVariationSelected() {
        var allSelected = true;
        
        $('.wc-ajax-modal-attribute').each(function() {
            var attributeName = $(this).find('.wc-ajax-modal-attribute-button').first().data('attribute');
            if (!modalProductData.attributes[attributeName]) {
                allSelected = false;
                return false; // Przerwij pętlę
            }
        });
        
        return allSelected && modalProductData.variation_id > 0;
    }
    
    // Znajdź pasujący wariant na podstawie wybranych atrybutów
    function findMatchingVariation() {
        modalProductData.variation_id = 0;
        
        if (!modalProductData.variations || Object.keys(modalProductData.variations).length === 0) {
            return;
        }
        
        // Pobierz wszystkie wartości atrybutów
        var allAttributesSelected = true;
        $('.wc-ajax-modal-attribute').each(function() {
            var attributeName = $(this).find('.wc-ajax-modal-attribute-button').first().data('attribute');
            if (!modalProductData.attributes[attributeName]) {
                allAttributesSelected = false;
                return false; // Przerwij pętlę
            }
        });
        
        if (!allAttributesSelected) {
            return;
        }
        
        // Szukaj pasującego wariantu
        $.each(modalProductData.variations, function(variationId, variation) {
            var isMatch = true;
            
            // Sprawdź czy wszystkie wybrane atrybuty pasują do tego wariantu
            $.each(modalProductData.attributes, function(attribute, value) {
                if (variation.attributes[attribute] !== '' && variation.attributes[attribute] !== value) {
                    isMatch = false;
                    return false; // Przerwij pętlę
                }
            });
            
            if (isMatch) {
                modalProductData.variation_id = parseInt(variationId);
                return false; // Przerwij pętlę
            }
        });
    }
    
    // Aktualizacja szczegółów wariantu (cena, obrazek) na podstawie wybranego wariantu
    function updateVariationDetails() {
        // Resetuj style walidacji
        $('.wc-ajax-modal-attribute-button').removeClass('invalid');
        $('.wc-ajax-modal-error').removeClass('show');
        
        if (modalProductData.variation_id > 0) {
            var variation = modalProductData.variations[modalProductData.variation_id];
            
            // Aktualizuj cenę
            if (variation.price_html) {
                $('.wc-ajax-modal-price').html(variation.price_html);
            }
            
            // Aktualizuj obrazek
            if (variation.image) {
                $('.wc-ajax-modal-image').attr('src', variation.image);
                
                // Aktualizuj aktywny obrazek w galerii
                $('.wc-ajax-modal-variant-image').removeClass('active');
                $('.wc-ajax-modal-variant-image[data-full-src="' + variation.image + '"]').addClass('active');
            }
            
            // Aktualizuj dostępność
            if (!variation.is_in_stock) {
                showError('Ten wariant jest niedostępny');
                $('.wc-ajax-modal-add-to-cart').addClass('disabled');
            } else {
                $('.wc-ajax-modal-error').removeClass('show');
                $('.wc-ajax-modal-add-to-cart').removeClass('disabled');
            }
        } else {
            // Resetuj do domyślnej ceny
            $('.wc-ajax-modal-price').html(modalProductData.price_html);
            $('.wc-ajax-modal-add-to-cart').removeClass('disabled');
        }
    }
    
    // Pokaż komunikat błędu
    function showError(message) {
        var $error = $('.wc-ajax-modal-error');
        $error.text(message).addClass('show');
    }
    
    // Podświetl nieprawidłowe atrybuty
    function highlightInvalidAttributes() {
        $('.wc-ajax-modal-attribute').each(function() {
            var attributeName = $(this).find('.wc-ajax-modal-attribute-button').first().data('attribute');
            if (!modalProductData.attributes[attributeName]) {
                $(this).find('.wc-ajax-modal-attribute-button').addClass('invalid');
            }
        });
    }
    
    // Dodaj do koszyka przez AJAX
    function addToCartAjax($button) {
        $button.addClass('loading');
        
        var data = {
            action: 'wc_ajax_add_to_cart',
            product_id: modalProductData.product_id,
            quantity: modalProductData.quantity,
            nonce: wcAjaxCart.nonce
        };
        
        // Dodaj ID wariantu i atrybuty jeśli to produkt wariantowy
        if (hasAttributes()) {
            if (modalProductData.variation_id > 0) {
                data.variation_id = modalProductData.variation_id;
                data.variation = modalProductData.attributes;
            } else {
                $button.removeClass('loading');
                showError('Wybierz wszystkie opcje produktu');
                highlightInvalidAttributes();
                return;
            }
        }
        
        // Wyślij żądanie AJAX
        $.ajax({
            url: wcAjaxCart.ajax_url,
            type: 'POST',
            data: data,
            success: function(response) {
                $button.removeClass('loading');
                
                if (response.success) {
                    // Zamknij modal
                    closeModal();
                    
                    // Pobierz tytuł produktu
                    var productTitle = $('.wc-ajax-modal-title').html();
                    
                    // Aktualizuj fragmenty
                    if (response.fragments) {
                        $.each(response.fragments, function(key, value) {
                            $(key).replaceWith(value);
                        });
                    }
                    
                    // Pokaż powiadomienie
                    if (typeof showNotification === 'function') {
                        showNotification(productTitle);
                    }
                    
                    // Wyzwól zdarzenie added_to_cart
                    $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
                } else {
                    // Pokaż błąd
                    if (response.data && response.data.message) {
                        showError(response.data.message);
                    } else {
                        showError('Wystąpił błąd podczas dodawania do koszyka');
                    }
                    
                    // Jeśli potrzebne przekierowanie
                    if (response.data && response.data.redirect) {
                        window.location.href = response.data.redirect;
                    }
                }
            },
            error: function() {
                $button.removeClass('loading');
                showError('Wystąpił błąd podczas dodawania do koszyka');
            }
        });
    }
    
    // Inicjalizacja po załadowaniu dokumentu
    $(document).ready(function() {
        initModal();
    });

})(jQuery);