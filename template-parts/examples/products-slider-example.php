<?php
/**
 * Example usage of Products Slider
 * 
 * @package alkooutlet
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Example 1: Products by IDs with dark theme
$product_ids = [53, 56, 57, 179, 180, 181]; // Replace with actual product IDs
set_query_var('icon', get_template_directory_uri() . '/assets/icons/wine-glass.svg'); // Replace with actual icon
set_query_var('small_text', 'Świeże propozycje');
set_query_var('title', 'NOWE DOZNANIA SMAKOWE I EDYCJE SPECJALNE');
set_query_var('description', 'Limitowane edycje i nowe smaki stworzone z myślą o tych, którzy szukają czegoś więcej. Wyjątkowe połączenia i niezapomniane aromaty, które zmieniają codzienność w chwilę przyjemności.');
set_query_var('product_ids', $product_ids);
set_query_var('theme', 'light'); // Options: 'light', 'dark'
get_template_part('template-parts/components/products.slider');
reset_query_vars();

// Example 2: Products by category with light theme
set_query_var('icon', get_template_directory_uri() . '/assets/icons/wine-bottle.svg'); // Replace with actual icon
set_query_var('small_text', 'Okazje z charakterem');
set_query_var('title', 'OKAZJE NA BUTELKI Z CHARAKTEREM');
set_query_var('description', 'Limitowane okazje dla tych, którzy cenią smak i oszczędność. Wybierz wyjątkowe propozycje w atrakcyjnych cenach dostępne przez ograniczony czas.');
set_query_var('category_id', 16); // Replace with actual category ID
set_query_var('theme', 'dark');
get_template_part('template-parts/components/products.slider');
reset_query_vars();

/**
 * Helper function to reset query vars after using set_query_var
 */
function reset_query_vars() {
    set_query_var('icon', '');
    set_query_var('small_text', '');
    set_query_var('title', '');
    set_query_var('description', '');
    set_query_var('product_ids', []);
    set_query_var('category_id', 0);
    set_query_var('theme', 'light');
    set_query_var('max_products', 6);
}
?>
