<?php
/**
 * Example usage of Products Slider with the new layout
 * 
 * @package alkooutlet
 */

// For light theme slider
set_query_var('icon', get_template_directory_uri() . '/assets/icons/wine-glass.svg');
set_query_var('small_text', 'Świeże propozycje');
set_query_var('title', 'NOWE DOZNANIA SMAKOWE I EDYCJE SPECJALNE');
set_query_var('description', 'Limitowane edycje i nowe smaki stworzone z myślą o tych, którzy szukają czegoś więcej. Wyjątkowe połączenia i niezapomniane aromaty, które zmieniają codzienność w chwilę przyjemności.');
set_query_var('product_ids', [123, 124, 125, 126, 127, 128]); // Replace with actual product IDs
set_query_var('theme', 'light');
get_template_part('template-parts/components/products.slider');

// For dark theme slider (shown lower on the page)
set_query_var('icon', get_template_directory_uri() . '/assets/icons/bottle.svg');
set_query_var('small_text', 'Okazje z charakterem');
set_query_var('title', 'OKAZJE NA BUTELKI Z CHARAKTEREM');
set_query_var('description', 'Limitowane okazje dla tych, którzy cenią smak i oszczędność. Wybierz wyjątkowe propozycje w atrakcyjnych cenach dostępne przez ograniczony czas.');
set_query_var('category_id', 15); // Replace with actual category ID
set_query_var('theme', 'dark');
get_template_part('template-parts/components/products.slider');
?>
