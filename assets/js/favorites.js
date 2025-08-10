console.log("Favorites script loaded");

(function () {
  "use strict";

  // Flaga zapobiegająca podwójnemu wywołaniu
  let isProcessing = false;

  // Poczekaj na pełne załadowanie strony
  function waitForReady(callback) {
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", callback);
    } else {
      callback();
    }
  }

  // Funkcja do pobierania ulubionych z cookies
  function getFavorites() {
    try {
      const favorites = getCookie("wc_favorites");
      if (favorites) {
        const parsed = JSON.parse(decodeURIComponent(favorites));
        return Array.isArray(parsed) ? parsed.map((id) => parseInt(id)) : [];
      }
    } catch (e) {
      console.log("Błąd parsowania cookies ulubionych:", e);
    }
    return [];
  }

  // Funkcja do zapisywania ulubionych w cookies
  function saveFavorites(favorites) {
    try {
      const validFavorites = favorites
        .filter((id) => id && !isNaN(id))
        .map((id) => parseInt(id));
      const cookieValue = encodeURIComponent(JSON.stringify(validFavorites));
      setCookie("wc_favorites", cookieValue, 30);
    } catch (e) {
      console.log("Błąd zapisywania cookies:", e);
    }
  }

  // Funkcja do ustawiania cookie
  function setCookie(name, value, days) {
    const expires = new Date();
    expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
    document.cookie =
      name +
      "=" +
      value +
      ";expires=" +
      expires.toUTCString() +
      ";path=/;SameSite=Lax";
  }

  // Funkcja do pobierania cookie
  function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(";");
    for (let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) === " ") c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  }

  // Funkcja sprawdzająca czy element jest głównym produktem
  function isMainProduct(card) {
    return (
      card.closest(".single-product-hero-section") ||
      card.closest(".product-summary") ||
      card.closest(".woocommerce-product-details") ||
      card.classList.contains("single-product-hero-section") ||
      card.classList.contains("product-summary") ||
      (!card.closest(".carousel") &&
        !card.closest(".slider") &&
        !card.closest(".swiper") &&
        !card.closest(".related") &&
        !card.closest(".upsells") &&
        !card.closest(".cross-sells") &&
        !card.closest('[class*="carousel"]') &&
        !card.closest('[class*="slider"]') &&
        !card.closest('[class*="related"]') &&
        card.querySelector(".fav-icon-single"))
    );
  }

  // Funkcja do aktualizacji ikony ulubionego
  function updateFavoriteIcon(productId, isFavorite) {
   // console.log(updateFavoriteIcon);
    const productCards = document.querySelectorAll("li.product, div.product");
    console.log(productCards);
    productCards.forEach((card) => {
      const cardProductId = getProductIdFromCard(card);
      if (cardProductId == productId) {
        const favIcon = card.querySelector(".fav-button img");
        if (favIcon) {
          const currentSrc = favIcon.src;
     
          if (isFavorite) {
            if (currentSrc.includes("fav.svg")) {
              favIcon.src = currentSrc.replace("fav.svg", "fav-filled.svg");
            }
            favIcon.alt = "Usuń z ulubionych";
            favIcon
              .closest(".fav-button")
              .setAttribute("aria-label", "Usuń z ulubionych");
          } else {
            if (currentSrc.includes("fav-filled.svg")) {
              favIcon.src = currentSrc.replace("fav-filled.svg", "fav.svg");
            }
            favIcon.alt = "Dodaj do ulubionych";
            favIcon
              .closest(".fav-button")
              .setAttribute("aria-label", "Dodaj do ulubionych");
          }
        }
      }
    });

    // Obsługa ikony na stronie pojedynczego produktu - tylko głównego produktu
    if (document.body.classList.contains("single-product")) {
      const singleProductIcon = document.querySelector(
        ".fav-icon-single .fav-button img"
      );
      if (singleProductIcon) {
        const mainProductId = getMainProductId();
        if (mainProductId == productId) {
          const currentSrc = singleProductIcon.src;
          if (isFavorite) {
            if (currentSrc.includes("fav.svg")) {
              singleProductIcon.src = currentSrc.replace(
                "fav.svg",
                "fav-filled.svg"
              );
            }
            singleProductIcon.alt = "Usuń z ulubionych";
            singleProductIcon
              .closest(".fav-button")
              .setAttribute("aria-label", "Usuń z ulubionych");
          } else {
            if (currentSrc.includes("fav-filled.svg")) {
              singleProductIcon.src = currentSrc.replace(
                "fav-filled.svg",
                "fav.svg"
              );
            }
            singleProductIcon.alt = "Dodaj do ulubionych";
            singleProductIcon
              .closest(".fav-button")
              .setAttribute("aria-label", "Dodaj do ulubionych");
          }
        }
      }
    }
  }

  // Funkcja do pobierania ID głównego produktu na stronie single-product
  function getMainProductId() {
    const addToCartForm = document.querySelector(
      "form.cart, .variations_form, form.variations_form"
    );
    if (addToCartForm) {
      const productId =
        addToCartForm.getAttribute("data-product_id") ||
        addToCartForm.querySelector('input[name="add-to-cart"]')?.value ||
        addToCartForm.querySelector('input[name="product_id"]')?.value;
      if (productId) {
        return parseInt(productId);
      }
    }

    const postId = document
      .querySelector('article[id*="post-"]')
      ?.id?.match(/post-(\d+)/)?.[1];
    if (postId) {
      return parseInt(postId);
    }

    const mainSection = document.querySelector(
      ".single-product-hero-section, .product-summary"
    );
    if (mainSection) {
      const productIdElement = mainSection.querySelector(
        "[data-product-id], [data-product_id]"
      );
      if (productIdElement) {
        const productId =
          productIdElement.getAttribute("data-product-id") ||
          productIdElement.getAttribute("data-product_id");
        if (productId) {
          return parseInt(productId);
        }
      }
    }

    const addToCartBtn = document.querySelector(
      ".single_add_to_cart_button, .ajax_add_to_cart"
    );
    if (addToCartBtn) {
      const productId =
        addToCartBtn.getAttribute("data-product_id") ||
        addToCartBtn.getAttribute("data-product-id");
      if (productId) {
        return parseInt(productId);
      }
    }

    return null;
  }

  // Funkcja do przełączania ulubionego
  function toggleFavorite(productId) {
    productId = parseInt(productId);
    if (!productId || isNaN(productId)) {
      return;
    }

    let favorites = getFavorites();
    const index = favorites.indexOf(productId);

    if (index > -1) {
      favorites.splice(index, 1);
      updateFavoriteIcon(productId, false);
    } else {
      favorites.push(productId);
      updateFavoriteIcon(productId, true);
    }

    saveFavorites(favorites);
    updateFavoritesCount();
  }

  // Funkcja do aktualizacji licznika ulubionych
  function updateFavoritesCount() {
    const favorites = getFavorites();
    const count = favorites.length;
    const counters = document.querySelectorAll(
      ".favorites-count, .wishlist-count"
    );

    counters.forEach((counter) => {
      counter.textContent = count;
      counter.style.display = count > 0 ? "inline-flex" : "none";
    });
  }

  // Funkcja do pobierania ID produktu z karty
  function getProductIdFromCard(card) {
    if (
      document.body.classList.contains("single-product") ||
      window.location.pathname.includes("/product/")
    ) {
      if (isMainProduct(card)) {
        return getMainProductId();
      } else {
        let productId =
          card.getAttribute("data-product-id") ||
          card.getAttribute("data-product_id");
        if (productId) {
          return parseInt(productId);
        }

        const productLink = card.querySelector(
          '.product-link, a[href*="/product/"]'
        );
        if (productLink) {
          productId =
            productLink.getAttribute("data-product-id") ||
            productLink.getAttribute("data-product_id");
          if (productId) {
            return parseInt(productId);
          }

          const href = productLink.getAttribute("href");
          if (href && href.includes("/product/")) {
            const urlParams = new URLSearchParams(href.split("?")[1] || "");
            const urlProductId =
              urlParams.get("product_id") || urlParams.get("add-to-cart");
            if (urlProductId && !isNaN(urlProductId)) {
              return parseInt(urlProductId);
            }
          }
        }

        const elementsWithProductId = card.querySelectorAll(
          "[data-product_id], [data-product-id]"
        );
        if (elementsWithProductId.length > 0) {
          for (let element of elementsWithProductId) {
            productId =
              element.getAttribute("data-product_id") ||
              element.getAttribute("data-product-id");
            if (productId) {
              return parseInt(productId);
            }
          }
        }

        const forms = card.querySelectorAll("form");
        for (let form of forms) {
          const hiddenProductId = form.querySelector(
            'input[name="product_id"], input[name="add-to-cart"]'
          );
          if (hiddenProductId && hiddenProductId.value) {
            return parseInt(hiddenProductId.value);
          }

          productId =
            form.getAttribute("data-product_id") ||
            form.getAttribute("data-product-id");
          if (productId) {
            return parseInt(productId);
          }
        }

        return null;
      }
    }

    // Dla pozostałych stron
    let productId = card.getAttribute("data-product-id");
    if (productId) {
      return parseInt(productId);
    }

    const productLink = card.querySelector(
      '.product-link, a[href*="/product/"]'
    );
    if (productLink) {
      productId = productLink.getAttribute("data-product-id");
      if (productId) {
        return parseInt(productId);
      }
    }

    const elementsWithProductId = card.querySelectorAll(
      "[data-product_id], [data-product-id]"
    );
    if (elementsWithProductId.length > 0) {
      for (let element of elementsWithProductId) {
        productId =
          element.getAttribute("data-product_id") ||
          element.getAttribute("data-product-id");
        if (productId) {
          return parseInt(productId);
        }
      }
    }

    const allLinks = card.querySelectorAll("a");
    for (let link of allLinks) {
      productId =
        link.getAttribute("data-product-id") ||
        link.getAttribute("data-product_id");
      if (productId) {
        return parseInt(productId);
      }

      const href = link.getAttribute("href");
      if (href && href.includes("/product/")) {
        const urlParams = new URLSearchParams(href.split("?")[1] || "");
        const urlProductId =
          urlParams.get("product_id") || urlParams.get("add-to-cart");
        if (urlProductId && !isNaN(urlProductId)) {
          return parseInt(urlProductId);
        }
      }
    }

    const forms = card.querySelectorAll("form");
    for (let form of forms) {
      const hiddenProductId = form.querySelector(
        'input[name="product_id"], input[name="add-to-cart"]'
      );
      if (hiddenProductId && hiddenProductId.value) {
        return parseInt(hiddenProductId.value);
      }

      productId =
        form.getAttribute("data-product_id") ||
        form.getAttribute("data-product-id");
      if (productId) {
        return parseInt(productId);
      }
    }

    const productElements = card.querySelectorAll(
      '.product, [class*="product"], [id*="product"]'
    );
    for (let element of productElements) {
      productId =
        element.getAttribute("data-product-id") ||
        element.getAttribute("data-product_id") ||
        element.getAttribute("data-id");
      if (productId && !isNaN(productId)) {
        return parseInt(productId);
      }
    }

    const dataElements = card.querySelectorAll(
      "[data-product-id], [data-product_id], [data-id]"
    );
    for (let element of dataElements) {
      productId =
        element.getAttribute("data-product-id") ||
        element.getAttribute("data-product_id") ||
        element.getAttribute("data-id");
      if (productId && !isNaN(productId)) {
        return parseInt(productId);
      }
    }

    return null;
  }

  // Inicjalizacja - ustaw ikony na podstawie zapisanych ulubionych
  function initializeFavorites() {
    const favorites = getFavorites();
    const productCards = document.querySelectorAll(
      '.product-card, .product, [class*="product-"]'
    );

    productCards.forEach((card) => {
      const productId = getProductIdFromCard(card);
      if (productId && favorites.includes(productId)) {
        updateFavoriteIcon(productId, true);
      }
    });

    if (document.body.classList.contains("single-product")) {
      const mainProductId = getMainProductId();
      if (mainProductId && favorites.includes(mainProductId)) {
        updateFavoriteIcon(mainProductId, true);
      }
    }

    updateFavoritesCount();
  }

  // UPROSZCZONY Event listener z obsługą buttonów
  function setupEventListeners() {
    document.addEventListener("click", function (e) {
      if (isProcessing) {
        return;
      }

      // Sprawdź czy kliknięto button ulubionych
      const favButton = e.target.closest(".fav-button");

      if (favButton) {
        e.preventDefault();
        e.stopPropagation();

        isProcessing = true;

        // Pobierz ID produktu z buttona lub jego kontenera
        let productId = favButton.getAttribute("data-product-id");

        if (!productId) {
          const favIcon = favButton.closest(".fav-icon, .fav-icon-single");
          if (favIcon) {
            productId = favIcon.getAttribute("data-product-id");
          }
        }

        if (!productId) {
          const productCard = favButton.closest(
            '.product-card, .product, [class*="product-"]'
          );
          if (productCard) {
            productId = getProductIdFromCard(productCard);
          }
        }

        if (productId) {
          // Sprawdź czy jesteśmy na stronie ulubionych
          const isOnFavoritesPage =
            document.querySelector(".favorites-page") ||
            window.location.pathname.includes("/ulubione/") ||
            document.querySelector(".favorite-product") ||
            document.body.classList.contains("page-template-favorites") ||
            document.title.toLowerCase().includes("ulubione");

          if (isOnFavoritesPage) {
            let favorites = getFavorites();
            const index = favorites.indexOf(parseInt(productId));

            if (index > -1) {
              favorites.splice(index, 1);
              saveFavorites(favorites);
              updateFavoritesCount();
              setTimeout(() => {
                location.reload();
              }, 100);
            }
          } else {
            toggleFavorite(productId);
          }
        } else {
          console.log("Nie znaleziono ID produktu");
        }

        setTimeout(() => {
          isProcessing = false;
        }, 200);
      }
    });

    // Observer dla dynamicznie ładowanych produktów
    const observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        if (mutation.type === "childList" && mutation.addedNodes.length > 0) {
          const newProducts = Array.from(mutation.addedNodes).filter(
            (node) =>
              node.nodeType === 1 &&
              (node.classList.contains("product-card") ||
                node.classList.contains("product") ||
                (node.querySelector &&
                  node.querySelector(".product-card, .product")))
          );

          if (newProducts.length > 0) {
            setTimeout(() => {
              initializeFavorites();
            }, 100);
          }
        }
      });
    });

    observer.observe(document.body, {
      childList: true,
      subtree: true,
    });
  }

  // Funkcja do usuwania produktu z ulubionych
  function removeFavorite(productId) {
    productId = parseInt(productId);
    let favorites = getFavorites();
    const index = favorites.indexOf(productId);

    if (index > -1) {
      favorites.splice(index, 1);
      saveFavorites(favorites);

      const productElement = document.querySelector(
        `.favorite-product[data-product-id="${productId}"]`
      );
      if (productElement) {
        productElement.style.transition =
          "opacity 0.3s ease, transform 0.3s ease";
        productElement.style.opacity = "0";
        productElement.style.transform = "scale(0.9)";

        setTimeout(() => {
          productElement.remove();

          const remainingProducts =
            document.querySelectorAll(".favorite-product");
          if (remainingProducts.length === 0) {
            location.reload();
          }
        }, 300);
      }

      updateFavoritesCount();
    }
  }

  // Funkcja do czyszczenia wszystkich ulubionych
  function clearAllFavorites() {
    saveFavorites([]);
    updateFavoritesCount();
    location.reload();
  }

  // Inicjalizacja po załadowaniu strony
  waitForReady(function () {
    setupEventListeners();
    setTimeout(initializeFavorites, 100);

    // Eksportuj funkcje dla globalnego użycia
    window.getFavorites = getFavorites;
    window.toggleFavorite = toggleFavorite;
    window.updateFavoritesCount = updateFavoritesCount;
    window.removeFavorite = removeFavorite;
    window.clearAllFavorites = clearAllFavorites;
    window.getProductIdFromCard = getProductIdFromCard;
  });
})();
