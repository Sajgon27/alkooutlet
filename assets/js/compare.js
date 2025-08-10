(function ($) {
  "use strict";

  class ProductCompare {
    constructor() {
      this.compareProducts = window.compareData
        ? window.compareData.currentProducts
        : [];
      this.maxProducts = window.compareData
        ? window.compareData.maxProducts
        : 3;
      this.ajaxUrl = window.compareData ? window.compareData.ajaxUrl : "";
      this.nonce = window.compareData ? window.compareData.nonce : "";
      this.searchTimeout = null;

      this.init();
    }

    init() {
      this.bindEvents();
      this.updateCompareButtons();
    }

    bindEvents() {
      const self = this;

      // Usuń pojedynczy produkt - poprawiony event handler
      $(document).on("click", ".remove-product-btn", function (e) {
        e.preventDefault();
        e.stopPropagation();

        console.log("Remove button clicked"); // Debug

        let $button = $(this);
        let productId = $button.data("product-id");

        // Fallback - sprawdź czy data-product-id jest w atrybucie
        if (!productId) {
          productId = $button.attr("data-product-id");
        }

        console.log("Product ID to remove:", productId); // Debug

        if (productId) {
          self.removeProduct(productId);
        } else {
          console.error("No product ID found for removal");
        }
      });

      // Usuń wszystkie produkty
      $(document).on("click", ".remove-all-btn", function (e) {
        e.preventDefault();
        e.stopPropagation();
        self.removeAllProducts();
      });

      // Dodaj do porównania z karty produktu
      $(document).on("click", ".add-to-compare", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const productId = $(this).data("product-id");

        if (productId) {
          self.addProduct(productId);
        }
      });

      // Usuń z porównania z karty produktu
      $(document).on("click", ".remove-from-compare", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const productId = $(this).data("product-id");
        if (productId) {
          self.removeProduct(productId);
        }
      });

      // Dodatkowy handler dla obrazków w przyciskach (zapobiega problemom z event bubbling)
      $(document).on("click", ".remove-product-btn img", function (e) {
        e.preventDefault();
        e.stopPropagation();

        // Przekieruj kliknięcie do rodzica
        $(this).closest(".remove-product-btn").trigger("click");
      });
    }

    addProduct(productId) {
      console.log("Adding product:", productId); // Debug

      if (this.compareProducts.includes(productId.toString())) {
        this.showNotification("Produkt już znajduje się w porównaniu", "info");
        console.log("Product already in compare list:", productId); // Debug
        return;
      }

      if (this.compareProducts.length >= this.maxProducts) {
        console.warn("Max products limit reached:", this.maxProducts); // Debug
        this.showNotification(
          `Można porównać maksymalnie ${this.maxProducts} produkty`,
          "warning"
        );
        return;
      }

      this.compareProducts.push(productId.toString());
      this.updateSession();
      this.updateCompareButtons();
      this.showNotification("Produkt dodany do porównania", "success");

      // Odśwież stronę jeśli jesteśmy na stronie porównania
      if (
        window.location.pathname.includes("porownywarka") ||
        window.location.pathname.includes("comparator")
      ) {
        setTimeout(() => {
          window.location.reload();
        }, 500); // Krótkie opóźnienie dla UX
      }
    }

    removeProduct(productId) {
      console.log("Removing product:", productId); // Debug
      console.log("Current products:", this.compareProducts); // Debug

      const index = this.compareProducts.indexOf(productId.toString());
      console.log("Product index:", index); // Debug

      if (index > -1) {
        this.compareProducts.splice(index, 1);
        this.updateSession();
        this.updateCompareButtons();
        this.showNotification("Produkt usunięty z porównania", "info");

        // Odśwież stronę jeśli jesteśmy na stronie porównania
        if (
          window.location.pathname.includes("porownywarka") ||
          window.location.pathname.includes("comparator")
        ) {
          setTimeout(() => {
            window.location.reload();
          }, 500); // Krótkie opóźnienie dla UX
        }
      } else {
        console.error("Product not found in compare list");
      }
    }

    removeAllProducts() {
      if (this.compareProducts.length === 0) return;

      if (
        confirm("Czy na pewno chcesz usunąć wszystkie produkty z porównania?")
      ) {
        this.compareProducts = [];
        this.updateSession();
        this.updateCompareButtons();
        this.showNotification(
          "Wszystkie produkty usunięte z porównania",
          "info"
        );

        // Odśwież stronę jeśli jesteśmy na stronie porównania
        if (
          window.location.pathname.includes("porownywarka") ||
          window.location.pathname.includes("comparator")
        ) {
          setTimeout(() => {
            window.location.reload();
          }, 500);
        }
      }
    }

    updateSession() {
      console.log("Updating session with products:", this.compareProducts); // Debug

      $.ajax({
        url: this.ajaxUrl,
        type: "POST",
        data: {
          action: "update_compare_products",
          products: this.compareProducts,
          nonce: this.nonce,
        },
        success: (response) => {
          console.log("Session update response:", response); // Debug
          if (!response.success) {
            console.error("Błąd aktualizacji sesji:", response.data);
            this.showNotification(
              "Błąd aktualizacji. Spróbuj ponownie.",
              "error"
            );
          }
        },
        error: (xhr, status, error) => {
          console.error("Błąd AJAX:", error);
          this.showNotification("Błąd połączenia. Spróbuj ponownie.", "error");
        },
      });
    }

    updateCompareButtons() {
      // Aktualizuj przyciski na kartach produktów
      console.log("test");

      /*
      ZMIANY SZYMON
      $(".product-card").each((index, element) => {
        const $card = $(element);
        const productId = $card.data("product-id");
        const isInCompare = this.compareProducts.includes(productId.toString());

        const $compareBtn = $card.find(".compare-button");
        if ($compareBtn.length === 0) {
          // Dodaj przycisk porównania jeśli nie istnieje
          const compareButton = this.createCompareButton(
            productId,
            isInCompare
          );
          $card.find(".product-info-bottom").append(compareButton);
        } else {
        
          this.updateCompareButton($compareBtn, productId, isInCompare);
        }
      });

      // Aktualizuj licznik w headerze
      this.updateCompareCounter();
      */

      const fullId = $("main div.product").attr("id"); // e.g., "product-56"
      if(fullId) {
      const productId = fullId.replace("product-", "");
      const isInCompare = this.compareProducts.includes(productId.toString());
      const $compareBtn = $(".compare-button");
      this.updateCompareButton($compareBtn, productId, isInCompare);
      }
    }

    createCompareButton(productId, isInCompare) {
      const action = isInCompare ? "remove-from-compare" : "add-to-compare";
      const text = isInCompare ? "Usuń z porównania" : "Dodaj do porównania";
      const icon = isInCompare ? "minus" : "plus";

      return `
        <button class="compare-button ${action}" data-product-id="${productId}" title="${text}">
          <img src="${window.location.origin}/wp-content/themes/your-theme/images/${icon}.svg" alt="${text}">
        </button>
      `;
    }

    updateCompareButton($button, productId, isInCompare) {
      const action = isInCompare ? "remove-from-compare" : "add-to-compare";
      const text = isInCompare ? "Usuń z porównania" : "Dodaj do porównania";
      const icon = isInCompare ? "minus" : "plus";

      $button
        .removeClass("add-to-compare remove-from-compare")
        .addClass(action)
        .attr("title", text)
        .find("span")
        .text(text); // ✅ This updates the <span> text
    }

    updateCompareCounter() {
      const $compareCount = $(".compare-count");
      const $compareLink = $(".compare-link");
      const $compareDiv = $(".compare");

      if (this.compareProducts.length > 0) {
        // Pokaż licznik i aktualizuj wartość
        $compareCount.text(this.compareProducts.length).show();
        $compareDiv.addClass("has-products");
        $compareLink.addClass("active");
      } else {
        // Ukryj licznik gdy brak produktów
        $compareCount.hide();
        $compareDiv.removeClass("has-products");
        $compareLink.removeClass("active");
      }
    }

    goToCompare() {
      if (this.compareProducts.length === 0) {
        this.showNotification("Brak produktów do porównania", "warning");
        return;
      }

      const compareUrl = `${
        window.location.origin
      }/porownywarka/?products=${this.compareProducts.join(",")}`;
      window.location.href = compareUrl;
    }

    showNotification(message, type = "info") {
      // Usuń poprzednie powiadomienia
      $(".compare-notification").remove();

      const $notification = $(`
        <div class="compare-notification ${type}">
          ${message}
          <button class="close-notification">×</button>
        </div>
      `);

      $("body").append($notification);

      // Auto hide po 3 sekundach
      setTimeout(() => {
        $notification.fadeOut(() => $notification.remove());
      }, 3000);

      // Manual close
      $notification.on("click", ".close-notification", () => {
        $notification.fadeOut(() => $notification.remove());
      });
    }
  }

  // Inicjalizuj gdy DOM jest gotowy
  $(document).ready(() => {
    console.log("Initializing ProductCompare"); // Debug
    window.productCompare = new ProductCompare();
  });

  // Dodatkowa inicjalizacja dla przypadków gdy elementy są dodawane dynamicznie
  $(window).on("load", () => {
    if (window.productCompare) {
      window.productCompare.updateCompareButtons();
    }
  });
})(jQuery);
