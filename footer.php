<?php if (!is_product() && !is_post_type_archive('product')) : ?>
    </main>
<?php endif; ?>

<footer class="footer">
    <div class="container">
        <div class="footer__main">
            <div class="footer__logo">
                <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo2.webp" alt="<?php bloginfo('name'); ?>" width="160" height="120">
                </a>
                <div class="footer__description">
                    <p>Jesteśmy zespołem, który połączyła pasja do wyjątkowych alkoholi. Wierzymy, że każda butelka to nie tylko smak, ale też historia i emocje. W naszej ofercie znajdziesz starannie wyselekcjonowane trunki.</p>
                </div>
                <div class="footer__social">
                    <a href="<?php echo esc_url('https://www.facebook.com/'); ?>" class="footer__social-link" aria-label="Facebook">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/fb-red.svg" alt="Facebook" width="20" height="20">
                        <span><?php _e('Odwiedź naszego facebooka!', 'alkooutlet'); ?></span>
                    </a>
                </div>
            </div>
            
            <div class="footer__menus">
                <div class="footer__menu-section">
                    <h6 class="footer__menu-title"><?php _e('MAPA STRONY', 'alkooutlet'); ?></h6>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer1',
                        'menu_class' => 'footer__menu-list',
                        'container' => false,
                        'fallback_cb' => false,
                    ));
                    ?>
                </div>
                
                <div class="footer__menu-section">
                    <h6 class="footer__menu-title"><?php _e('INFORMACJE ZAKUPOWE', 'alkooutlet'); ?></h6>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer2',
                        'menu_class' => 'footer__menu-list',
                        'container' => false,
                        'fallback_cb' => false,
                    ));
                    ?>
                </div>
                
                <div class="footer__menu-section">
                    <h6 class="footer__menu-title"><?php _e('POTRZEBUJESZ POMOCY?', 'alkooutlet'); ?></h6>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer3',
                        'menu_class' => 'footer__menu-list',
                        'container' => false,
                        'fallback_cb' => false,
                    ));
                    ?>
                </div>
            </div>
        </div>
        
        <div class="footer__bottom">
            <div class="footer__copyright">
                <p>&copy; <?php echo date('Y'); ?> Alko Outlet. Wszelkie prawa zastrzeżone.</p>
            </div>
            <div class="footer__legal">
                <a href="<?php echo esc_url(home_url('/polityka-prywatnosci')); ?>"><?php _e('Polityka Prywatności', 'alkooutlet'); ?></a>
                <span class="footer__legal-separator">•</span>
                <a href="<?php echo esc_url(home_url('/warunki-korzystania')); ?>"><?php _e('Warunki korzystania', 'alkooutlet'); ?></a>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>