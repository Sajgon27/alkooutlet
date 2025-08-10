<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <header class="header">
        <div class="header__top">
            <div class="container">
                <div class="header__top-content">
                    <p class="header__top-text">
                        <span><?php echo get_field('navbar_-_tekst', 'option'); ?></span>
                    </p>

                    <div class="header__top-actions">
                        <a href="<?php echo esc_url('https://www.facebook.com/'); ?>" class="header__top-social" aria-label="Facebook">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/fb.svg" alt="Facebook" width="20" height="20">
                            <?php _e('Odwiedź naszego facebooka!', 'alkooutlet'); ?>
                        </a>

                        <div class="header__language-switcher">
                            <?php //echo do_shortcode("[wpml_language_selector_widget]"); 
                            ?>
                            <?php
                            $languages = apply_filters('wpml_active_languages', NULL, array('skip_missing' => 0));

                            if (!empty($languages)) : ?>
                                <div class="custom-language-switcher">
                                    <div class="current-language">
                                        <?php
                                        foreach ($languages as $lang) {
                                            if ($lang['active']) {
                                                // Show flag + native name (or code)
                                                echo '<img src="' . esc_url($lang['country_flag_url']) . '" alt="' . esc_attr($lang['language_code']) . '" class="lang-flag" />';
                                                echo ' ' . esc_html(strtoupper($lang['language_code']));
                                                break;
                                            }
                                        }
                                        ?>
                                        <span class="arrow"><img src="<?php echo get_template_directory_uri(); ?>/assets/icons/chevron-down.svg" alt="Arrow" /></span>
                                    </div>
                                    <ul class="language-dropdown">
                                        <?php foreach ($languages as $lang) {
                                            if (!$lang['active']) {
                                                echo '<li><a href="' . esc_url($lang['url']) . '">';
                                                echo '<img src="' . esc_url($lang['country_flag_url']) . '" alt="' . esc_attr($lang['language_code']) . '" class="lang-flag" />';
                                                echo ' ' . esc_html(strtoupper($lang['language_code']));
                                                echo '</a></li>';
                                            }
                                        } ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                        </div>
                        <div class="header__currency-switcher">
                            <?php echo do_shortcode("[currency_switcher format='%code%']"); 
                            ?>
                            <?php
                            global $woocommerce_wpml;

                            if (isset($woocommerce_wpml->multi_currency)) {
                                $currencies = $woocommerce_wpml->multi_currency->get_currency_codes();
                                $current_currency = $woocommerce_wpml->multi_currency->get_client_currency();

                                if (! empty($currencies)) : ?>
                                    <div class="custom-currency-switcher">
                                        <div class="current-currency">
                                            <img class="currency-image" src="<?php echo get_template_directory_uri(); ?>/assets/icons/header/money.svg" alt="Money" />
                                            <?php echo esc_html($current_currency); ?>
                                            <span class="arrow"><img src="<?php echo get_template_directory_uri(); ?>/assets/icons/chevron-down.svg" alt="Arrow" /></span>
                                        </div>
                                        <ul class="currency-dropdown">
                                            <?php foreach ($currencies as $currency) :
                                                if ($currency !== $current_currency) : ?>
                                                    <li>
                                                        <a href="<?php echo esc_url(add_query_arg('wcmlc', $currency)); ?>">
                                                            <?php echo esc_html($currency); ?>
                                                        </a>
                                                    </li>
                                            <?php endif;
                                            endforeach; ?>
                                        </ul>
                                    </div>
                            <?php endif;
                            }
                            ?>


                        </div>

                    </div>

                </div>
            </div>
        </div>
        <div class="header__main">
            <div class="container">
                <div class="header__main-content">
                    <div class="header__logo">
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.webp" alt="<?php bloginfo('name'); ?>" width="140" height="60" />
                        </a>
                    </div>

                    <div class="header__search">
                        <?php echo do_shortcode('[fibosearch]'); ?>
                    </div>

                    <div class="header__actions">
                        <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="header__action header__action--account" aria-label="<?php esc_attr_e('My Account', 'alkooutlet'); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/header/user.svg" alt="Account" width="24" height="24" />
                        </a>
                        <a href="<?php echo esc_url(get_permalink(apply_filters('wpml_object_id', 382, 'page', false, 'pl'))); ?>" class="header__action header__action--wishlist" aria-label="<?php esc_attr_e('Wishlist', 'alkooutlet'); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/header/heart.svg" alt="Wishlist" width="24" height="24" />
                            <span class="wishlist-count"></span>
                        </a>
                        <?php if (is_cart() || is_checkout()) : ?>
                            <a href="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : '#'); ?>" class="header__action header__action--cart" aria-label="<?php esc_attr_e('Cart', 'alkooutlet'); ?>">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/header/cart.svg" alt="Cart" width="24" height="24" />
                            </a>
                        <?php else : ?>
                            <span class="header__action header__action--cart header__cart-btn-normal" aria-label="<?php esc_attr_e('Cart', 'alkooutlet'); ?>">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/header/cart.svg" alt="Cart" width="24" height="24" />
                            </span>
                        <?php endif; ?>

                    </div>
                    <button class="header__menu-toggle" aria-label="<?php esc_attr_e('Toggle menu', 'alkooutlet'); ?>" aria-expanded="false">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M3 6H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <nav class="header__nav">
            <div class="container">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary_desktop',
                    'menu_class' => 'header__nav-menu',
                    'container' => false,
                    'fallback_cb' => false,
                ));
                ?>

                <div class="header__nav-buttons">
                    <a class="button button--outline" href="#"><?php _e("Nowości", "alkooutlet") ?></a>
                    <a class="button button--primary" href="#"><?php _e("Promocje", "alkooutlet") ?></a>
                </div>
            </div>
        </nav>

        <div class="header__mobile-menu">
            <div class="header__mobile-menu-inner">
                <div class="header__mobile-menu-header">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="header__mobile-logo">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.webp" alt="Logo" width="120" height="40" />
                    </a>
                    <button class="header__mobile-menu-close" aria-label="<?php esc_attr_e('Close menu', 'alkooutlet'); ?>">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
                <div class="header__mobile-search">
                    <?php echo do_shortcode('[fibosearch]'); ?>
                </div>
                <div class="header__mobile-actions">
                    <a href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : '#'); ?>" class="header__mobile-action">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/header/user-white.svg" alt="Account" width="24" height="24" />
                    </a>
                    <a href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('wishlist') : '#'); ?>" class="header__mobile-action">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/header/heart-white.svg" alt="Wishlist" width="24" height="24" />
                    </a>
                    <a href="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : '#'); ?>" class="header__mobile-action">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/header/cart-white.svg" alt="Cart" width="24" height="24" />
                    </a>
                </div>
                <nav class="header__mobile-nav">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary_mobile',
                        'menu_class' => 'header__mobile-menu-list',
                        'container' => false,
                        'fallback_cb' => false,
                    ));
                    ?>
                </nav>
                <div class="header__mobile-bottom">
                    <div class="header__mobile-switchers">
                        <div class="header__mobile-language-switcher">
                            <span class="current-lang">PL</span>
                        </div>
                        <div class="header__mobile-currency-switcher">
                            <span class="current-currency">PLN</span>
                        </div>
                    </div>
                    <div class="header__mobile-social">
                        <a href="<?php echo esc_url('https://www.facebook.com/'); ?>" class="header__mobile-social-link" aria-label="Facebook">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/fb.svg" alt="Facebook" width="20" height="20">
                            <?php _e('Odwiedź naszego facebooka!', 'alkooutlet'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <?php if (! is_product() && !is_post_type_archive('product')) : ?>
        <main class="site-main">
        <?php endif; ?>