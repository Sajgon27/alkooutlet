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
                    <p class="header__top-text"><?php echo get_field('navbar_-_tekst', 'option'); ?></p>
                 
                    <div class="header__top-actions">
                        <a href="<?php echo esc_url('https://www.facebook.com/'); ?>" class="header__top-social" aria-label="Facebook">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/fb.svg" alt="Facebook" width="20" height="20">
                            <?php _e('Odwiedź naszego facebooka!', 'alkooutlet'); ?>
                        </a>
                        <div class="header__top-switchers">
                            <div class="header__language-switcher">
                                <?php echo do_shortcode("[wpml_language_selector_widget]"); ?>
                            </div>
                            <div class="header__currency-switcher">
                                <?php echo do_shortcode("[currency_switcher format='%code%']"); ?>
                            </div>
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
                        <a href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('wishlist') : '#'); ?>" class="header__action header__action--wishlist" aria-label="<?php esc_attr_e('Wishlist', 'alkooutlet'); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/header/heart.svg" alt="Wishlist" width="24" height="24" />
                        </a>
                        <a href="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : '#'); ?>" class="header__action header__action--cart" aria-label="<?php esc_attr_e('Cart', 'alkooutlet'); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/header/cart.svg" alt="Cart" width="24" height="24" />
                        </a>
                     
                    </div>
                       <button class="header__menu-toggle" aria-label="<?php esc_attr_e('Toggle menu', 'alkooutlet'); ?>" aria-expanded="false">
                            ≡
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
                        <a class="button button--outline" href="#"><?php _e( "Nowości", "alkooutlet") ?></a>
                        <a class="button button--primary" href="#"><?php _e( "Promocje", "alkooutlet") ?></a>
                </div>
            </div>
        </nav>

        <div class="header__mobile-menu">
            <div class="header__mobile-menu-inner">
                <div class="header__mobile-menu-header">
                    <button class="header__mobile-menu-close" aria-label="<?php esc_attr_e('Close menu', 'alkooutlet'); ?>">
                        <span class="header__mobile-menu-close-icon"></span>
                    </button>
                </div>
                <div class="header__mobile-search">
                    <?php echo do_shortcode('[fibosearch]'); ?>
                </div>
                <div class="header__mobile-actions">
                    <a href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : '#'); ?>" class="header__mobile-action">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/header/user.svg" alt="Account" width="24" height="24" />
                    </a>
                    <a href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('wishlist') : '#'); ?>" class="header__mobile-action">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/header/heart.svg" alt="Wishlist" width="24" height="24" />
                    </a>
                    <a href="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : '#'); ?>" class="header__mobile-action">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/header/cart.svg" alt="Cart" width="24" height="24" />
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
    <?php if (! is_product()) : ?>
        <main class="site-main">
        <?php endif; ?>