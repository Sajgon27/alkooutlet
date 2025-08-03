//////////////////////////////////////
// ACCORDION FUNCTIONALITY
//////////////////////////////////////
console.log("FAQ script loaded");
document.addEventListener("DOMContentLoaded", function () {
  // Find all accordions on the page
  const accordions = document.querySelectorAll(".accordion");

  if (accordions.length) {
    accordions.forEach((accordion) => {
      const accordionItems = accordion.querySelectorAll(".accordion__item");
      const accordionHeaders = accordion.querySelectorAll(".accordion__header");

      // Set up click handlers for each header
      accordionHeaders.forEach((header) => {
        header.addEventListener("click", () => {
          const item = header.closest(".accordion__item");
          const content = header.nextElementSibling;
          const isActive = item.classList.contains("active");

          // If this is a single-open accordion (optional)
          const isSingleOpen = accordion.classList.contains(
            "accordion--single-open"
          );
          if (isSingleOpen) {
            accordionItems.forEach((otherItem) => {
              if (
                otherItem !== item &&
                otherItem.classList.contains("active")
              ) {
                otherItem.classList.remove("active");
                const otherContent = otherItem.querySelector(
                  ".accordion__content"
                );
                otherContent.style.maxHeight = null;
              }
            });
          }

          // Toggle active state
          if (isActive) {
            item.classList.remove("active");
            content.style.maxHeight = null;
          } else {
            item.classList.add("active");
            // Set exact height for smooth animation
            content.style.maxHeight = content.scrollHeight + "px";
          }
        });
      });

      // Initialize heights for any initially active items
      accordionItems.forEach((item) => {
        if (item.classList.contains("active")) {
          const content = item.querySelector(".accordion__content");
          content.style.maxHeight = content.scrollHeight + "px";
        }
      });

      // Handle window resize to adjust content heights
      window.addEventListener("resize", () => {
        accordionItems.forEach((item) => {
          if (item.classList.contains("active")) {
            const content = item.querySelector(".accordion__content");
            content.style.maxHeight = content.scrollHeight + "px";
          }
        });
      });
    });
  }
});
